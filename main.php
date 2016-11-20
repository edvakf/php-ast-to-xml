<?php

$code = '<?php $a = HOGE;';
$code = '<?php $a = 1 === 1; $b = 1 == 1;';
$code = '<?php function hoge() { $a = 1; $b = 2; };';
$code = '<?php class A{}';
$code = '<?php $a = null; $b = HOGE;';
$code = '<?php $a = [1, &$a];';
$code = '<?php $a = "asdf"; $b = function($c) {return $c + 1; }; echo $d = $b(2);';
try {
  $ast = ast\parse_code($code, /*version*/35);
  echo $code . PHP_EOL . PHP_EOL;
  //$ast = ast\parse_file('code.php', [>version<]35);
  //echo file_get_contents('code.php') . PHP_EOL . PHP_EOL;
} catch (ParseError $e) {
  echo 'failed to parse';
  exit();
}

$xml = new SimpleXMLElement('<code/>');

function dig($xml, $node) {
  if (is_null($node)) {
    // do nothing
  } elseif (is_scalar($node)) {
    $self = $xml->addChild('scalar');
    if (is_int($node)) {
      $self->addAttribute('type', 'int');
    } elseif (is_float($node)) {
      $self->addAttribute('type', 'float');
    } elseif (is_bool($node)) {
      $self->addAttribute('type', 'bool');
    } elseif (is_string($node)) {
      $self->addAttribute('type', 'string');
    }
    $self[0] = $node;
    return $self;
  } elseif ($node instanceof ast\Node) {

    $self = $xml->addChild(ast\get_kind_name($node->kind));

    $self->addAttribute('lineno', $node->lineno);
    if (ast\kind_uses_flags($node->kind)) {
      $self->addAttribute('flags', getFlagNames($node->kind, $node->flags));
    }

    if ($node instanceof ast\Node\Decl) {
      // function and class declaration
      $self->addAttribute('name', $node->name);
      $self->addAttribute('endLineno', $node->endLineno);
      $self->addAttribute('docComment', $node->docComment);
    }

    foreach ($node->children as $attr => $child) {
      if (is_string($attr)) {
        $container = $self->addChild($attr);
        dig($container, $child);
      } else {
        dig($self, $child);
      }
    }
    return $self;
  } else {
    throw new Exception('unknown node');
  }
}

