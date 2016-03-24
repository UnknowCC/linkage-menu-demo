<?php

namespace library;

/**
 * 分页类
 */
class Page
{
    private $total;
    private $listRows;
    private $limit;
    private $uri;
    private $pageNum;
    private $page;
    private $config = array(
        'head' => '<b>共 %TOTAL_ROW% 条记录</b>',
        'first' => '首页',
        'prev' => '上一页',
        'next' => '下一页',
        'last' => '尾页',
        'jump' => '跳转'
    );
    private $listNum;

    private function __construct($total, $listRows = 20, $query = '', $listNum = 9,
     $order = true)
    {
        $this->total = $total;
        $this->listRows = $listRows;
        $this->listNum = $listNum;
        $this->uri = $listNum;
        $this->uri = $this->getUri($query);
        $this->pageNum = ceil($this->total / $this->listRows);

        if (isset($_GET['page'])) {
            $page = intval($_GET['page']);
        } else {
            if ($order) {
                $page = 1;
            } else {
                $page = $this->pageNum;
            }
        }

        if ($total > 0) {
            if (preg_match('/\D/', $page)) {
                $this->page = 1;
            } else {
                $this->page = abs($page);
            }
        } else {
            $this->page = 0;
        }

        $this->limit = " LIMIT ".$this->setLimit();
    }

    /**
     * 获取当前网页URL
     * @param  string|array $query 查询参数
     * @return string        url查询格式
     */
    private function getUri($query)
    {
        $requestUri = $_SERVER['REQUEST_URI'];
        $url = strstr($requestUri, '?') ? $requestUri : $requestUri.'?';

        if (is_array($query)) {
            $url .= http_build_query($query);
        } elseif (! empty($query)) {
            $url .= '&'.trim($query, '?&');
        }

        $urlArr = parse_url($url);
        if (isset($urlArr['query'])) {
            parse_str($urlArr['query'], $queryArr);
            unset($queryArr['page']);
            $url = $urlArr['path'].'?'.http_build_query($queryArr);
        }

        if (strstr($url, '?')) {
            if (substr($url, -1) != '?') {
                $url .= '&';
            }
        } else {
            $url .= '?';
        }
        return $url;
    }

    /**
     * 设置每页显示记录的条数
     */
    private function setLimit()
    {
        if ($this->page > 0) {
            return ($this->page - 1) * $this->listRows.', '.$this->listRows;
        }
    }

    /**
     * 分页内容
     * @return string
     */
    private function paging()
    {
        $pageArr = array();

        $pageArr[] = str_replace('%TOTAL_ROW%', $this->total, $this->config['head']);
        $pageArr[] = $this->firstPage();
        $pageArr[] = $this->pageList();
        $pageArr[] = $this->nextPage();
        $pageArr[] = $this->goPage();

        $pageStr = '<div class="page">';
        $pageStr .= implode(' ', $pageArr);
        $pageStr .= '</div>';
        return $pageStr;
    }

    /**
     * 首页和前一页
     * @return string
     */
    private function firstPage()
    {
        if ($this->page > 1) {
            $str = '<a href="'.$this->uri.'page=1">'.$this->config['first'].'</a>';
            $str .= '<a href="'.$this->uri.'page='.($this->page-1).'">'.$this->config['prev'].'</a>';
            return $str;
        }
    }

    /**
     * 页面清单
     * @return string
     */
    private function pageList()
    {
        $linkPageStr = '<span>';
        $prevNum = ceil($this->listNum / 2);
        $nextNum = floor($this->listNum / 2);

        for ($i=$prevNum; $i >= 1 ; $i--) {
            $page = $this->page - $i;
            if ($page >= 1) {
                $linkPageStr .= '<a href="'.$this->uri.'page='.$page.'">'.$page.'</a>';
            }
        }

        if ($this->pageNum > 1) {
            $linkPageStr .= '<span class="currentPage">';
            $linkPageStr .= $this->page;
            $linkPageStr .= '</span>';
        }

        for ($i=1; $i < $nextNum; $i++) {
            $page = $this->page + $i;
            if ($page <= $this->pageNum) {
                $linkPageStr .= '<a href="'.$this->uri.'page='.$page.'">'.$page.'</a>';
            } else {
                break;
            }
        }
        $linkPageStr .= '</span>';
        return $linkPageStr;
    }

    /**
     * 下一页和尾页
     * @return string
     */
    private function nextPage()
    {
        if ($this->page != $this->pageNum) {
            $str = '<a href="'.$this->uri.'page='.($this->page + 1).'">'.$this->config['next'].'</a>';

            $str .= '<a href="'.$this->uri.'page='.$this->pageNum.'">'.$this->config['last'].'</a>';
            return $str;
        }
    }

    /**
     * 页面跳转
     * @return [type] [description]
     */
    private function goPage()
    {
        $str = '<input class="pagevalue" type="text" value="'.$this->page.'">';
        $str .= '<input class="pagejump" type="button" value="'.$this->config['jump'].'">';
        return $str;
    }

    /**
     * 用类当方法使用
     * $page = new Page($argument);
     * echo $page();
     */
    // public function __invoke()
    // {
    //     return $this->paging();
    // }

    /**
     * 静态调用
     * @param  [type] $method    [description]
     * @param  [type] $arguments [description]
     * @return [type]            [description]
     */
    public static function __callStatic($method, $arguments)
    {
        if ($method == 'paging') {
            $linkObj = new self($arguments);
            return $linkObj->$method();
        } else {
            return '';
        }
    }
}

echo Page::paging(40);
// $page = new Page(20,5);
// echo $page();
