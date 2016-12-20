<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;
use View;
use Auth;
use Illuminate\Http\Request;
use DB;
use App\Models\Utils\DateHandle;
use App\Models\Menu;
use App\Models\Guild\Guild;

class PageController extends BaseController
{
    use AuthorizesRequests, AuthorizesResources, DispatchesJobs, ValidatesRequests;
    
    /* 数据相关 */
    protected $connection;                          //数据库连接
    protected $database;                            //数据库名
    protected $table;                               //数据表名
    protected $proceduce;                           //存储过程名
    protected $modelName;                           //模型名称
    protected $isSourceData             = true;     //是否调用统计存储过程
    protected $searchFromSourceData     = false;    //存储过程搜索
    
    /* 搜索相关 */
    protected $searchBox;                           //搜索框
    protected $isAdvanceSearch          = false;    //是否开启高级搜索
    protected $advanceSearchBox         = NULL;     //高级搜索框    
    protected $advanceSearchFields;                 //高级搜索字段
    protected $searchKeyword;                       //搜索关键词字段
    protected $searchDatetime;                      //搜索日期字段
    protected $searchPlaceholder;                   //搜索占位符
    protected $dataFormat               = 1;        //1='Y-m-d' 2='Y-m-d H:i:s'
    protected $tableOrder               = '0, desc';
    protected $selectColumns            = '*';
    public    $customValue;                         //列表页传递特殊值，提供给ajax获取数据
    public    $columnDefs               = false;
    
    /* 显示相关 */
    protected $moduleView               ='pages';   //模板路径
    protected $listTitle;                           //列表页标题
    protected $showTitle;                           //详情页标题   
    protected $showType                 = false;    //详情是否传递类型参数
    protected $op;                                  //操作
    protected $tableColumns             = 'true,false,false,true,false,false,false,false,true,false';
    protected $customHtmlPart           = '';       //列表页顶部,自定义添加HTML内容
    protected $dateFilter               = true;     //是否显示选择日期选项
    
    /* URL相关  */
    protected $moduleRoute;                         //url路径
    protected $moduleAjax;                          //列表页ajax路径
    protected $moduleIndexAjax;                     //index列表 回调url
    protected $localUrl                 = 'http://localhost/gamepub/public';        //本地调试 
    protected $languageUrl              = '/chinese.json';
    
    /* 其它 */
    protected $type;                                //分类使用  
    protected $export                   = false;    //导出excel  开启/关闭
    protected $exportObject;                        //导出excel对象

    public function __construct()
    {
        $this->middleware('auth');
        View::composer(['layouts/sidebar','layouts/admin_sidebar'], function($view){
            $guild = count(Guild::where('GuildType', 0)->where('AuditStatus',2)->get());

            $menuCount['ChairmanController'] = $guild;
            $menus = Menu::menuLoad();
            $view->with('menus', $menus);
            $view->with('menuCount', $menuCount);
        });

        View::composer($this->moduleView.'/*', function ($view) {
            $view->with('languageUrl',          $this->languageUrl);
            $view->with('localUrl',             $this->localUrl);
            $view->with('moduleRoute',          $this->moduleRoute);
            $view->with('moduleAjax',           $this->moduleAjax);
            $view->with('searchPlaceholder',    $this->searchPlaceholder);
            $view->with('tableOrder',           $this->tableOrder);
            $view->with('tableColumns',         $this->tableColumns);
            $view->with('dateFilter',           $this->dateFilter);
            $view->with('moduleIndexAjax',      $this->moduleIndexAjax);
            $view->with('moduleView',           $this->moduleView);
            $view->with('searchBox',            $this->setSearchBox());
            $view->with('advanceSearchBox',     $this->setAdvanceSearchBox());
            $view->with('advanceSearchFields',  $this->advanceSearchFields);
            $view->with('isAdvanceSearch',      $this->isAdvanceSearch);
            $view->with('type',                 $this->type);
            $view->with('customHtmlPart',       $this->customHtmlPart);
            $view->with('exportObject',         $this->exportObject);
            $view->with('columnDefs',           $this->columnDefs);
        }); 
    }

    public function list()
    {
        $fields = $this->dataFields();
        $list = $fields['list'];
        foreach($list as $key => $value)
        {
            $titles[] = $value;
        }
        return view('pages/list', ['title'=>$this->listTitle, 'tableTitles'=>$titles, 'customValue'=>$this->customValue]);
    }

