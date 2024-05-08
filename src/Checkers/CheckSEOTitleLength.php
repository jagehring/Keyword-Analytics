<?php


namespace Qmas\KeywordAnalytics\Checkers;


use Qmas\KeywordAnalytics\Abstracts\Checker;
use Qmas\KeywordAnalytics\CheckingMessage;

class CheckSEOTitleLength extends Checker
{
    private $min;

    private $max;

    protected $seo_title;

    protected $seo_titleCharactersCount = 0;

    public function __construct($seo_title)
    {
        parent::__construct();

        $this->min = config('keyword-analytics.variables.seo_title_length.min');
        $this->max = config('keyword-analytics.variables.seo_title_length.max');

        $this->title = $seo_title;
        $this->titleCharactersCount = mb_strlen($this->title);
    }

    public function check(): Checker
    {
        if ($this->titleCharactersCount === 0) {
            $this->result->push($this->msgIfEmpty());
        }
        elseif ($this->titleCharactersCount < $this->min) {
            $this->result->push($this->msgIfTooShort());
        }
        elseif ($this->titleCharactersCount > $this->max) {
            $this->result->push($this->msgIfTooLong());
        }
        else {
            $this->result->push($this->msgIfOk());
        }

        return $this;
    }

    protected function msgIfEmpty()
    {
        return (new CheckingMessage(
            CheckingMessage::IGNORED_TYPE,
            CheckingMessage::SEOTITLE_FIELD,
            CheckingMessage::IGNORE_MSG_ID,
            __('Please consider to add some content to meta title tag.'),
            CheckingMessage::LENGTH_VALIDATOR,
            [
                "length" => 0,
                "min" => $this->min,
                "max" => $this->max
            ]
        ))->build();
    }

    protected function msgIfTooShort()
    {
        return (new CheckingMessage(
            CheckingMessage::WARNING_TYPE,
            CheckingMessage::SEOTITLE_FIELD,
            CheckingMessage::TOO_SHORT_MSG_ID,
            __('The meta title should be more than :min and less than :max chars.', [
                'min' => $this->min,
                'max' => $this->max
            ]),
            CheckingMessage::LENGTH_VALIDATOR,
            [
                "length" => $this->titleCharactersCount,
                "min" => $this->min,
                "max" => $this->max
            ]
        ))->build();
    }

    protected function msgIfTooLong()
    {
        return (new CheckingMessage(
            CheckingMessage::WARNING_TYPE,
            CheckingMessage::SEOTITLE_FIELD,
            CheckingMessage::TOO_LONG_MSG_ID,
            __('The meta title should be more than :min and less than :max chars.', [
                'min' => $this->min,
                'max' => $this->max
            ]),
            CheckingMessage::LENGTH_VALIDATOR,
            [
                "length" => $this->titleCharactersCount,
                "min" => $this->min,
                "max" => $this->max
            ]
        ))->build();
    }

    protected function msgIfOk()
    {
        return (new CheckingMessage(
            CheckingMessage::SUCCESS_TYPE,
            CheckingMessage::SEOTITLE_FIELD,
            CheckingMessage::SUCCESS_MSG_ID,
            __('The meta title should be more than :min and less than :max chars.', [
                'min' => $this->min,
                'max' => $this->max
            ]),
            CheckingMessage::LENGTH_VALIDATOR,
            [
                "length" => $this->titleCharactersCount,
                "min" => $this->min,
                "max" => $this->max
            ]
        ))->build();
    }

}
