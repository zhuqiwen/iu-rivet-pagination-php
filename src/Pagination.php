<?php

namespace Edu\IU\VPCM\Rivet;

/**
 * @author Qiwen Zhu
 * @emial qiwzhu@iu.edu
 */

class Pagination {
    protected $pageKeyInGet = 'page';
    protected $baseUrl;
    protected $perPage = 9;
    protected $totalItems;
    protected $numPageLinks = 5;
    protected $last;

    public function __construct(string $baseUrl, int $total, array $options = [])
    {
        $this->baseUrl = $baseUrl;
        $this->totalItems = $total;
        $this->perPage = isset($options['perPage']) ?? $this->perPage;
        $this->pageKeyInGet = isset($options['pageKey']) ?? $this->pageKeyInGet;
        $this->numPageLinks = isset($options['numPageLinks']) ?? $this->numPageLinks;
        $this->last = $this->getTotalPageLinks();
    }


    /**
     * @return string
     * Return rendered pagination
     */
    public function render(): string
    {
        $prevSet = $this->buildPreviousSet();
        $prev = $this->buildPrevious();
        $nextSet = $this->buildNextSet();
        $next = $this->buildNext();
        $pageLinks = $this->buildPageLinks();

        $html = <<< PAGINATION
<nav role="navigation" aria-label="More pages of items">
    <ul class="rvt-pagination">
    $prevSet
    $prev
    $pageLinks
    $next
    $nextSet
    </ul>
</nav>
PAGINATION;


        return $html;
    }



    protected function getCurrentPage(): int
    {
        return
        (isset($_GET[$this->pageKeyInGet]) && filter_var($_GET[$this->pageKeyInGet], FILTER_VALIDATE_INT))
            ?
            $_GET[$this->pageKeyInGet]
            :
            1;
    }

    protected function getTotalPageLinks(): int
    {
        return  ceil($this->totalItems / $this->perPage);
    }

    protected function buildNext(): string
    {

    }

    protected function buildNextSet(): string
    {

    }

    protected function buildPrevious(): string
    {

    }

    protected function buildPreviousSet(): string
    {

    }


    //checkers

    private function hasPrev(): bool
    {
        return $this->getCurrentPage() != 1;
    }

    private function hasNext(): bool
    {
        return $this->getCurrentPage() != $this->last;
    }

    private function hasPrevSet(): bool
    {
        if(!$this->hasPrev()){
            return false;
        }

        return $this->getCurrentPage() < ($this->last - $this->numPageLinks);
    }

    private function hasNextSet(): bool
    {
        if(!$this->hasNext()){
            return false;
        }

        return $this->getCurrentPage() > $this->numPageLinks;
    }



}