    public function list_ajax(Request $request)
    {
        if($this->searchFromSourceData)
        {
            $this->search_source_data($request);
        }

        $requests       = $request->all();
        $draw           = $requests['draw'];
        $columns        = $requests['columns'];
        $start          = $requests['start'];
        $length         = $requests['length'];
        $type           = $requests['type'];
        $this->type     = $type;

        if(isset($requests['searchKeyword']))
        {
            $searchValue    = trim($requests['searchKeyword']);
        }
        else
        {
            $searchValue    = '';
        }    
        if(isset($requests['searchFields']))
        {
            $searchFields    = $requests['searchFields'];
        }
        else
        {
            $searchFields    = NUll;
        }    

        $order          = $requests['order'];
        $orderNumber    = $order[0]['column'];
        $orderDir       = $order[0]['dir'];
        $conditions     = array();

        if(!empty($requests['dateRange']))
        {
            $dateRange      = $requests['dateRange'];
            $dateRange      = explode('-', $dateRange);
            if($this->dataFormat == 1)
            {
                $from   = DateHandle::dateFormat('Y/m/d H:i:s', 'Y-m-d', $dateRange[0]);
                $to     = DateHandle::dateFormat('Y/m/d H:i:s', 'Y-m-d', $dateRange[1]);
            }
            if($this->dataFormat == 2)
            {
                $from   = DateHandle::dateFormat('Y/m/d H:i:s', 'Y-m-d H:i:s', $dateRange[0]);
                $to     = DateHandle::dateFormat('Y/m/d H:i:s', 'Y-m-d H:i:s', $dateRange[1]);
            
            }
            if($this->dataFormat == 3)
            {
                $from   = DateHandle::dateFormat('Y/m/d H:i:s', 'timestamp', $dateRange[0]);
                $to     = DateHandle::dateFormat('Y/m/d H:i:s', 'timestamp', $dateRange[1]);
            }
        }
        else
        {
            $from  = NULL;
            $to    = NULL;
        }

        $dataObject = $this->dataFields();
        $list_fields = $dataObject['list'];
        foreach($list_fields as $key => $value)
        {
            $orderColumns[] = $key;
        }
        $orderColumnsStr = $orderColumns[$orderNumber];

        $sql = " SELECT {$this->selectColumns} FROM {$this->table} ";
        $searchConditions = $this->setSearchConditions($type);
        if(count($searchConditions))
        {
            $conditions = $searchConditions;
        }
        if($searchValue)
        {
            $searchKeyword = str_replace('-', '.', $this->searchKeyword);
            $conditions[] = " {$searchKeyword} like '%{$searchValue}%' ";
        }
        if($from && $to)
        {
           $searchDatetime = str_replace('-', '.', $this->searchDatetime);
           $conditions[] = " ({$searchDatetime} BETWEEN  '{$from}' AND '{$to}') ";
        }
        if($searchFields)
        {
            $searchFieldsOp = $this->advanceSearchFields;
            $searchFieldsOp = json_decode($searchFieldsOp);
            foreach($searchFields as $searchField)
            {
                $key = key($searchField);

                $value = trim(current($searchField));
                if($searchFieldsOp->$key == 'like' && !empty($value))
                {
                    $keyy = str_replace('-', '.', $key);
                    $conditions[] = " {$keyy} like '%{$value}%' ";
                }
                if($searchFieldsOp->$key == '=string' && !empty($value))
                {
                    $keyy = str_replace('-', '.', $key);
                    $conditions[] = " {$keyy} = '{$value}' ";
                } 
                if($searchFieldsOp->$key == '=int' && $value != 99)
                {
                    $keyy = str_replace('-', '.', $key);
                    $conditions[] = " {$keyy} = {$value} ";
                }
            } 
        }
        if(count($conditions))
        {
            $sql .= " WHERE ";
            $sql .= implode(' AND ', $conditions);
        }

        $countResult = DB::select($sql);
        $total  = count($countResult);

        $sql .= " ORDER BY {$orderColumnsStr} {$orderDir}";
        if($this->export)
        {
            $sql .= " LIMIT 10000 ";
        }
        else
        {
            $sql .= " LIMIT {$start}, {$length} ";
        }
        $results = DB::select($sql);
        $objects = array();
        $objects['draw'] = $draw;
        $objects['recordsTotal'] = $total;
        $objects['recordsFiltered'] = $total;

        if(count($results))
        {

            foreach($results as $result)
            {
                $object = array();
                foreach($list_fields as $key => $value)
                { 
                    if(preg_match('/^custom|^op/', $key))
                    {
                        $object[] = $this->dataFilter($key, NULL, $result, $type);
                    }
                    else
                    {
                        $object[] = $this->dataFilter($key, $result->$key, $result);
                    }
                }   
                $objects['data'][] = $object;
            }    
        }
        else
        {
            for($i=0; $i<10; $i++)
            {
                if($i == 0)
                {
                    $array[] = '空';
                }
                else
                {
                    $array[] = '';
                }
            }
            $objects['data'][] = $array;
        }

        return json_encode($objects);
    }

    public function view($id)
    {
        $model  = new $this->modelName();
        $object  = $model::find($id); 

        $fields = $this->dataFields();
        $showFields = $fields['show'];
        foreach($showFields as $key => $value)
        {
            if(preg_match('/^empty/', $key))
            {
                $object->$key = '暂无';
            }
            else
            {
                $object->$key = $this->dataFilter($key, $object->$key, $object);
            }

        }
        return view('pages/show', ['object'=>$object, 'title'=>$this->showTitle, 'fields'=>$show_fields]);
    }
    
    public function setSearchBox()
    {
        $str = '<div class="search_wrapper" style="text-align:right;">';
        $str .='<input type="text" placeholder="'.$this->searchPlaceholder.'" id="searchKeyword" class="form-control">';
        $str .=' <button type="button" class="btn btn-default" id="searchSubmit">搜索</button>';
        if($this->isAdvanceSearch)
        {
            $str .=' <button type="button" class="btn btn-default" title="高级查询" id="advanceSearchButton">高级</button>'; 
        }
        $str .='</div>';
        return $str; 
    }
    
    protected   function dataFields(){}
    public      function dataFilter($field, $data, $object=NULL){return $data;}
    public      function setAdvanceSearchBox(){}  
    public      function setAdvanceSearchFields(){}
    public      function setSearchConditions($type){return array();}
}
