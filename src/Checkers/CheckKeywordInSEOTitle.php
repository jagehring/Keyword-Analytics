<?php

namespace Qmas\KeywordAnalytics\Checkers;

use Qmas\KeywordAnalytics\Abstracts\Checker;
use Qmas\KeywordAnalytics\CheckingMessage;

class CheckKeywordInSEOTitle extends CheckSEOTitleLength
{
    private $min;

    private $max;

    protected $keyword;

    protected $keywordCount = 0;

    public function __construct($keyword, $seo_title)
    {
        parent::__construct($seo_title);

        $this->min = config('keyword-analytics.variables.keyword_in_seo_title.min');
        $this->max = config('keyword-analytics.variables.keyword_in_seo_title.max');

        $this->keyword = $keyword;
    }

    protected function countKeyword()
    {
        $this->keywordCount = preg_match_all('/('.$this->keyword.')/im', $this->title);
    }

    public function check(): Checker
    {
        if ($this->titleCharactersCount === 0) {
            $this->result->push($this->msgIfSEOTitleEmpty());
            return $this;
        }

        $this->countKeyword();

        if ($this->keywordCount === 0) {
            $this->result->push($this->msgIfKeywordNotFound());
        }
        elseif ($this->keywordCount < $this->min) {
            $this->result->push($this->msgIfKeywordTooLow());
        }
        elseif ($this->keywordCount > $this->max) {
            $this->result->push($this->msgIfKeywordTooOften());
        }
        else {
            $this->result->push($this->msgIfEnough());
        }

        return $this;
    }

    protected function msgIfSEOTitleEmpty(): array
    {
        return (new CheckingMessage(
            CheckingMessage::IGNORED_TYPE,
            CheckingMessage::SEOTITLE_FIELD,
            CheckingMessage::IGNORE_MSG_ID,
            __('The meta title is empty.'),
            CheckingMessage::KEYWORD_COUNT_VALIDATOR,
            ["min" => $this->min, "max" => $this->max]
        ))->build();
    }

    protected function msgIfKeywordNotFound(): array
    {
        return (new CheckingMessage(
            CheckingMessage::ERROR_TYPE,
            CheckingMessage::SEOTITLE_FIELD,
            CheckingMessage::KEYWORD_NOT_FOUND_MSG_ID,
            __('The meta title does not contain the keyword.'),
            CheckingMessage::KEYWORD_COUNT_VALIDATOR,
            ["min" => $this->min, "max" => $this->max, "keywordCount" => 0]
        ))->build();
    }

    protected function msgIfKeywordTooLow(): array
    {
        return (new CheckingMessage(
            CheckingMessage::ERROR_TYPE,
            CheckingMessage::SEOTITLE_FIELD,
            CheckingMessage::KEYWORD_TOO_LOW_MSG_ID,
            __('The meta title contains the keyword less than :min times.', ['min' => $this->min]),
            CheckingMessage::KEYWORD_COUNT_VALIDATOR,
            ["min" => $this->min, "max" => $this->max, "keywordCount" => $this->keywordCount]
        ))->build();
    }

    protected function msgIfKeywordTooOften(): array
    {
        return (new CheckingMessage(
            CheckingMessage::ERROR_TYPE,
            CheckingMessage::SEOTITLE_FIELD,
            CheckingMessage::KEYWORD_TOO_OFTEN_MSG_ID,
            __('The meta title contains the keyword more than :max times.', ['max' => $this->max]),
            CheckingMessage::KEYWORD_COUNT_VALIDATOR,
            ["min" => $this->min, "max" => $this->max, "keywordCount" => $this->keywordCount]
        ))->build();
    }

    protected function msgIfEnough(): array
    {
        return (new CheckingMessage(
            CheckingMessage::SUCCESS_TYPE,
            CheckingMessage::SEOTITLE_FIELD,
            CheckingMessage::SUCCESS_MSG_ID,
            __('The keyword was found in the meta title :count times.', ['count' => $this->keywordCount]),
            CheckingMessage::KEYWORD_COUNT_VALIDATOR,
            ["min" => $this->min, "max" => $this->max, "keywordCount" => $this->keywordCount]
        ))->build();
    }
}
