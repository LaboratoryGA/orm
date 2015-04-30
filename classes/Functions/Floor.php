<?php
namespace Claromentis\Orm\Functions;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;;

/**
 * This is basicallt just grabbed from the "mysql floor" example from here:
 * http://docs.doctrine-project.org/en/2.1/reference/dql-doctrine-query-language.html
 *
 * @author cmadmin
 */
class Floor extends FunctionNode {
	
    public $simpleArithmeticExpression;

	public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker) {
		return 'FLOOR(' . $sqlWalker->walkSimpleArithmeticExpression(
						$this->simpleArithmeticExpression
				) . ')';
	}

	public function parse(\Doctrine\ORM\Query\Parser $parser) {
		$lexer = $parser->getLexer();

		$parser->match(Lexer::T_IDENTIFIER);
		$parser->match(Lexer::T_OPEN_PARENTHESIS);

		$this->simpleArithmeticExpression = $parser->SimpleArithmeticExpression();

		$parser->match(Lexer::T_CLOSE_PARENTHESIS);
	}

}
