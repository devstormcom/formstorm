<?php
namespace devStorm\Library\BBCode;
use Golonka\BBCode\BBCodeParser;
require_once APP_PATH.'/vendor/JBBCode/Parser.php';
/**
 * BBCode parser
 *
 * @author Flavio Kleiber <flavio.kleiber@gentleman-informatik.ch>
 * @copyright (c) 2014 Flavio Kleiber, Gentleman Informatik
 * @package devstorm.library.bbcode
 */

class Parser{

    public function __construct() {
        $this->parser = new \JBBCode\Parser();
        $this->parser->addCodeDefinitionSet(new \JBBCode\DefaultCodeDefinitionSet());
    }

    public function parse($string) {
        $this->parser->parse($string);
        $html = $this->parser->getAsHtml();
        return $html;
    }
}
?>