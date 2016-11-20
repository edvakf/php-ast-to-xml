<?php

namespace astxml;

class ASTXMLTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider ast2xmlProvider
     */
    public function test_ast2xml($input, $expected)
    {
        $ast = \ast\parse_code($input, /*version*/ 35);
        $actual = ASTXML::ast2xml($ast);
        $this->assertEquals($expected, $this->prettyXML($actual));
    }

    public function ast2xmlProvider()
    {
        return [
            [
            '<?php $a = 1;', <<<XML
<?xml version="1.0"?>
<ast>
  <AST_STMT_LIST lineno="1">
    <AST_ASSIGN lineno="1">
      <var>
        <AST_VAR lineno="1">
          <name>
            <scalar value="a" type="string"/>
          </name>
        </AST_VAR>
      </var>
      <expr>
        <scalar value="1" type="int"/>
      </expr>
    </AST_ASSIGN>
  </AST_STMT_LIST>
</ast>

XML
            ],
            [
            '<?php $a = "1";', <<<XML
<?xml version="1.0"?>
<ast>
  <AST_STMT_LIST lineno="1">
    <AST_ASSIGN lineno="1">
      <var>
        <AST_VAR lineno="1">
          <name>
            <scalar value="a" type="string"/>
          </name>
        </AST_VAR>
      </var>
      <expr>
        <scalar value="1" type="string"/>
      </expr>
    </AST_ASSIGN>
  </AST_STMT_LIST>
</ast>

XML
            ],
            [
            '<?php $a = null;', <<<XML
<?xml version="1.0"?>
<ast>
  <AST_STMT_LIST lineno="1">
    <AST_ASSIGN lineno="1">
      <var>
        <AST_VAR lineno="1">
          <name>
            <scalar value="a" type="string"/>
          </name>
        </AST_VAR>
      </var>
      <expr>
        <AST_CONST lineno="1">
          <name>
            <AST_NAME lineno="1" NAME_NOT_FQ="NAME_NOT_FQ">
              <name>
                <scalar value="null" type="string"/>
              </name>
            </AST_NAME>
          </name>
        </AST_CONST>
      </expr>
    </AST_ASSIGN>
  </AST_STMT_LIST>
</ast>

XML
            ],
            [
            '<?php [1, "2"];', <<<XML
<?xml version="1.0"?>
<ast>
  <AST_STMT_LIST lineno="1">
    <AST_ARRAY lineno="1">
      <AST_ARRAY_ELEM lineno="1">
        <value>
          <scalar value="1" type="int"/>
        </value>
        <key/>
      </AST_ARRAY_ELEM>
      <AST_ARRAY_ELEM lineno="1">
        <value>
          <scalar value="2" type="string"/>
        </value>
        <key/>
      </AST_ARRAY_ELEM>
    </AST_ARRAY>
  </AST_STMT_LIST>
</ast>

XML
            ],
            [
            '<?php ["a" => "2"];', <<<XML
<?xml version="1.0"?>
<ast>
  <AST_STMT_LIST lineno="1">
    <AST_ARRAY lineno="1">
      <AST_ARRAY_ELEM lineno="1">
        <value>
          <scalar value="2" type="string"/>
        </value>
        <key>
          <scalar value="a" type="string"/>
        </key>
      </AST_ARRAY_ELEM>
    </AST_ARRAY>
  </AST_STMT_LIST>
</ast>

XML
            ],
            [
            '<?php $a = [&$a];', <<<XML
<?xml version="1.0"?>
<ast>
  <AST_STMT_LIST lineno="1">
    <AST_ASSIGN lineno="1">
      <var>
        <AST_VAR lineno="1">
          <name>
            <scalar value="a" type="string"/>
          </name>
        </AST_VAR>
      </var>
      <expr>
        <AST_ARRAY lineno="1">
          <AST_ARRAY_ELEM lineno="1" BY_REFERENCE="BY_REFERENCE">
            <value>
              <AST_VAR lineno="1">
                <name>
                  <scalar value="a" type="string"/>
                </name>
              </AST_VAR>
            </value>
            <key/>
          </AST_ARRAY_ELEM>
        </AST_ARRAY>
      </expr>
    </AST_ASSIGN>
  </AST_STMT_LIST>
</ast>

XML
            ],
            [
            '<?php function hoge($a) {return $a + 1;}', <<<XML
<?xml version="1.0"?>
<ast>
  <AST_STMT_LIST lineno="1">
    <AST_FUNC_DECL lineno="1" name="hoge" endLineno="1" docComment="">
      <params>
        <AST_PARAM_LIST lineno="1">
          <AST_PARAM lineno="1">
            <type/>
            <name>
              <scalar value="a" type="string"/>
            </name>
            <default/>
          </AST_PARAM>
        </AST_PARAM_LIST>
      </params>
      <uses/>
      <stmts>
        <AST_STMT_LIST lineno="1">
          <AST_RETURN lineno="1">
            <expr>
              <AST_BINARY_OP lineno="1" BINARY_ADD="BINARY_ADD">
                <left>
                  <AST_VAR lineno="1">
                    <name>
                      <scalar value="a" type="string"/>
                    </name>
                  </AST_VAR>
                </left>
                <right>
                  <scalar value="1" type="int"/>
                </right>
              </AST_BINARY_OP>
            </expr>
          </AST_RETURN>
        </AST_STMT_LIST>
      </stmts>
      <returnType/>
    </AST_FUNC_DECL>
  </AST_STMT_LIST>
</ast>

XML
            ],
            [
            '<?php class A{ private $a = 1; public function __construct(){} }', <<<XML
<?xml version="1.0"?>
<ast>
  <AST_STMT_LIST lineno="1">
    <AST_CLASS lineno="1" name="A" endLineno="1" docComment="">
      <extends/>
      <implements/>
      <stmts>
        <AST_STMT_LIST lineno="1">
          <AST_PROP_DECL lineno="1" MODIFIER_PRIVATE="MODIFIER_PRIVATE">
            <AST_PROP_ELEM lineno="1">
              <name>
                <scalar value="a" type="string"/>
              </name>
              <default>
                <scalar value="1" type="int"/>
              </default>
            </AST_PROP_ELEM>
          </AST_PROP_DECL>
          <AST_METHOD lineno="1" MODIFIER_PUBLIC="MODIFIER_PUBLIC" name="__construct" endLineno="1" docComment="">
            <params>
              <AST_PARAM_LIST lineno="1"/>
            </params>
            <uses/>
            <stmts>
              <AST_STMT_LIST lineno="1"/>
            </stmts>
            <returnType/>
          </AST_METHOD>
        </AST_STMT_LIST>
      </stmts>
    </AST_CLASS>
  </AST_STMT_LIST>
</ast>

XML
            ],
        ];
    }

    private function prettyXML(\SimpleXMLElement $xml): string
    {
        // http://stackoverflow.com/questions/8615422/php-xml-how-to-output-nice-format
        $domxml = new \DOMDocument('1.0');
        $domxml->preserveWhiteSpace = true;
        $domxml->formatOutput = true;
        $domxml->loadXML($xml->asXML());
        return $domxml->saveXML();
    }

    /**
     * @dataProvider xpathProvider
     */
    public function test_xpath($code, $xpath)
    {
        $ast = \ast\parse_code($code, /*version*/ 35);
        $xml = ASTXML::ast2xml($ast);
        //var_dump($this->prettyXML($xml));
        $results = $xml->xpath($xpath);
        $this->assertTrue(isset($results[0]));
        //var_dump($results[0]);
    }

    public function xpathProvider()
    {
        return [
            ['<?php if ($a = 1) {}', '//AST_IF//AST_ASSIGN'], // assignment within if is not prefered
            ['<?php in_array("", [1]);', '//AST_CALL[./expr/AST_NAME/name/scalar[@value="in_array"] and count(./args/AST_ARG_LIST/*) != 3]'], // in_array without the third argument is a bad practice
            ['<?php function a($a) {return $a + 1;}', '//AST_PARAM[./type[not(node())]]'], // param type is not provided
            ['<?php function a($a) {return $a + 1;}', '//AST_FUNC_DECL[./returnType[not(node())]]'], // param type is not provided
        ];
    }
}
