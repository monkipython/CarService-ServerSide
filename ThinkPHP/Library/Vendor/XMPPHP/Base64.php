<?php

class Base64 {

    private $_alpha = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/';
    private $_PADCHAR = '=';
    private function _alpha_gender($key=''){
        if(strlen($key) == 64){
            $this->_alpha = $key;
        }
    }


    private function _getbyte64($str, $i){
        $idx = strpos($this->_alpha , $str[$i]);
        if ( $idx === -1 ) {
            trigger_error("Cannot decode base64",E_USER_ERROR);
        }

        return $idx;
    }

    private function  _getbyte( $s, $i ) {
        $x = ord($s[$i]);
        if ( $x > 255 ) {
            trigger_error("INVALID_CHARACTER_ERR: DOM Exception 5",E_USER_ERROR);
        }
        return $x;
    }

    public function encode($s='',$key = false ) {

        if($key && strlen($key) == 64){
            $this->_alpha_gender($key);
        }

        $s = (string)$s;
        $x = array();
        $imax = strlen($s) - strlen($s) % 3;
        $b10 =0;

        if ( strlen($s) === 0 ) {
          return $s;
        }

        for ( $i = 0; $i < $imax; $i += 3 ) {
          $b10 = ( $this->_getbyte( $s, $i ) << 16 ) | ( $this->_getbyte( $s, $i + 1 ) << 8 ) | $this->_getbyte( $s, $i + 2 );
          $x[] = ( $this->_alpha[( $b10 >> 18 )] );
          $x[] = ( $this->_alpha[( ( $b10 >> 12 ) & 0x3F )] );
          $x[] = ( $this->_alpha[( ( $b10 >> 6 ) & 0x3f )] );
          $x[] = ( $this->_alpha[( $b10 & 0x3f )] );
        }

        switch ( strlen($s) - $imax ) {
          case 1:
            $b10 = $this->_getbyte( $s, $i ) << 16;
            $x[] = ( $this->_alpha[( $b10 >> 18 )] .$this->_alpha[( ( $b10 >> 12 ) & 0x3F )] . $this->_PADCHAR . $this->_PADCHAR );
            break;

          case 2:
            $b10 = ( $this->_getbyte( $s, $i ) << 16 ) | ( $this->_getbyte( $s, $i + 1 ) << 8 );
            $x[] = ( $this->_alpha[( $b10 >> 18 )] . $this->_alpha[( ( $b10 >> 12 ) & 0x3F )] . $this->_alpha[( ( $b10 >> 6 ) & 0x3f )] . $this->_PADCHAR );
            break;
        }

        return implode('', $x);
  }

    public function decode ($s='', $key = false){

        if($key && strlen($key) == 64){
            $this->_alpha_gender($key);
        }

        $s = (string)$s;
        $pads = 0;
        $imax = strlen($s);
        $x = array();
        $b10 = 0;

        if ( $imax === 0 ) {
          return $s;
        }

        if ( $imax % 4 !== 0 ) {
          trigger_error("Cannot decode base64",E_USER_ERROR);
        }

        if ( $s[$imax - 1 ] === $this->_PADCHAR ) {
          $pads = 1;

          if ( $s[$imax - 2] === $this->_PADCHAR ) {
            $pads = 2;
          }

          // either way, we want to ignore this last block
          $imax -= 4;
        }

        for ( $i = 0; $i < $imax; $i += 4 ) {
          $b10 = ( $this->_getbyte64( $s, $i ) << 18 ) | ( $this->_getbyte64( $s, $i + 1 ) << 12 ) | ($this->_getbyte64( $s, $i + 2 ) << 6 ) | $this->_getbyte64( $s, $i + 3 );
          $x[] = ( chr( $b10 >> 16) . chr( ( $b10 >> 8 ) & 0xff ) . chr($b10 & 0xff ) );
        }

        switch ( $pads ) {
          case 1:
            $b10 = ( $this->_getbyte64( $s, $i ) << 18 ) | ( $this->_getbyte64( $s, $i + 1 ) << 12 ) | ( $this->_getbyte64( $s, $i + 2 ) << 6 );
            $x[] = ( chr( $b10 >> 16 )  . chr (( $b10 >> 8 ) & 0xff ) );
            break;

          case 2:
            $b10 = ( $this->_getbyte64( $s, $i ) << 18) | ( $this->_getbyte64( $s, $i + 1 ) << 12 );
            $x[] = ( chr( $b10 >> 16 ) );
            break;
        }

        return implode('', $x);
    }

}
