<?php
/**
 * Drupal_Sniffs_Semantics_FunctionTSniff
 *
 * PHP version 5
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @link     http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Check that "@" and "%" placeholders in t()/watchdog() are not escaped twice
 * with check_plain().
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @link     http://pear.php.net/package/PHP_CodeSniffer
 */
class DrupalPractice_Sniffs_FunctionCalls_TCheckPlainSniff extends Drupal_Sniffs_Semantics_FunctionCall
{


    /**
     * Returns an array of function names this test wants to listen for.
     *
     * @return array
     */
    public function registerFunctionNames()
    {
        return array(
                't',
                'watchdog',
               );

    }//end registerFunctionNames()


    /**
     * Processes this function call.
     *
     * @param PHP_CodeSniffer_File $phpcsFile
     *   The file being scanned.
     * @param int $stackPtr
     *   The position of the function call in the stack.
     * @param int $openBracket
     *   The position of the opening parenthesis in the stack.
     * @param int $closeBracket
     *   The position of the closing parenthesis in the stack.
     * @param Drupal_Sniffs_Semantics_FunctionCallSniff $sniff
     *   Can be used to retreive the function's arguments with the getArgument()
     *   method.
     *
     * @return void
     */
    public function processFunctionCall(
        PHP_CodeSniffer_File $phpcsFile,
        $stackPtr,
        $openBracket,
        $closeBracket,
        Drupal_Sniffs_Semantics_FunctionCallSniff $sniff
    ) {
        $tokens = $phpcsFile->getTokens();
        if ($tokens[$stackPtr]['content'] === 't') {
            $argument = $sniff->getArgument(2);
        } else {
            // For watchdog() the placeholders are in the third argument.
            $argument = $sniff->getArgument(3);
        }

        if ($argument === false) {
            return;
        }

        if ($tokens[$argument['start']]['code'] !== T_ARRAY) {
            return;
        }

        $checkPlain = $argument['start'];
        while ($checkPlain = $phpcsFile->findNext(T_STRING, ($checkPlain + 1), $tokens[$argument['start']]['parenthesis_closer'])) {
            if ($tokens[$checkPlain]['content'] === 'check_plain') {
                $warning = 'The extra check_plain() is not necessary for placeholders, "@" and "%" will automatically run check_plain()';
                $phpcsFile->addWarning($warning, $checkPlain, 'CheckPlain');
            }
        }

    }//end processFunctionCall()


}//end class

?>
