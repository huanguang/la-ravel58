<?php

/**
 * Created by PhpStorm.
 * User: LHG
 * Date: 2019/9/9
 * Time: 17:28
 */
namespace App\Admin\Extensions;
use Encore\Admin\Grid\Exporters\ExcelExporter;
use Maatwebsite\Excel\Facades\Excel;
class ProductExporter extends ExcelExporter
{
    protected $fileName = '商品列表.xlsx';
    protected $head = [];

    protected $body = [];

    public function setAttr($head, $body){

        $this->head = $head;

        $this->body = $body;

    }

    public function export()

    {

        //定义文件名称为日期拼上uniqid()

        $fileName = date('YmdHis') . '-' . uniqid();

        Excel::create($fileName, function($excel) {

            $excel->sheet('sheet1', function($sheet) {

                // 这段逻辑是从表格数据中取出需要导出的字段

                $head = $this->head;

                $body = $this->body;

                //init列

                $title_array = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q',

                    'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH'];

                $rows = collect([$head]); //写入标题

                $sheet->rows($rows);

                collect( $this->getData() )->map( function ($item,$k)use($body,$sheet,$title_array ) {

                    foreach ($body as $i=>$keyName){

                        if($keyName == 'image') { //判断图片列，如果是则放图片

                            $objDrawing = new \PHPExcel_Worksheet_Drawing;

                            $v = public_path(''). array_get($item, $keyName); //拼接图片地址

                            $objDrawing->setPath( $v );

                            $sp = $title_array[$i];

                            $objDrawing->setCoordinates( $sp . ($k+2) );

                            $sheet->setHeight($k+2, 65); //设置高度

                            $sheet->setWidth(array( $sp =>12));  //设置宽度

                            $objDrawing->setHeight(80);

                            $objDrawing->setOffsetX(1);

                            $objDrawing->setRotation(1);

                            $objDrawing->setWorksheet($sheet);

                        } else { //否则放置文字数据

                            $v = array_get($item, $keyName);

                            $sheet->cell($title_array[$i] . ($k+2), function ($cell) use ($v) {

                                $cell->setValue($v);

                            });

                        }

                    }

                });

            });

        })->export('xls');

    }
}