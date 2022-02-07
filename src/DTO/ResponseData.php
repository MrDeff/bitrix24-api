<?php

namespace Bitrix24Api\DTO;

class ResponseData
{
    /**
     * @var Result
     */
    protected Result $result;
    /**
     * @var Time
     */
    protected Time $time;
    /**
     * @var Pagination
     */
    protected Pagination $pagination;

    /**
     * ResponseData constructor.
     *
     * @param Result     $result
     * @param Time       $time
     * @param Pagination $pagination
     */
    public function __construct(Result $result, Time $time, Pagination $pagination)
    {
        $this->result = $result;
        $this->time = $time;
        $this->pagination = $pagination;
    }

    /**
     * @return Pagination
     */
    public function getPagination(): Pagination
    {
        return $this->pagination;
    }

    /**
     * @return Time
     */
    public function getTime(): Time
    {
        return $this->time;
    }

    /**
     * @return Result
     */
    public function getResult(): Result
    {
        return $this->result;
    }
}