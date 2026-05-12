<?php

namespace App\Models;
use PhpOffice\PhpWord\TemplateProcessor;

class TemplateProcessorMod extends TemplateProcessor
{
 public function cloneBlock($blockname, $clones = 1, $replace = true, $indexVariables = false, $variableReplacements = null)
  {
        $xmlBlock = null;
        $matches = array();
        preg_match(
            '/(\${' . $blockname . '})(.*?)(\${\/' . $blockname . '})/is',
            $this->tempDocumentMainPart,
            $matches
        );

        if (isset($matches[3])) {
            $xmlBlock = $matches[2];
            if ($indexVariables) {
                $cloned = $this->indexClonedVariables($clones, $xmlBlock);
            } elseif ($variableReplacements !== null && is_array($variableReplacements)) {
                $cloned = $this->replaceClonedVariables($variableReplacements, $xmlBlock);
            } else {
                $cloned = array();
                for ($i = 1; $i <= $clones; $i++) {
                    $cloned[] = $xmlBlock;
                }
            }

            if ($replace) {
                $this->tempDocumentMainPart = str_replace(
                    $matches[1] . $matches[2] . $matches[3],
                    implode('', $cloned),
                    $this->tempDocumentMainPart
                );
            }
        }

        return $xmlBlock;
    }
}
