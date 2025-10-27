<?php
function excelToArray($filepath,$start=2){  
    require_once dirname(__FILE__) . '/Classes/PHPExcel/IOFactory.php';  
      
    //加载excel文件  
    $filename = $filepath;  
    $objPHPExcelReader = PHPExcel_IOFactory::load($filename);    
  
    // $reader = $objPHPExcelReader->getWorksheetIterator();  
    //循环读取sheet  
    $reader[] = $objPHPExcelReader->getSheet(0);
    foreach($reader as $sheet) {  
        //读取表内容  
        $content = $sheet->getRowIterator();  
        //逐行处理  
        $res_arr = array();  
        foreach($content as $key => $items) {  
              
             $rows = $items->getRowIndex();              //行  
             $columns = $items->getCellIterator();       //列  
             $row_arr = array();  
             //确定从哪一行开始读取  
             if($rows < $start){  
                 continue;  
             }
             //逐列读取  
             foreach($columns as $head => $cell) {  
                 //获取cell中数据  
                 $data = $cell->getValue();
                 if(is_object($data))  $data= $data->__toString(); 
                 $row_arr[] = $data;  
             }  
             $res_arr[] = $row_arr;  
        }  
          
    }  
      
    return $res_arr;  
}
/** 
 * 创建(导出)Excel数据表格 
 * @param  array   $list        要导出的数组格式的数据 
 * @param  string  $filename    导出的Excel表格数据表的文件名 
 * @param  array   $indexKey    $list数组中与Excel表格表头$header中每个项目对应的字段的名字(key值) 
 * @param  array   $startRow    第一条数据在Excel表格中起始行 
 * @param  [bool]  $excel2007   是否生成Excel2007(.xlsx)以上兼容的数据表 
 * 比如: $indexKey与$list数组对应关系如下: 
 *     $indexKey = array('id','username','sex','age'); 
 *     $list = array(array('id'=>1,'username'=>'YQJ','sex'=>'男','age'=>24)); 
 */  
function exportExcel($list,$filename,$indexKey,$startRow=1,$excel2007=false){  
    //文件引入  
	require_once dirname(__FILE__) . '/Classes/PHPExcel.php';
	require_once dirname(__FILE__) . '/Classes/PHPExcel/Writer/Excel2007.php';  
      
    if(empty($filename)) $filename = time();  
    if( !is_array($indexKey)) return false;  
      
    $header_arr = array('A','B','C','D','E','F','G','H','I','J','K','L','M', 'N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI');  
    //初始化PHPExcel()  
    $objPHPExcel = new PHPExcel();  
      
    //设置保存版本格式  
    if($excel2007){  
        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);  
        $filename = $filename.'.xlsx';  
    }else{  
        $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);  
        $filename = $filename.'.xls';  
    }  
      
    //接下来就是写数据到表格里面去  
    $objActSheet = $objPHPExcel->getActiveSheet();  
    //$startRow = 1;  
    foreach ($indexKey as $key => $value){
        $objActSheet->setCellValue($header_arr[$key].$startRow,$value);
    }
    $startRow++;
    foreach ($list as $row) {
        $i = 0;
        foreach ($row as $key => $value){  
            //这里是设置单元格的内容  
            $objActSheet->setCellValue($header_arr[$i].$startRow,$value);
            $i++;
        }  
        $startRow++;  
    }  
      
    // 下载这个表格，在浏览器输出  
    header("Pragma: public");  
    header("Expires: 0");  
    header("Cache-Control:must-revalidate, post-check=0, pre-check=0");  
    header("Content-Type:application/force-download");  
    header("Content-Type:application/vnd.ms-execl");  
    header("Content-Type:application/octet-stream");  
    header("Content-Type:application/download");;  
    header('Content-Disposition:attachment;filename='.$filename.'');  
    header("Content-Transfer-Encoding:binary");  
    $objWriter->save('php://output');  
} 
