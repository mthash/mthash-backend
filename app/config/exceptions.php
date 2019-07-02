<?php
class MtHashException extends Exception {}
class BusinessLogicException extends Exception {}
class ValidationException extends BusinessLogicException {}
class TokenException extends BusinessLogicException {}