function getFlagNames($kind, $flags) {
  $consts = [
    ['kinds' => [ast\AST_NAME], 'exclusive' => true, 'flags' => [
    "ast\\flags\\NAME_FQ",
    "ast\\flags\\NAME_NOT_FQ",
    "ast\\flags\\NAME_RELATIVE",
    ]],
    ['kinds' => [ast\AST_METHOD, ast\AST_PROP_DECL, ast\AST_CLASS_CONST_DECL, ast\AST_TRAIT_ALIAS], 'exclusive' => false, 'flags' => [
    "ast\\flags\\MODIFIER_PUBLIC",
    "ast\\flags\\MOFIFIER_PROTECTED",
    "ast\\flags\\MOFIFIER_PRIVATE",
    "ast\\flags\\MOFIFIER_STATIC",
    "ast\\flags\\MOFIFIER_ABSTRACT",
    "ast\\flags\\MOFIFIER_FINAL",
    ]],
    ['kinds' => [ast\AST_CLOSURE], 'exclusive' => false, 'flags' => [
    "ast\\flags\\MODIFIER_STATIC",
    ]],
    ['kinds' => [ast\AST_FUNC_DECL, ast\AST_METHOD, ast\AST_CLOSURE], 'exclusive' => false, 'flags' => [
    "ast\\flags\\RETURNS_REF",
    ]],
    ['kinds' => [ast\AST_CLASS], 'exclusive' => true, 'flags' => [
    "ast\\flags\\CLASS_ABSTRACT",
    "ast\\flags\\CLASS_FINAL",
    "ast\\flags\\CLASS_TRAIT",
    "ast\\flags\\CLASS_INTERFACE",
    "ast\\flags\\CLASS_ANONYMOUS",
    ]],
    ['kinds' => [ast\AST_PARAM], 'exclusive' => true, 'flags' => [
    "ast\\flags\\PARAM_REF",
    "ast\\flags\\PARAM_VARIADIC",
    ]],
    ['kinds' => [ast\AST_TYPE], 'exclusive' => true, 'flags' => [
    "ast\\flags\\TYPE_ARRAY",
    "ast\\flags\\TYPE_CALLABLE",
    "ast\\flags\\TYPE_VOID",
    "ast\\flags\\TYPE_BOOL",
    "ast\\flags\\TYPE_LONG",
    "ast\\flags\\TYPE_DOUBLE",
    "ast\\flags\\TYPE_STRING",
    "ast\\flags\\TYPE_ITERABLE",
    ]],
    ['kinds' => [ast\AST_CAST], 'exclusive' => true, 'flags' => [
    "ast\\flags\\TYPE_NULL",
    "ast\\flags\\TYPE_BOOL",
    "ast\\flags\\TYPE_LONG",
    "ast\\flags\\TYPE_DOUBLE",
    "ast\\flags\\TYPE_STRING",
    "ast\\flags\\TYPE_ARRAY",
    "ast\\flags\\TYPE_OBJECT",
    ]],
    ['kinds' => [ast\AST_UNARY_OP], 'exclusive' => true, 'flags' => [
    "ast\\flags\\UNARY_BOOL_NOT",
    "ast\\flags\\UNARY_BITWISE_NOT",
    "ast\\flags\\UNARY_MINUS",
    "ast\\flags\\UNARY_PLUS",
    "ast\\flags\\UNARY_SILENCE",
    ]],
    ['kinds' => [ast\AST_BINARY_OP, ast\AST_ASSIGN_OP], 'exclusive' => true, 'flags' => [
    "ast\\flags\\BINARY_BITWISE_OR",
    "ast\\flags\\BINARY_BITWISE_AND",
    "ast\\flags\\BINARY_BITWISE_XOR",
    "ast\\flags\\BINARY_CONCAT",
    "ast\\flags\\BINARY_ADD",
    "ast\\flags\\BINARY_SUB",
    "ast\\flags\\BINARY_MUL",
    "ast\\flags\\BINARY_DIV",
    "ast\\flags\\BINARY_MOD",
    "ast\\flags\\BINARY_POW",
    "ast\\flags\\BINARY_SHIFT_LEFT",
    "ast\\flags\\BINARY_SHIFT_RIGHT",
    ]],
    ['kinds' => [ast\AST_BINARY_OP], 'exclusive' => true, 'flags' => [
    "ast\\flags\\BINARY_BOOL_AND",
    "ast\\flags\\BINARY_BOOL_OR",
    "ast\\flags\\BINARY_BOOL_XOR",
    "ast\\flags\\BINARY_IS_IDENTICAL",
    "ast\\flags\\BINARY_IS_NOT_IDENTICAL",
    "ast\\flags\\BINARY_IS_EQUAL",
    "ast\\flags\\BINARY_IS_NOT_EQUAL",
    "ast\\flags\\BINARY_IS_SMALLER",
    "ast\\flags\\BINARY_IS_SMALLER_OR_EQUAL",
    "ast\\flags\\BINARY_IS_GREATER",
    "ast\\flags\\BINARY_IS_GREATER_OR_EQUAL",
    "ast\\flags\\BINARY_SPACESHIP",
    "ast\\flags\\BINARY_COALESCE",
    ]],
    ['kinds' => [ast\AST_ASSIGN_OP], 'exclusive' => true, 'flags' => [
    "ast\\flags\\ASSIGN_BITWISE_OR",
    "ast\\flags\\ASSIGN_BITWISE_AND",
    "ast\\flags\\ASSIGN_BITWISE_XOR",
    "ast\\flags\\ASSIGN_CONCAT",
    "ast\\flags\\ASSIGN_ADD",
    "ast\\flags\\ASSIGN_SUB",
    "ast\\flags\\ASSIGN_MUL",
    "ast\\flags\\ASSIGN_DIV",
    "ast\\flags\\ASSIGN_MOD",
    "ast\\flags\\ASSIGN_POW",
    "ast\\flags\\ASSIGN_SHIFT_LEFT",
    "ast\\flags\\ASSIGN_SHIFT_RIGHT",
    ]],
    ['kinds' => [ast\AST_MAGIC_CONST], 'exclusive' => true, 'flags' => [
    "ast\\flags\\MAGIC_LINE",
    "ast\\flags\\MAGIC_FILE",
    "ast\\flags\\MAGIC_DIR",
    "ast\\flags\\MAGIC_NAMESPACE",
    "ast\\flags\\MAGIC_FUNCTION",
    "ast\\flags\\MAGIC_METHOD",
    "ast\\flags\\MAGIC_CLASS",
    "ast\\flags\\MAGIC_TRAIT",
    ]],
    ['kinds' => [ast\AST_USE, ast\AST_GROUP_USE, ast\AST_USE_ELEM], 'exclusive' => true, 'flags' => [
    "ast\\flags\\USE_NORMAL",
    "ast\\flags\\USE_FUNCTION",
    "ast\\flags\\USE_CONST",
    ]],
    ['kinds' => [ast\AST_INCLUDE_OR_EVAL], 'exclusive' => true, 'flags' => [
    "ast\\flags\\EXEC_EVAL",
    "ast\\flags\\EXEC_INCLUDE",
    "ast\\flags\\EXEC_INCLUDE_ONCE",
    "ast\\flags\\EXEC_REQUIRE",
    "ast\\flags\\EXEC_REQUIRE_ONCE",
    ]],
    ['kinds' => [ast\AST_ARRAY], 'exclusive' => true, 'flags' => [
    "ast\\flags\\ARRAY_SYNTAX_SHORT",
    "ast\\flags\\ARRAY_SYNTAX_LONG",
    "ast\\flags\\ARRAY_SYNTAX_LIST",
    ]],
  ];

  if (in_array($kind, [ast\AST_ARRAY_ELEM, ast\AST_CLOSURE_VAR], true)) {
    if ($flags === 1) {
      return 'BY_REFERENCE';
    } else {
      return '';
    }
  }

  foreach ($consts as $const) {
    if (in_array($kind, $const['kinds'], true)) {
      $flag_list = [];
      foreach ($const['flags'] as $f) {
        if ($const['exclusive']) {
          if ($flags === constant($f)) {
            return explode("\\", $f)[2];
          }
        } else {
          if ($flags & constant($f)) {
            $flag_list[] = explode("\\", $f)[2];
          }
        }
      }
      return implode(',', $flag_list);
    }
  }
  return '';
}

dig($xml, $ast);

//echo $xml->asXML();

// http://stackoverflow.com/questions/8615422/php-xml-how-to-output-nice-format
$domxml = new DOMDocument('1.0');
$domxml->preserveWhiteSpace = true;
$domxml->formatOutput = true;
$domxml->loadXML($xml->asXML());
$out = $domxml->saveXML();
echo $out;
