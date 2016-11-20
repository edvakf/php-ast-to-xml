<?php

namespace astxml;

class ASTXML
{
    public static function ast2xml(\ast\Node $ast): \SimpleXMLElement
    {
        $xml = new \SimpleXMLElement('<ast/>');
        self::dig($xml, $ast);
        return $xml;
    }

    private static function dig(\SimpleXMLElement $xml, \ast\Node $node): \SimpleXMLElement
    {
        $self = $xml->addChild(\ast\get_kind_name($node->kind));

        $self->addAttribute('lineno', $node->lineno);

        if (\ast\kind_uses_flags($node->kind)) {
            $flags = self::getFlagNames($node->kind, $node->flags);
            foreach ($flags as $flag) {
                // XML's boolean attributes often have the value equal to its name
                $self->addAttribute($flag, $flag);
            }
        }

        if ($node instanceof \ast\Node\Decl) {
            // function and class declaration
            $self->addAttribute('name', $node->name);
            $self->addAttribute('endLineno', $node->endLineno);
            $self->addAttribute('docComment', $node->docComment);
        }

        foreach ($node->children as $attr => $child) {
            $parent = $self;
            if (is_string($attr)) {
                $parent = $self->addChild($attr);
            }
            if (is_null($child)) {
                // do nothing
            } elseif (is_scalar($child)) {

                $scalar = $parent->addChild('scalar');
                $scalar->addAttribute('value', $child);

                if (is_int($child)) {
                    $scalar->addAttribute('type', 'int');
                } elseif (is_float($child)) {
                    $scalar->addAttribute('type', 'float');
                } elseif (is_bool($child)) {
                    $scalar->addAttribute('type', 'bool');
                } elseif (is_string($child)) {
                    $scalar->addAttribute('type', 'string');
                }
            } else {
                self::dig($parent, $child);
            }
        }

        return $self;
    }

