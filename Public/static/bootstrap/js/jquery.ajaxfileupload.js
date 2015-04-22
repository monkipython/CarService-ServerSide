$(function(){
	// Variable to store your files
	var files;

	// Add events
	$('.select-image').on('change', prepareUpload);

	// Grab the files and set them to our variable
	function prepareUpload(event)
	{
		files = event.target.files;
		uploadFiles(event);
	}

	// Catch the form submit and upload the files
	function uploadFiles(event)
	{
		console.log('uploadfile');
		event.stopPropagation(); // Stop stuff happening
        event.preventDefault(); // Totally stop stuff happening


        // Create a formdata object and add the files
		var data = new FormData();
		$.each(files, function(key, value)
		{
			data.append(key, value);
		});
        
        $.ajax({
            url: 'http://121.40.92.53/ycbb/index.php/App/Chat/uploadPic',
            type: 'POST',
            data: data,
            cache: false,
            dataType: 'json',
            processData: false, // Don't process the files
            contentType: false, // Set content type to false as jQuery will tell the server its a query string request
            success: function(data, textStatus, jqXHR)
            {
            	console.log(data);
            	if(typeof data.error === 'undefined')
            	{
            		// Success so call function to process the form
            		submitForm(event, data);
            	}
            	else
            	{
            		// Handle errors here
            		console.log('ERRORS: ' + data.error);
            	}
            },
            error: function(jqXHR, textStatus, errorThrown)
            {
            	// Handle errors here
            	console.log('ERRORS: ' + textStatus);
            	// STOP LOADING SPINNER
            }
        });
    }
    
    /*
function submitForm(event, data)
	{
		console.log('submit form');
		// Create a jQuery object from the form
		$form = $(event.target);
		
		// Serialize the form data
		var formData = $form.serialize();
		
		// You should sterilise the file names
		$.each(data.files, function(key, value)
		{
			formData = formData + '&filenames[]=' + value;
		});

		$.ajax({
			url: 'http://121.40.92.53/ycbb/index.php/App/Chat/uploadPic',
            type: 'POST',
            data: formData,
            cache: false,
            dataType: 'json',
            success: function(data, textStatus, jqXHR)
            {
            	if(typeof data.error === 'undefined')
            	{
            		// Success so call function to process the form
            		console.log('SUCCESS: ' + data.success);
            	}
            	else
            	{
            		// Handle errors here
            		console.log('ERRORS: ' + data.error);
            	}
            },
            error: function(jqXHR, textStatus, errorThrown)
            {
            	// Handle errors here
            	console.log('ERRORS: ' + textStatus);
            },
            complete: function()
            {
            	// STOP LOADING SPINNER
            }
		});
	}
*/
	return false;
});