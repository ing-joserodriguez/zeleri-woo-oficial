<?php
if ( ! class_exists( 'Zeleri_Pay_Signature' ) ) {

    class Zeleri_Pay_Signature {
        private $secret;

        public function __construct($secret) {
            $this->secret = $secret;
        }

        public function generate($input) {
            $object = zeleri_parseInput($input);
            $sortedObject = zeleri_sortObjectKeys($object);
            $message = zeleri_concatenateObjectProperties($sortedObject);

            $hmac = hash_hmac('sha256', $message, $this->secret);
            return $hmac;
        }

        public function validate($data, $signature) {
            $object = zeleri_parseInput($data);
            $sortedObject = zeleri_sortObjectKeys($object);
            $message = zeleri_concatenateObjectProperties($sortedObject);

            $hmac = hash_hmac('sha256', $message, $this->secret);
            $expectedSignature = $hmac;

            return $signature === $expectedSignature;
        }
    }

    function zeleri_sortObjectKeys($object) {
        if (!is_array($object) || empty($object)) {
            return $object;
        }

        $sortedKeys = array_keys($object);
        sort($sortedKeys);

        $sortedObject = [];
        foreach ($sortedKeys as $key) {
            $sortedObject[$key] = $object[$key];
        }

        return $sortedObject;
    }

    function zeleri_concatenateObjectProperties($object) {
        $message = '';
        foreach ($object as $key => $value) {
            if ($key === 'signature') continue;
            $message .= $key . wp_json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }
        return $message;
    }

    function zeleri_parseInput($input) {
        if (is_array($input)) {
            return $input;
        } elseif (is_string($input)) {
            return parse_str($input, $output) ? $output : [];
        } else {
            throw new Exception("Invalid input type. Expected object or string.");
        }
    }

}
