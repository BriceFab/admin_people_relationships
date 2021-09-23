<?php

namespace App\Query\Mysql;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

/**
 * Créer une fonction SQL SplitString
 * Class SplitString
 * @author Riva Fabrice
 * @package App\Query
 */
class SplitString extends FunctionNode
{
    /*
     * en pure SQL:
DROP FUNCTION IF EXISTS SPLIT_STRING;
CREATE FUNCTION SPLIT_STRING(delim VARCHAR(12), str VARCHAR(255), pos INT)
RETURNS VARCHAR(255) DETERMINISTIC
RETURN
    REPLACE(
        SUBSTRING(
            SUBSTRING_INDEX(str, delim, pos),
            LENGTH(SUBSTRING_INDEX(str, delim, pos-1)) + 1
        ),
        delim, ''
    );
     */

    private $delimiter = null;
    private $string = null;
    private $pos = null;

    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);

        $this->delimiter = $parser->StringPrimary();

        $parser->match(Lexer::T_COMMA);
        $this->string = $parser->StringPrimary();

        $parser->match(Lexer::T_COMMA);
        $this->pos = $parser->ArithmeticFactor();

        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    public function getSql(SqlWalker $sqlWalker)
    {
        $delimiter = $this->delimiter->value;
        $string = $this->string->value;
        $pos = $sqlWalker->walkArithmeticTerm($this->pos);

        return <<<SQL
            REPLACE(
                SUBSTRING(
                    SUBSTRING_INDEX('$string', '$delimiter', $pos),
                    LENGTH(SUBSTRING_INDEX('$string', '$delimiter', $pos - 1)) + 1
                ),
                '$delimiter', ''
            )
SQL;
    }
}
