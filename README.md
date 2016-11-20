# php-ast-to-xml

Converts the AST obained by nikic/php-ast to an XML.

It enables you to query a particular structure in a PHP source code by XPath.

## Requirements

* https://github.com/nikic/php-ast

## How to use

```
$code = '<?php $a = 1;';

$ast = \ast\parse_code($input, /*version*/ 35);
$xml = \ASTXML::ast2xml($ast);
echo $xml;

//<?xml version="1.0"?>
//<ast><AST_STMT_LIST lineno="1"><AST_ASSIGN lineno="1"><var><AST_VAR lineno="1"><name><scalar value="a" type="string"/></name></AST_VAR></var><expr><scalar value="1" type="int"/></expr></AST_ASSIGN></AST_STMT_LIST></ast>
//
```

or to pretty print,

```
$code = '<?php $a = 1;';

$ast = \ast\parse_code($input, /*version*/ 35);
$xml = \ASTXML::ast2xml($ast);

$domxml = new \DOMDocument('1.0');
$domxml->preserveWhiteSpace = true;
$domxml->formatOutput = true;
$domxml->loadXML($xml->asXML());
echo $domxml->saveXML();

//<?xml version="1.0"?>
//<ast>
//  <AST_STMT_LIST lineno="1">
//    <AST_ASSIGN lineno="1">
//      <var>
//        <AST_VAR lineno="1">
//          <name>
//            <scalar value="a" type="string"/>
//          </name>
//        </AST_VAR>
//      </var>
//      <expr>
//        <scalar value="1" type="int"/>
//      </expr>
//    </AST_ASSIGN>
//  </AST_STMT_LIST>
//</ast>
//
```

## Example XPaths

Here are some useful XPath examples for you to use as LINT.

### Assignment within if, such as `if ($a = 1) {}`.

```
//AST_IF//AST_ASSIGN
```

### Param type not provided

```
//AST_PARAM[./type[not(node())]]
```

### Return type not provided

```
//AST_FUNC_DECL[./returnType[not(node())]]
```

### The third argument to `in_array` not provided

```
//AST_CALL[./expr/AST_NAME/name/scalar[@value="in_array"] and count(./args/AST_ARG_LIST/*) != 3]
```
