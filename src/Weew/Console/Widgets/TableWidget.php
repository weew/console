<?php

namespace Weew\Console\Widgets;

use Weew\Console\IInput;
use Weew\Console\IOutput;
use Weew\Console\OutputFormat;

class TableWidget {
    /**
     * @var array
     */
    private $rows = [];

    /**
     * @var int
     */
    private $gutter = 1;

    /**
     * @var int
     */
    private $indent = 2;

    /**
     * @var int
     */
    private $sectionIndent = 1;

    /**
     * @var string
     */
    private $title = '';

    /**
     * @var string
     */
    private $verticalSeparator = '';

    /**
     * @var IInput
     */
    private $input;

    /**
     * @var IOutput
     */
    private $output;

    /**
     * TableWidget constructor.
     *
     * @param IInput $input
     * @param IOutput $output
     */
    public function __construct(IInput $input, IOutput $output) {
        $this->input = $input;
        $this->output = $output;
    }

    /**
     * @param $title
     *
     * @return $this
     */
    public function setTitle($title) {
        $this->title = $title;

        return $this;
    }

    /**
     * @param $verticalSeparator
     *
     * @return $this
     */
    public function setVerticalSeparator($verticalSeparator) {
        $this->verticalSeparator = $verticalSeparator;

        return $this;
    }

    /**
     * @param $gutter
     *
     * @return $this
     */
    public function setGutter($gutter) {
        $this->gutter = $gutter;

        return $this;
    }

    /**
     * @param $indent
     *
     * @return $this
     */
    public function setIndent($indent) {
        $this->indent = $indent;

        return $this;
    }

    /**
     * @param $sectionIndent
     *
     * @return $this
     */
    public function setSectionIndent($sectionIndent) {
        $this->sectionIndent = $sectionIndent;

        return $this;
    }

    /**
     * @param $cols
     *
     * @return $this
     */
    public function addRow($cols) {
        $this->addItem('@row', func_get_args());

        return $this;
    }

    /**
     * @param $cols
     *
     * @return $this
     */
    public function addSection($cols) {
        $this->addItem('@section', func_get_args());

        return $this;
    }

    /**
     * Render table.
     */
    public function render() {
        if ($this->title) {
            $this->output->writeLine($this->title);
        }

        $rows = $this->formatRows($this->rows);
        $widths = $this->calculateColumnWidths($rows);

        foreach ($rows as $row) {
            $type = $row['type'];

            foreach ($row['cols'] as $colIndex => $col) {
                if ($type === '@row') {
                    $width = array_get($widths, $colIndex);
                    $colWidth = strlen($this->output->format($col, OutputFormat::PLAIN));

                    // if not last row
                    if ( array_has($row['cols'], $colIndex + 1)) {
                        $col .= str_repeat(' ', $width - $colWidth);
                    }
                }

                $this->output->write($col);
            }

            $this->output->writeLine();
        }
    }

    /**
     * @param $type
     * @param array $args
     */
    private function addItem($type, array $args) {
        if (is_array(array_first($args))) {
            $cols = array_values(array_first($args));
        } else {
            // reset array indexes
            $cols = array_values($args);
        }

        $this->rows[] = ['type' => $type, 'cols' => $cols];
    }

    /**
     * @param array $rows
     *
     * @return array
     */
    private function formatRows(array $rows) {
        foreach ($rows as $rowIndex => $row) {
            $type = $row['type'];
            $indent = $this->indent;
            $gutter = str_repeat(' ', $this->gutter);
            $verticalSeparator = $this->verticalSeparator;

            if ($type === '@section') {
                $indent = $this->sectionIndent;
            }

            foreach ($row['cols'] as $colIndex => $col) {
                if ($colIndex === 0) {
                    $col = str_repeat(' ', $indent) . $col;
                } else {
                    $col = $gutter . $verticalSeparator . $gutter . $col;
                }

                array_set($row['cols'], $colIndex, $col);
            }

            array_set($rows, $rowIndex, $row);
        }

        return $rows;
    }

    /**
     * @param array $rows
     *
     * @return array
     */
    private function calculateColumnWidths(array $rows) {
        $widths = [];

        foreach ($rows as $row) {
            if ($row['type'] !== '@row') {
                continue;
            }

            foreach ($row['cols'] as $colIndex => $col) {
                $currentWidth = array_get($widths, $colIndex, 0);
                $col = $this->output->format($col, OutputFormat::PLAIN);
                $width = strlen($col);

                if ($width > $currentWidth) {
                    $widths[$colIndex] = $width;
                }
            }
        }

        return $widths;
    }
}
