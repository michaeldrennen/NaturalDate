<?php
namespace MichaelDrennen\NaturalDate\Exceptions;

class NaturalDateException extends \Exception {

    public $debugMessages = [];

    public function __construct( $message = "", $code = 0, \Throwable $previous = null, array $debugMessages = [] ) {
        parent::__construct( $message, $code, $previous );
        $this->debugMessages = $debugMessages;
    }
}