<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2023/3/14 0014
 * Time: 11:57
 */
class DownSummary{

    protected $objPHPExcel;

    protected $current_row = 0;
    protected $header_title;
    protected $header_string;

    public function SetHeaderTitle($invalue) {
        $this->header_title = $invalue;
    }

    public function SetHeaderString($invalue) {
        $this->header_string = $invalue;
    }

    public function init() {
        //Yii::$enableIncludePath = false;
        $phpExcelPath = Yii::getPathOfAlias('ext.phpexcel');
        spl_autoload_unregister(array('YiiBase','autoload'));
        include($phpExcelPath . DIRECTORY_SEPARATOR . 'PHPExcel.php');
        $this->objPHPExcel = new PHPExcel();
        $this->setReportFormat();
        $this->outHeader();
    }

    public function setSummaryHeader($headerArr){
        $this->setSummaryWidth();
        if(!empty($headerArr)){
            $this->objPHPExcel->getActiveSheet()->mergeCells("A".$this->current_row.':'."A".($this->current_row+1));
            $this->objPHPExcel->getActiveSheet()->getStyle("A{$this->current_row}:Y".($this->current_row+1))->applyFromArray(
                array(
                    'font'=>array(
                        'bold'=>true,
                    ),
                    'alignment'=>array(
                        'horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        'vertical'=>PHPExcel_Style_Alignment::VERTICAL_CENTER,
                    ),
                    'borders'=>array(
                        'allborders'=>array(
                            'style'=>PHPExcel_Style_Border::BORDER_THIN,
                        ),
                    )
                )
            );
            $colOne = 0;
            $colTwo = 1;
            foreach ($headerArr as $list){
                $startStr = $this->getColumn($colOne);
                $colspan = key_exists("colspan",$list)?count($list["colspan"])-1:0;
                $this->objPHPExcel->getActiveSheet()
                    ->setCellValueByColumnAndRow($colOne, $this->current_row, $list["name"]);
                $colOne+=$colspan;
                $colOne++;
                $endStr = $this->getColumn($colOne-1);
                if(!empty($colspan)){
                    $this->objPHPExcel->getActiveSheet()->mergeCells($startStr.$this->current_row.':'.$endStr.$this->current_row);
                }
                if(key_exists("background",$list)){
                    $background = $list["background"];
                    $background = end(explode("#",$background));
                    $this->setHeaderStyleTwo("{$startStr}{$this->current_row}:{$endStr}{$this->current_row}",$background);
                }
                if(isset($list["colspan"])){
                    foreach ($list["colspan"] as $item){
                        $this->objPHPExcel->getActiveSheet()
                            ->setCellValueByColumnAndRow($colTwo, $this->current_row+1, $item["name"]);
                        $colTwo++;
                    }
                }
            }

            $this->current_row+=2;
        }
    }

    private function setSummaryWidth(){
        for ($col=0;$col<18;$col++){
            $width = 13;
            $this->objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($col)->setWidth($width);
        }
    }

    public function setSummaryData($data){
        if(key_exists("MO",$data)){//是否有澳門地區的數據
            $moData=$data["MO"];
            unset($data["MO"]);
        }else{
            $moData = array();
        }
        if(!empty($data)){
            foreach ($data as $region=>$regionList){
                if(isset($regionList["list"])&&!empty($regionList["list"])){
                    foreach ($regionList["list"] as $city=>$cityList){
                        $col = 0;
                        foreach ($cityList as $text){
                            $this->setCellValueForSummary($col, $this->current_row, $text);
                            $col++;
                        }
                        $this->current_row++;
                    }
                }
                //合计
                $col = 0;
                foreach ($regionList["count"] as $text){
                    $this->setCellValueForSummary($col, $this->current_row, $text);
                    $col++;
                }
                $this->objPHPExcel->getActiveSheet()
                    ->getStyle("A{$this->current_row}:Y{$this->current_row}")
                    ->applyFromArray(
                        array(
                            'font'=>array(
                                'bold'=>true,
                            ),
                            'borders' => array(
                                'top' => array(
                                    'style' => PHPExcel_Style_Border::BORDER_THIN
                                )
                            )
                        )
                    );
                $this->current_row++;
                $this->current_row++;
            }
        }

        if(!empty($moData)){
            $col = 0;
            foreach ($moData as $text){
                $this->setCellValueForSummary($col, $this->current_row, $text);
                $col++;
            }
        }
    }

