var JTalk = new (function() {
    // to be used inside constructors and helper functions
    var jtalk = this;

    // namespace for chat states
    Strophe.NS.CHATSTATE = "http://jabber.org/protocol/chatstates";

    /* A simple decorator for functions to be called (w)ith (c)ommon
     * (a)ttributes, that is, with pre-parsed common XMPP stanza attributes.
     * The decorated function receives the attributes in object notation.
     */
    function wca(f) {
        function _f(stanza) {
            var common_attrs = ["to", "from", "id", "type", "xml:lang"];

            var attrs = {}
            for (i = 0; i < common_attrs.length; i++) {
                var a = common_attrs[i];
                attrs[a] = $(stanza).attr(a);
            }

            return f(stanza, attrs);
        }

        return _f;
    }

    /* The Events object */
    this.Events = new (function() {
        var events = {};

        this.addHandler = function(ev, handler, priority) {
            if (!events[ev]) {
                events[ev] = [];
            }

            if (!priority) priority = 0;

            for (i = 0; i < events[ev].length; i++) {
                if (events[ev][i].priority > priority) break;
            }

            events[ev].splice(i, 0, {handler: handler, priority: priority});
        }

        this._trigger = function(ev) {
            var args = Array.prototype.slice.call(arguments, 1);

            if (!events[ev])
                return null;

            var result = null;
            for (i = 0; i < events[ev].length; i++) {
                var ret = events[ev][i].handler.apply(null, args);

                if (ret === null) {
                    events[ev].splice(i--, 1);
                } else if (result !== null) {
                    result = ret;
                }
            }

            return result;
        }
    })();

    // convenience alias
    var trigger = this.Events._trigger;

    /* Contact constructor */
    function Contact(jid, name, group, subscription) {
        this._update = function(jid, name, group, subscription) {
            this.jid = jid ? Strophe.getBareJidFromJid(jid) : this.jid;
            this.name = name ? name : null;
            this.group = group ? group : null;

            // normalize subscription to the four known values
            var rfc_3921_subscription_values = [
                "to",
                "from",
                "both",
                "none",
            ]

            if (rfc_3921_subscription_values.indexOf(subscription)) {
                this.subscription = subscription;
            } else if (!subscription) {
                this.subscription = "none";
            }

            this.presence = null;
        }

        var self = this;

        function _setPresenceAttr(name, value) {
            if (!this.presence) {
                self.presence = {};
            }

            if (value) {
                self.presence[name] = value;
            } else {
                delete this.presence[name];
                if ($.isEmptyObject(this.presence)) {
                    self.presence = null;
                }
            }
        }

        this._setShow = function(show) {
            _setPresenceAttr("show", show);
        }

        this._setStatus = function(status) {
            _setPresenceAttr("status", status);
        }

        // require at least jid when creating
        if (!jid) return null;
        this._update(jid, name, group, subscription);
    }

    /* The Roster object. Created after connection. */
    this.Roster = null;

    /* Constructor for the Roster object.
     * Requires a working connection.
     */
    function Roster() {
        var roster = {};

        this.get = function() {
            if (arguments.length == 0) {
                return roster;
            }

            var jid = Strophe.getBareJidFromJid(arguments[0]);
            var contact = roster[jid];
			
            return contact ? contact : null;
        }

        this.add = function(jid, name, group) {
            // check whether contact already exists
            jid = Strophe.getBareJidFromJid(jid);
            if (roster[jid]) {
                return roster[jid];
            }

            var contact = new Contact(jid, name, group);
            roster[jid] = contact;

            // add contact to the roster
            var iq = $riq("set", connection.getUniqueId("roster_add"));
            iq.c("item", {jid: jid});

            if (name) iq.attrs({name: name});
            if (group) iq.c("group", group);

            connection.send(iq);

            // request subscription to the contact's presence
            var pres = $pres({
                to: jid,
                type: "subscribe"
            });

            connection.send(pres);

            return contact;
        }

        this.remove = function(contact) {
            // make sure the contact is in the roster
            if (!roster[contact.jid]) {
                return;
            }

            var iq = $riq("set", connection.getUniqueId("roster_remove"));
            iq.c("item", {jid: contact.jid, subscription: "remove"});

            connection.send(iq);
        }

        /* Builds and returns an iq for roster management.
         * The returned element is of the form:
         *
         * <iq from type id>
         *  <query xmlns='jabber:iq:roster'>
         *  </query>
         * </iq>
         *
         * The current node of the returned iq object is the query element.
         */
        function $riq(type, id) {
            var iq = $iq({
                from: jtalk.me.jid,
                type: type,
                id: id
            });

            iq.c("query", {xmlns: Strophe.NS.ROSTER});
            return iq;
        }

        /* Handler for roster-related events */
        var onRosterEvent = wca(
            function(iq, attrs) {
                // selector for roster items
                var selector = [
                    "query[xmlns=",
                    Strophe.NS.ROSTER,
                    "] > item",
                ].join("");

                $(iq).find(selector).each(
                    function() {
                        var jid = $(this).attr("jid");
                        jid = Strophe.getBareJidFromJid(jid);

                        var name = $(this).attr("name");
                        var group = $(this).attr("group");
                        var subs = $(this).attr("subscription");

                        if (subs == "remove" && roster[jid]) {
                            trigger("contact removed", roster[jid]);
                            delete roster[jid];
                        } else {
                            var event = null;

                            if (roster[jid]) {
                                roster[jid]._update(jid, name, group, subs);
                                event = "contact changed";
                            } else {
                                roster[jid] = new Contact(jid, name, group, subs);
                                event = "new contact";
                            }

                            trigger(event, roster[jid]);
                        }
                    });

                if (attrs.type == "get" || attrs.type == "set") {
                    // send response iq to the server
                    var reply = $iq({
                        to: _server,
                        from: attrs.to,
                        type: "result",
                        id: attrs.id
                    });

                    connection.send(reply);
                }

                return true;
            }
        )

        var self = this;

        // initial roster request
        // use the callback manually for this first request to ensure
        // we can use get() when triggering "roster received".
        var request = $riq("get", connection.getUniqueId("roster_request"));
        connection.sendIQ(request,
            function(iq) {
                onRosterEvent(iq);
                trigger("roster received", self.get());
				
                // let the callback handle further events
                connection.addHandler(onRosterEvent, Strophe.NS.ROSTER, "iq");
        });
    }

    this.Chat = function(contact) {
        if (_active_chats[contact.jid]) {
            return _active_chats[contact.jid];
        }

        return new (function () {
            var self = this;
            var _history = [];
            var _chatstates = {}; // jid -> chatstate

            /* Send a message with chatstate support */
            function _send(message, chatstate) {
                var elem = $msg({
                    to: contact.jid,
                    from: jtalk.me.jid,
                    type: "chat"
                });

                if (message) elem.c("body", message);
                if (chatstate) {
                    elem.c(chatstate, {xmlns: Strophe.NS.CHATSTATE});
                }

                connection.send(elem);
            }

            /* Internal function for receiving chat messages.
             * Expects the message in object notation.
             */
            this._recv = function(message) {
                if (message.text) {
                    var obj = {from: contact, message: message.text};
                    var keep = trigger("incoming message", self, obj);

                    if (keep !== false) {
                        self.history(message.from.jid, message.text);
                    }
                }

                if (message.chatstate) {
                    _chatstates[message.from.jid] = message.chatstate;
                    trigger("incoming chat state",
                            self,
                            message.from,
                            message.chatstate);
                }
            }

            this.send = function(message) {
                _send(message, "active");

                var keep = trigger("outgoing message",
                        self,
                        {from: jtalk.me, message: message});

                if (keep !== false) {
                    self.history(jtalk.me.jid, message);
                }
            }

            this.state = function(obj) {
                // try treating obj as a Contact
                if (obj.jid) {
                    var chatstate = _chatstates[obj.jid];
                    return chatstate ? chatstate : null;
                }

                // assume string
                var xep_0085_states = [
                    "active",
                    "inactive",
                    "gone",
                    "composing",
                    "paused"
                ];

                if (xep_0085_states.indexOf(obj) >= 0) {
                    _send(null, obj);
                }
            }

            this.history = function() {
                if (arguments.length == 0) {
                    return _history;
                }

                var from = arguments[0];
                var message = arguments[1];

                _history.push({from: from, message: message});
            }

            _active_chats[contact.jid] = this;
        })();
    }

    var _user = null;
    var _server = null;
    var connection = null;
    var _active_chats = {};

    this.me = null;

    var onMessage = wca(
        function(message, attrs) {
            // parse message attributes and add them to 'attrs'
            var body = $(message).find("body:first");
            var jid = Strophe.getBareJidFromJid(attrs.from);

            attrs.text = null;
            if (body.length != 0) {
                attrs.text = body.text();
            }

            // select the tag corresponding to the chat state
            var selector = [
                "*[xmlns='",
                Strophe.NS.CHATSTATE,
                "']"
            ].join("");

            attrs.chatstate = null;
            var tag = $(message).find(selector);
            if (tag.length != 0) {
                attrs.chatstate = tag.prop("tagName");
            }

            attrs.from = jtalk.Roster.get(jid);
            if (attrs.from) {
                if (!_active_chats[jid]) {
                    var accept = trigger("chat requested", attrs.from);
                    if (accept === false) return;
                }

                jtalk.Chat(attrs.from)._recv(attrs);
            }

            return true;
        }
    );

    var onPresence = wca(
        function(presence, attrs) {
            var jid = Strophe.getBareJidFromJid(attrs.from);
            var contact = jtalk.Roster.get(jid);

            if (!contact) return true;

            switch (attrs.type) {
                case "unavailable":
                    trigger("contact unavailable", contact);
                    break;

                case "subscribe":
                    var type = "unsubscribed";

                    if (trigger("incoming subscription", contact) === true) {
                        type = "subscribed";
                    }

                    connection.send($pres({to: jid, type: type}));
                    break;

                case "unsubscribed":
                    trigger("subscription denied", contact);
                    break;

                case "probe":
                case "error":
                case "subscribed":
                    return true;
            }

            var show = $(presence).find("show").text();
            if (show) contact._setShow(show);

            var status = $(presence).find("status").text();
            if (status) contact._setStatus(status);

            if (show || status) {
                trigger("contact changed", contact);
            } else if (!attrs.type) {
                // no show, status or type, assume it's initial presence
                trigger("contact available", contact);
            }

            return true;
        }
    );

    function onConnect(status) {
    	if (status == Strophe.Status.CONNECTED) {
	    	console.log('Strophe is connected.');
            jtalk.me = new Contact(_user);
            jtalk.Roster = new Roster();
            jtalk.Roster.get();
            // send initial presence
            connection.send($pres());
			
/* 			_getRoster(); */
            // register handlers
            connection.addHandler(onMessage, null, "message", "chat");
            connection.addHandler(onPresence, null, "presence");

            trigger("connected");
            
        }

        return true;
    }
    
	function your_roster_callback_function(iq){
	  $(iq).find('item').each(function(){
	    var jid = $(this).attr('jid'); //你的联系人
	    var fid = jid.split("@")[0];
		var userStatus = new Contact(jid);
		console.log(userStatus);
	    $('.chat_body').append("<div class='user offline' onclick='javascript:chooseRoster(this)'> "+fid+" </div>");
	  });
	}
	
	/*
function _getRoster(){
		connection.sendIQ($iq({type: 'get'}).c('query', {xmlns: 'jabber:iq:roster'}), your_roster_callback_function);
	}
	
    function onDisconnect(status) {
        if (status == Strophe.Status.DISCONNECTED) {
            _user = null;
            _server = null;
            connection = null;
            _active_chats = {};

            jtalk.me = null;
        }

        return true;
    }
*/

    this.connect = function(server, user, password, callback) {
        _user = user;
        _server = server;

        connection = new Strophe.Connection(server);
        connection.connect(user, password, onConnect);
    }

    this.disconnect = function() {
        connection.disconnect(onDisconnect);
    }

    this.show = function(show) {
        var rfc_3921_show_values = [
            "away",
            "chat",
            "dnd",
            "xa"
        ];

        if (rfc_3921_show_values.indexOf(show) >= 0) {
            connection.send($pres().c("show", show));
            this.me._setShow(show);
        }
    }

    this.status = function(status) {
        connection.send($pres().c("status", status));
        this.me._setStatus(status);
    }
})();
