<?php

namespace Admin\Controller;

use Think\Controller;

class ExController extends Controller {

    public function ex() {
//$pc_data_json = file_get_contents('http://s.liansuo.net/pc/');
        $pc_status = file_get_contents('http://baidu.896691.com/index.php/Home/Index/json_sogou_pc');
        $m_status = file_get_contents('http://baidu.896691.com/index.php/Home/Index/json_sogou_m');
        if ($pc_status == 'ok' && $m_status == 'ok') {
            $pc_data_json = file_get_contents('http://baidu.896691.com/data/json/sogou_json_pc.json');
//$m_data_json = file_get_contents('http://s.liansuo.net/phone');
            $m_data_json = file_get_contents('http://baidu.896691.com/data/json/sogou_json_m.json');
            $pc_data = json_decode($pc_data_json, true);
            $m_data = json_decode($m_data_json, true);
            $pc_datacount = count($pc_data);
            $m_datacount = count($m_data);
            Vendor('PHPExcel.PHPExcel');
            Vendor('PHPExcel.PHPExcel.Writer.Excel5');
            $objPHPExcel = new \PHPExcel();
//创建多个sheet
            $objPHPExcel->createSheet();
            $objPHPExcel->createSheet();
            $objPHPExcel->createSheet();
            $objPHPExcel->createSheet();
//设置sheet的name
            $objPHPExcel->setActiveSheetIndex(0);
            $objPHPExcel->getActiveSheet()->setTitle('添加新计划');
            $objPHPExcel->setActiveSheetIndex(1);
            $objPHPExcel->getActiveSheet()->setTitle('添加新推广组');
            $objPHPExcel->setActiveSheetIndex(2);
            $objPHPExcel->getActiveSheet()->setTitle('添加创意（PC招商产品）');
            $objPHPExcel->setActiveSheetIndex(3);
            $objPHPExcel->getActiveSheet()->setTitle('添加创意（无线招商产品）');
            $objPHPExcel->setActiveSheetIndex(4);
            $objPHPExcel->getActiveSheet()->setTitle('添加新关键词');
//生成 添加创意（PC招商产品）sheet
            $objPHPExcel->setActiveSheetIndex(2);
            $objPHPExcel->getActiveSheet()->setCellValue('A' . 1, '推广计划名称（必填）');
            $objPHPExcel->getActiveSheet()->setCellValue('B' . 1, '推广组（必填）');
            $objPHPExcel->getActiveSheet()->setCellValue('C' . 1, '客户名称（必填）');
            $objPHPExcel->getActiveSheet()->setCellValue('D' . 1, '创意ID（必填）');
            $objPHPExcel->getActiveSheet()->setCellValue('E' . 1, '行业一级分类（必填）');
            $objPHPExcel->getActiveSheet()->setCellValue('F' . 1, '行业二级分类（必填）');
            $objPHPExcel->getActiveSheet()->setCellValue('G' . 1, '投资金额（必填）');
            $objPHPExcel->getActiveSheet()->setCellValue('H' . 1, '发源地区（选填）');
            $objPHPExcel->getActiveSheet()->setCellValue('I' . 1, '创意短介绍（必填）');
            $objPHPExcel->getActiveSheet()->setCellValue('J' . 1, '品牌名称（必填）');
            $objPHPExcel->getActiveSheet()->setCellValue('k' . 1, '创意详细介绍（必填）');
            $objPHPExcel->getActiveSheet()->setCellValue('L' . 1, '中间页渠道跳转URL（必填）');
            $objPHPExcel->getActiveSheet()->setCellValue('M' . 1, '中间页图片显示URL（必填）');
            $objPHPExcel->getActiveSheet()->setCellValue('N' . 1, '搜索渠道跳转URL（必填）');
            $objPHPExcel->getActiveSheet()->setCellValue('O' . 1, '搜索图片显示URL（必填）');
            $objPHPExcel->getActiveSheet()->setCellValue('P' . 1, '可加盟地区（选填）');
            $objPHPExcel->getActiveSheet()->setCellValue('Q' . 1, '创意所属公司（选填）');
            $objPHPExcel->getActiveSheet()->setCellValue('R' . 1, '公司地址（选填）');
            for ($i = 0; $i < $pc_datacount; $i++) {
                $j = $i + 2;
                $objPHPExcel->getActiveSheet()->setCellValue('A' . $j, '');
                $objPHPExcel->getActiveSheet()->setCellValue('B' . $j, '');
                $objPHPExcel->getActiveSheet()->setCellValue('C' . $j, $pc_data[$i]["customer"]);
                $objPHPExcel->getActiveSheet()->setCellValue('D' . $j, $pc_data[$i]["id"]);
                $objPHPExcel->getActiveSheet()->setCellValue('E' . $j, $pc_data[$i]["cate_1"]);
                $objPHPExcel->getActiveSheet()->setCellValue('F' . $j, $pc_data[$i]["cate_2"]);
                $objPHPExcel->getActiveSheet()->setCellValue('G' . $j, $pc_data[$i]["money_invest"]);
                $objPHPExcel->getActiveSheet()->setCellValue('H' . $j, '');
                $objPHPExcel->getActiveSheet()->setCellValue('I' . $j, $pc_data[$i]['product_name']);
                $objPHPExcel->getActiveSheet()->setCellValue('J' . $j, $pc_data[$i]["brand_name"]);
                $objPHPExcel->getActiveSheet()->setCellValue('K' . $j, $pc_data[$i]["product_desc"]);
                $objPHPExcel->getActiveSheet()->setCellValue('L' . $j, $pc_data[$i]["url"]);
                $objPHPExcel->getActiveSheet()->setCellValue('M' . $j, $pc_data[$i]["pic_url"]);
                $objPHPExcel->getActiveSheet()->setCellValue('N' . $j, $pc_data[$i]["url2"]);
                $objPHPExcel->getActiveSheet()->setCellValue('O' . $j, $pc_data[$i]["pic_url2"]);
                $objPHPExcel->getActiveSheet()->setCellValue('P' . $j, '');
                $objPHPExcel->getActiveSheet()->setCellValue('Q' . $j, '');
                $objPHPExcel->getActiveSheet()->setCellValue('R' . $j, '');
            }
//生成 添加创意（无线招商产品）sheet
            $objPHPExcel->setActiveSheetIndex(3);
            $objPHPExcel->getActiveSheet()->setCellValue('A' . 1, '推广计划名称（必填）');
            $objPHPExcel->getActiveSheet()->setCellValue('B' . 1, '推广组（必填）');
            $objPHPExcel->getActiveSheet()->setCellValue('C' . 1, '客户名称（必填）');
            $objPHPExcel->getActiveSheet()->setCellValue('D' . 1, '创意ID（必填）');
            $objPHPExcel->getActiveSheet()->setCellValue('E' . 1, '行业一级分类（必填）');
            $objPHPExcel->getActiveSheet()->setCellValue('F' . 1, '行业二级分类（必填）');
            $objPHPExcel->getActiveSheet()->setCellValue('G' . 1, '投资金额（必填）');
            $objPHPExcel->getActiveSheet()->setCellValue('H' . 1, '发源地区（选填）');
            $objPHPExcel->getActiveSheet()->setCellValue('I' . 1, '创意短介绍（必填）');
//$objPHPExcel->getActiveSheet()->setCellValue('J' . 1, '品牌名称（必填）');
            $objPHPExcel->getActiveSheet()->setCellValue('J' . 1, '创意详细介绍（必填）');
            $objPHPExcel->getActiveSheet()->setCellValue('K' . 1, '中间页渠道跳转URL（必填）');
            $objPHPExcel->getActiveSheet()->setCellValue('L' . 1, '中间页图片显示URL（必填）');
            $objPHPExcel->getActiveSheet()->setCellValue('M' . 1, '搜索渠道跳转URL（必填）');
            $objPHPExcel->getActiveSheet()->setCellValue('N' . 1, '搜索图片显示URL（必填）');
            $objPHPExcel->getActiveSheet()->setCellValue('O' . 1, '可加盟地区（选填）');
            $objPHPExcel->getActiveSheet()->setCellValue('P' . 1, '创意所属公司（选填）');
            $objPHPExcel->getActiveSheet()->setCellValue('Q' . 1, '公司地址（选填）');
            for ($i = 0; $i < $m_datacount; $i++) {
                $j = $i + 2;
                $objPHPExcel->getActiveSheet()->setCellValue('A' . $j, '');
                $objPHPExcel->getActiveSheet()->setCellValue('B' . $j, '');
                $objPHPExcel->getActiveSheet()->setCellValue('C' . $j, $m_data[$i]["customer"]);
                $objPHPExcel->getActiveSheet()->setCellValue('D' . $j, $m_data[$i]["id"]);
                $objPHPExcel->getActiveSheet()->setCellValue('E' . $j, $m_data[$i]["cate_1"]);
                $objPHPExcel->getActiveSheet()->setCellValue('F' . $j, $m_data[$i]["cate_2"]);
                $objPHPExcel->getActiveSheet()->setCellValue('G' . $j, $m_data[$i]["money_invest"]);
                $objPHPExcel->getActiveSheet()->setCellValue('H' . $j, '');
                $objPHPExcel->getActiveSheet()->setCellValue('I' . $j, $m_data[$i]['product_name']);
                $objPHPExcel->getActiveSheet()->setCellValue('J' . $j, $m_data[$i]['product_desc']);
                $objPHPExcel->getActiveSheet()->setCellValue('K' . $j, $m_data[$i]["wap_url"]);
                $objPHPExcel->getActiveSheet()->setCellValue('L' . $j, $m_data[$i]["wap_pic_url"]);
                $objPHPExcel->getActiveSheet()->setCellValue('M' . $j, $m_data[$i]["wap_url2"]);
                $objPHPExcel->getActiveSheet()->setCellValue('N' . $j, $m_data[$i]["wap_pic_url2"]);
                $objPHPExcel->getActiveSheet()->setCellValue('O' . $j, '');
                $objPHPExcel->getActiveSheet()->setCellValue('P' . $j, '');
                $objPHPExcel->getActiveSheet()->setCellValue('Q' . $j, '');
            }
            $item = 'sougou';
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="' . $item . '.xls"');  //日期为文件名后缀
            header('Cache-Control: max-age=0');
            $objWriter = new \PHPExcel_Writer_Excel5($objPHPExcel);
            $objWriter->save('php://output');
        } else {
            echo '生成接口出问题了，请尽快联系技术人员！O(∩_∩)O~';
        }
    }

}