    private function setCellValueForSummary($col,$row,$text){
        $this->objPHPExcel->getActiveSheet()
            ->setCellValueByColumnAndRow($col, $row, $text);
        if (strpos($text,'%')!==false){
            $number = floatval($text);
            if($number>=100){
                $str = $this->getColumn($col);
                $this->objPHPExcel->getActiveSheet()
                    ->getStyle($str.$row)->applyFromArray(
                        array(
                            'font'=>array(
                                'color'=>array('rgb'=>'00a65a')
                            )
                        )
                    );
            }
        }
    }

    protected function setReportFormat() {
        $this->objPHPExcel->getDefaultStyle()->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $this->objPHPExcel->getDefaultStyle()->getFont()
            ->setSize(10);
        $this->objPHPExcel->getDefaultStyle()->getAlignment()
            ->setWrapText(true);
        $this->objPHPExcel->getActiveSheet()->getDefaultRowDimension()
            ->setRowHeight(-1);
    }

    protected function outHeader($sheetid=0){
        $this->objPHPExcel->setActiveSheetIndex($sheetid)
            ->setCellValueByColumnAndRow(0, 1, $this->header_title)
            ->setCellValueByColumnAndRow(0, 2, $this->header_string);
        $this->objPHPExcel->getActiveSheet()->mergeCells("A1:C1");
        $this->objPHPExcel->getActiveSheet()->mergeCells("A2:C2");
        $this->objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0, 1)->getFont()
            ->setSize(14)
            ->setBold(true);
        $this->objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0, 1)->getAlignment()
            ->setWrapText(false);
        $this->objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0, 2)->getFont()
            ->setSize(12)
            ->setBold(true)
            ->setItalic(true);
        $this->objPHPExcel->getActiveSheet()->getStyleByColumnAndRow(0, 2)->getAlignment()
            ->setWrapText(false);

        $this->current_row = 4;
    }

    public function outExcel($name="summary"){
        $objWriter = PHPExcel_IOFactory::createWriter($this->objPHPExcel, 'Excel2007');
        ob_start();
        $objWriter->save('php://output');
        $output = ob_get_clean();
        spl_autoload_register(array('YiiBase','autoload'));
        $time=time();
        $str="{$name}_".$time.".xlsx";
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.ms-execl");
        header("Content-Type:application/octet-stream");
        header("Content-Type:application/download");;
        header('Content-Disposition:attachment;filename="'.$str.'"');
        header("Content-Transfer-Encoding:binary");
        echo $output;
    }

    protected function setHeaderStyleTwo($cells,$color="AFECFF") {
        $styleArray = array(
            'font'=>array(
                'bold'=>true,
            ),
            'alignment'=>array(
                'horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical'=>PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'borders'=>array(
                'allborders'=>array(
                    'style'=>PHPExcel_Style_Border::BORDER_THIN,
                ),
            ),
            'fill'=>array(
                'type'=>PHPExcel_Style_Fill::FILL_SOLID,
                'startcolor'=>array(
                    'argb'=>$color,
                ),
            ),
        );
        $this->objPHPExcel->getActiveSheet()->getStyle($cells)
            ->applyFromArray($styleArray);
    }
    protected function getColumn($index){
        $index++;
        $mod = $index % 26;
        $quo = ($index-$mod) / 26;

        if ($quo == 0) return chr($mod+64);
        if (($quo == 1) && ($mod == 0)) return 'Z';
        if (($quo > 1) && ($mod == 0)) return chr($quo+63).'Z';
        if ($mod > 0) return chr($quo+64).chr($mod+64);
    }
}