    private static function getFlagNames(int $kind, int $flags): array
    {
        $consts = [
            ['kinds' => [\ast\AST_NAME], 'exclusive' => true, 'flags' => [
            "\\ast\\flags\\NAME_FQ",
            "\\ast\\flags\\NAME_NOT_FQ",
            "\\ast\\flags\\NAME_RELATIVE",
            ]],
            ['kinds' => [\ast\AST_METHOD, \ast\AST_PROP_DECL, \ast\AST_CLASS_CONST_DECL, \ast\AST_TRAIT_ALIAS], 'exclusive' => false, 'flags' => [
            "\\ast\\flags\\MODIFIER_PUBLIC",
            "\\ast\\flags\\MODIFIER_PROTECTED",
            "\\ast\\flags\\MODIFIER_PRIVATE",
            "\\ast\\flags\\MODIFIER_STATIC",
            "\\ast\\flags\\MODIFIER_ABSTRACT",
            "\\ast\\flags\\MODIFIER_FINAL",
            ]],
            ['kinds' => [\ast\AST_CLOSURE], 'exclusive' => false, 'flags' => [
            "\\ast\\flags\\MODIFIER_STATIC",
            ]],
            ['kinds' => [\ast\AST_FUNC_DECL, \ast\AST_METHOD, \ast\AST_CLOSURE], 'exclusive' => false, 'flags' => [
            "\\ast\\flags\\RETURNS_REF",
            ]],
            ['kinds' => [\ast\AST_CLASS], 'exclusive' => true, 'flags' => [
            "\\ast\\flags\\CLASS_ABSTRACT",
            "\\ast\\flags\\CLASS_FINAL",
            "\\ast\\flags\\CLASS_TRAIT",
            "\\ast\\flags\\CLASS_INTERFACE",
            "\\ast\\flags\\CLASS_ANONYMOUS",
            ]],
            ['kinds' => [\ast\AST_PARAM], 'exclusive' => true, 'flags' => [
            "\\ast\\flags\\PARAM_REF",
            "\\ast\\flags\\PARAM_VARIADIC",
            ]],
            ['kinds' => [\ast\AST_TYPE], 'exclusive' => true, 'flags' => [
            "\\ast\\flags\\TYPE_ARRAY",
            "\\ast\\flags\\TYPE_CALLABLE",
            "\\ast\\flags\\TYPE_VOID",
            "\\ast\\flags\\TYPE_BOOL",
            "\\ast\\flags\\TYPE_LONG",
            "\\ast\\flags\\TYPE_DOUBLE",
            "\\ast\\flags\\TYPE_STRING",
            "\\ast\\flags\\TYPE_ITERABLE",
            ]],
            ['kinds' => [\ast\AST_CAST], 'exclusive' => true, 'flags' => [
            "\\ast\\flags\\TYPE_NULL",
            "\\ast\\flags\\TYPE_BOOL",
            "\\ast\\flags\\TYPE_LONG",
            "\\ast\\flags\\TYPE_DOUBLE",
            "\\ast\\flags\\TYPE_STRING",
            "\\ast\\flags\\TYPE_ARRAY",
            "\\ast\\flags\\TYPE_OBJECT",
            ]],
            ['kinds' => [\ast\AST_UNARY_OP], 'exclusive' => true, 'flags' => [
            "\\ast\\flags\\UNARY_BOOL_NOT",
            "\\ast\\flags\\UNARY_BITWISE_NOT",
            "\\ast\\flags\\UNARY_MINUS",
            "\\ast\\flags\\UNARY_PLUS",
            "\\ast\\flags\\UNARY_SILENCE",
            ]],
            ['kinds' => [\ast\AST_BINARY_OP, \ast\AST_ASSIGN_OP], 'exclusive' => true, 'flags' => [
            "\\ast\\flags\\BINARY_BITWISE_OR",
            "\\ast\\flags\\BINARY_BITWISE_AND",
            "\\ast\\flags\\BINARY_BITWISE_XOR",
            "\\ast\\flags\\BINARY_CONCAT",
            "\\ast\\flags\\BINARY_ADD",
            "\\ast\\flags\\BINARY_SUB",
            "\\ast\\flags\\BINARY_MUL",
            "\\ast\\flags\\BINARY_DIV",
            "\\ast\\flags\\BINARY_MOD",
            "\\ast\\flags\\BINARY_POW",
            "\\ast\\flags\\BINARY_SHIFT_LEFT",
            "\\ast\\flags\\BINARY_SHIFT_RIGHT",
            ]],
            ['kinds' => [\ast\AST_BINARY_OP], 'exclusive' => true, 'flags' => [
            "\\ast\\flags\\BINARY_BOOL_AND",
            "\\ast\\flags\\BINARY_BOOL_OR",
            "\\ast\\flags\\BINARY_BOOL_XOR",
            "\\ast\\flags\\BINARY_IS_IDENTICAL",
            "\\ast\\flags\\BINARY_IS_NOT_IDENTICAL",
            "\\ast\\flags\\BINARY_IS_EQUAL",
            "\\ast\\flags\\BINARY_IS_NOT_EQUAL",
            "\\ast\\flags\\BINARY_IS_SMALLER",
            "\\ast\\flags\\BINARY_IS_SMALLER_OR_EQUAL",
            "\\ast\\flags\\BINARY_IS_GREATER",
            "\\ast\\flags\\BINARY_IS_GREATER_OR_EQUAL",
            "\\ast\\flags\\BINARY_SPACESHIP",
            "\\ast\\flags\\BINARY_COALESCE",
            ]],
            ['kinds' => [\ast\AST_ASSIGN_OP], 'exclusive' => true, 'flags' => [
            "\\ast\\flags\\ASSIGN_BITWISE_OR",
            "\\ast\\flags\\ASSIGN_BITWISE_AND",
            "\\ast\\flags\\ASSIGN_BITWISE_XOR",
            "\\ast\\flags\\ASSIGN_CONCAT",
            "\\ast\\flags\\ASSIGN_ADD",
            "\\ast\\flags\\ASSIGN_SUB",
            "\\ast\\flags\\ASSIGN_MUL",
            "\\ast\\flags\\ASSIGN_DIV",
            "\\ast\\flags\\ASSIGN_MOD",
            "\\ast\\flags\\ASSIGN_POW",
            "\\ast\\flags\\ASSIGN_SHIFT_LEFT",
            "\\ast\\flags\\ASSIGN_SHIFT_RIGHT",
            ]],
            ['kinds' => [\ast\AST_MAGIC_CONST], 'exclusive' => true, 'flags' => [
            "\\ast\\flags\\MAGIC_LINE",
            "\\ast\\flags\\MAGIC_FILE",
            "\\ast\\flags\\MAGIC_DIR",
            "\\ast\\flags\\MAGIC_NAMESPACE",
            "\\ast\\flags\\MAGIC_FUNCTION",
            "\\ast\\flags\\MAGIC_METHOD",
            "\\ast\\flags\\MAGIC_CLASS",
            "\\ast\\flags\\MAGIC_TRAIT",
            ]],
            ['kinds' => [\ast\AST_USE, \ast\AST_GROUP_USE, \ast\AST_USE_ELEM], 'exclusive' => true, 'flags' => [
            "\\ast\\flags\\USE_NORMAL",
            "\\ast\\flags\\USE_FUNCTION",
            "\\ast\\flags\\USE_CONST",
            ]],
            ['kinds' => [\ast\AST_INCLUDE_OR_EVAL], 'exclusive' => true, 'flags' => [
            "\\ast\\flags\\EXEC_EVAL",
            "\\ast\\flags\\EXEC_INCLUDE",
            "\\ast\\flags\\EXEC_INCLUDE_ONCE",
            "\\ast\\flags\\EXEC_REQUIRE",
            "\\ast\\flags\\EXEC_REQUIRE_ONCE",
            ]],
            ['kinds' => [\ast\AST_ARRAY], 'exclusive' => true, 'flags' => [
            "\\ast\\flags\\ARRAY_SYNTAX_SHORT",
            "\\ast\\flags\\ARRAY_SYNTAX_LONG",
            "\\ast\\flags\\ARRAY_SYNTAX_LIST",
            ]],
            ];

        if (in_array($kind, [\ast\AST_ARRAY_ELEM, \ast\AST_CLOSURE_VAR], true)) {
            if ($flags === 1) {
                return ['BY_REFERENCE'];
            } else {
                return [];
            }
        }

        foreach ($consts as $const) {
            if (in_array($kind, $const['kinds'], true)) {
                $flag_list = [];
                foreach ($const['flags'] as $f) {
                    if ($const['exclusive']) {
                        if ($flags === constant($f)) {
                            return [explode("\\", $f)[3]];
                        }
                    } else {
                        if ($flags & constant($f)) {
                            $flag_list[] = explode("\\", $f)[3];
                        }
                    }
                }
                return $flag_list;
            }
        }
        return [];
    }
}
