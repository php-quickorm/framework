<?php
namespace System\Utilities;
/**
 * PHP-QuickORM 异常处理类
 * @author Rytia <rytia@outlook.com>
 * @copyright 2018 PHP-QuickORM
 */

class Exception
{
    /**
     * @param $errNo
     * @param $errStr
     * @param $errFile
     * @param $errLine
     * @uses 分发错误信息
     * @return boolean
     */
    public static function errorReport($errNo, $errStr, $errFile, $errLine){
        // 对路径进行过滤，绝对路径修改为相对路径
        $errFile = str_replace(getcwd(),"",$errFile);
        $errStr = str_replace(getcwd(),"",$errStr);

        // 错误分发
        switch ($errNo) {
            case E_ERROR:
            case E_USER_ERROR:
                (new static())->errorReportHtml($errNo, $errStr, $errFile, $errLine, "ERROR", "red");
                // 终止运行
                die();
                break;

            case E_WARNING:
            case E_USER_WARNING:
                (new static())->errorReportHtml($errNo, $errStr, $errFile, $errLine, "WARNING", "coral");
                break;

            case E_NOTICE:
            case E_USER_NOTICE:
                (new static())->errorReportHtml($errNo, $errStr, $errFile, $errLine, "NOTICE", "blue");
                break;

            default:
                (new static())->errorReportHtml($errNo, $errStr, $errFile, $errLine, "PHP ERROR:[$errNo]", "");
                break;
        }
        // 继续交由 PHP 内置的错误机制处理(处理 php log 方便)
        return false;
    }

    public static function exceptionReport($e){
        $errFile = str_replace(getcwd(),"",$e->getFile());
        $errStr = str_replace(getcwd(),"",$e->getMessage());
        $errTrace = str_replace(getcwd(),"",$e->getTraceAsString());
        (new static())->exceptionReportHtml($errTrace, $errStr, $errFile, $e->getLine(),"Exception", "indianred");
        return true;
    }


    /**
     * @param $errNo
     * @param $errStr
     * @param $errFile
     * @param $errLine
     * @param $type
     * @param $color
     * @uses 将错误信息整理并输出页面
     * 这是我写的最无语的一个地方，在生成HTML的部分有点狗血，勿见笑
     */
    public function errorReportHtml($errNo, $errStr, $errFile, $errLine, $type, $color){
        // 拼接 HTML
        $output = "<div style='text-align: center; line-height:2.5rem; margin:13% 0 5% 0;background: #ffffff; color:#333; font-weight:300; font-size: 1.2rem; letter-spacing: 2px'>";
        $output .= "<p style='font-size: 130%'> <span style='color: $color;'>$type</span> $errStr</p>";
        $output .= "Fatal error on line <span style='font-weight:400'>$errLine</span> in file <span style='font-weight:400'>$errFile</span><br>";
        $output .= "PHP " . PHP_VERSION . " (" . PHP_OS . ") <br>";
        $output .= "<p style='font-size: 80%'>Powered by PHP-QuickORM</p>";
        $output .= "</div>";
        // 显示页面
        echo $output;

    }

    /**
     * @param $errNo
     * @param $errStr
     * @param $errFile
     * @param $errLine
     * @param $type
     * @param $color
     * @uses 将异常信息整理并输出页面
     * 这是我写的最无语的一个地方，在生成HTML的部分有点狗血，勿见笑
     */
    public function exceptionReportHtml($errTrace, $errStr, $errFile, $errLine, $type, $color){
        // 拼接 HTML
        $output = "<div style='text-align: center; line-height:2.5rem; margin:8% 0 5% 0;background: #ffffff; color:#333; font-weight:300; font-size: 1.2rem; letter-spacing: 2px'>";
        $output .= "<p style='font-size: 130%'> <span style='color: $color;'>$type</span></p>";
        $output .= "$errStr<br>";
        $output .= "Fatal error on line <span style='font-weight:400'>$errLine</span> in file <span style='font-weight:400'>$errFile</span><br>";
        $output .= "PHP " . PHP_VERSION . " (" . PHP_OS . ") <br>";
        $output .= "<pre style='text-align: left; padding: 1rem; background: #f8f8f8; width: 61.8%; margin:2rem auto; overflow-y: overlay'>$errTrace</pre>";
        $output .= "<p style='font-size: 80%'>Powered by PHP-QuickORM</p>";
        $output .= "</div>";
        // 显示页面
        echo $output;

    }
}