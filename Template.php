<?php
namespace Yitemplate;

use Yitemplate\Parser;

class Template
{
    //存储注入的变量
    private $_vars = array();
    //存储配置文件
    private $_config = array();

    /**
     * 构造函数
     */
    public function __construct()
    {
        //验证各个目录是否存在s
        if ( !is_dir(TPL_DIR) || !is_dir(TPL_C_DIR) || !is_dir(CACHE) ) {
            exit('ERROR:目录不存在，请添加！');
        }

        //读取系统变量
//        $_root = simplexml_load_file('config/profile.xml');
//        $_tagLib = $_root->xpath('/root/taglib');
//
//        foreach ($_tagLib as $tag) {
//            $this->_config["$tag->name"] = $tag->value;
//        }
    }

    /**
     * 注入变量
     */
    public function assign($_varName, $_varValue)
    {
        //判断变量名称是否为空
        if ( !isset($_varName) || empty($_varName) ) {
            exit('ERROR:变量名不能为空！');
        } else {
            //将变量注入到数组中去
            $this->_vars[$_varName] = $_varValue;
        }
    }

    /**
     * 显示模板文件
     */
    public function display($filename)
    {
        //获取模板路径
        $_tplPath = TPL_DIR . $filename;

        //判断模板文件是否存在
        if ( !file_exists($_tplPath) ) {
            exit('ERROR:模板文件不存在！');
        }

        //编译后文件的文件名
        $_compileName = TPL_C_DIR . md5($filename) . $filename . '.php';
        //缓存文件的文件名
        $_cacheFileName = CACHE . md5($filename) . $filename . '.html';


        //第二次载入相同文件，直接载入缓存文件
        if ( IS_CACHE ) {
            //判断缓存文件和编译文件是否都存在,如果都存在则直接执行缓存文件
            if ( file_exists($_cacheFileName) && file_exists($_compileName) ) {
                //判断模板文件和编译文件是否修改
                if ( filemtime($_compileName) >= filemtime($_tplPath) && filemtime($_cacheFileName) >= filemtime($_compileName) ) {
                    include $_cacheFileName;
                    return;
                }
            }
        }


        //如果编译文件还不存在，或者模板文件被修改（检查最后修改时间），则重新生成编译文件
        if ( !file_exists($_compileName) || filemtime($_compileName) < filemtime($_tplPath) ) {
            //引入解析类

            //声明类的时候，传入模板文件路径
            $parser = new Parser($_tplPath);
            //调用解析方法的时候，传入编译文件名称
            $parser->compile($_compileName);
        }


        //载入编译文件，载入后已经输出到浏览器
        include $_compileName;

        //是否开启了缓存
        if ( IS_CACHE ) {
            //接受缓冲文件，并生成缓存文件
            file_put_contents($_cacheFileName, ob_get_contents());

            //清除缓冲区,意思就是清除了编译文件加载的内容
            ob_end_clean();

            //载入缓存文件，直接输出到浏览器
            include $_cacheFileName;
        }
    }
}