<?php
namespace Yelehair\Yitemplate;

class Parser
{
    //存储模板内容
    private $_tplcontent;

    /**
     * 构造函数，获取模板内容
     * @param unknown $_tplPath
     */
    public function __construct($_tplPath)
    {
        if ( !$this->_tplcontent = file_get_contents($_tplPath) ) {
            exit('ERROR:获取模板文件出错！');
        }
    }

    //解析普通变量
    private function parVar()
    {
        //替换变量的正则表达式
        $_patten = '/\{\$([\w]+)\}/';
        //如果匹配成功，则替换变量
        if ( preg_match($_patten, $this->_tplcontent) ) {
            //[ \1 ],正则表达式，得到替换的内容。
            $this->_tplcontent = preg_replace($_patten, '<?php echo $this->_vars["\1"];?>', $this->_tplcontent);
        } else {
            exit($this->_tplcontent);
        }
    }

    /**
     * 解析if语句
     */
    private function parIf()
    {
        $_varStartIf    = '/\{if\s+\(\$([\w]+)\)\}/';
        $_varMidlleElse = '/\{else\}/';
        $_varEndIf      = '/\{\/if\}/';

        if ( preg_match($_varStartIf, $this->_tplcontent) ) {
            if (preg_match($_varEndIf, $this->_tplcontent) ) {
                //替换头if
                $this->_tplcontent = preg_replace($_varStartIf, '<?php if ($this->_vars["\1"]) { ?>', $this->_tplcontent);
                //替换尾if
                $this->_tplcontent = preg_replace($_varEndIf, '<?php } ?>', $this->_tplcontent);
                //替换 else
                if ( preg_match($_varMidlleElse, $this->_tplcontent) ) {
                    $this->_tplcontent = preg_replace($_varMidlleElse, '<?php } else { ?>', $this->_tplcontent);
                }
            } else {
                exit('ERROR：if语句没有关闭！');
            }
        }
    }

    /**
     * 解析Foreach
     */
    private function parForeach()
    {
        //$_pattenStartForeach  = '/\{foreach\s+\$([\w]+)\(([\w]+),([\w]+)\)\}/';
        $_pattenStartForeach  = '/\{foreach\s+\$([\w]+)\(\s*([\w]+)\s*,\s*([\w]+)\s*\)\}/';
		$_pattenMiddleForeach = '/\{@([\w]+)\}/';
        $_pattenEndForeach    = '/\{\/foreach\}/';
        if ( preg_match($_pattenStartForeach, $this->_tplcontent) ) {
            if ( preg_match($_pattenEndForeach, $this->_tplcontent) ) {
                //替换开头和结尾
                $this->_tplcontent = preg_replace($_pattenStartForeach, '<?php foreach ($this->_vars["\1"] as $\2=>$\3) { ?>', $this->_tplcontent);
                $this->_tplcontent = preg_replace($_pattenEndForeach, '<?php } ?>', $this->_tplcontent);

                //替换中间内容
                if ( preg_match($_pattenMiddleForeach, $this->_tplcontent) ) {
                    $this->_tplcontent = preg_replace($_pattenMiddleForeach, '<?php echo $\1; ?>', $this->_tplcontent);
                }
            } else {
                exit('ERROR:foreach标签没有关闭！');
            }
        }
    }

    /**
     * 解析include
     */
    private function parInclude()
    {
        $_pattenInclude = '/\{#include\s+file=\"([\w\.\-]+)\"\}/';

        if ( preg_match($_pattenInclude, $this->_tplcontent,$_file) ) {
            //判断被包含文件是否存在
            if ( !file_exists($_file[1]) || empty($_file[1]) ) {
                exit('ERROR:包含文件出错！');
            }

            //替换为PHP代码
            $this->_tplcontent = preg_replace($_pattenInclude, '<?php include "\1"; ?>', $this->_tplcontent);
        }
    }

    /**
     * 解析系统变量
     */
    private function parConfig()
    {
        $_pattenConfig = '/<!--\{([\w]+)\}-->/';
        if (preg_match($_pattenConfig, $this->_tplcontent) ) {
            $this->_tplcontent = preg_replace($_pattenConfig, '<?php echo $this->_config["\1"]; ?>', $this->_tplcontent);
        }
    }

    /**
     * 解析注释
     */
    private function parCommon()
    {
        $_patten = '/\{#\}(.*)\{#\}/';
        if ( preg_match($_patten, $this->_tplcontent) ) {
            $this->_tplcontent = preg_replace($_patten, '<?php /* \1 */ ?>', $this->_tplcontent);
        }
    }

    /**
     * 解析文件方法
     */
    public function compile($_compileName)
    {
        //解析普通变量
        $this->parVar();
        //解析if语句
        $this->parIf();
        //解析include
        $this->parInclude();
        //解析系统变量
        $this->parConfig();
        //解析注释
        $this->parCommon();
        //解析foreach
        $this->parForeach();
        //经过解析变量，最后生成编译文件
        if ( !file_put_contents($_compileName, $this->_tplcontent) ) {
            exit('ERROR:编译文件出错！');
        }
    }
}