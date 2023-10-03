<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
/**
 * 文章内容超链接重定向
 * 
 * @package RedirectLinks/
 * @author 地主非
 * @version 1.0.0
 * @link https://www.myhelen.cn
 */
class RedirectLinks_Plugin implements Typecho_Plugin_Interface
{
    public static function activate()
    {
        Typecho_Plugin::factory('Widget_Abstract_Contents')->contentEx = array(__CLASS__, 'redirectLinks');
    }

    public static function deactivate()
    {
    }

    public static function config(Typecho_Widget_Helper_Form $form)
    {
        // 创建表单字段
        $domains = new Typecho_Widget_Helper_Form_Element_Textarea('domains', NULL, '', '需要排除的域名', '输入需要排除重定向的域名，每行一个');
        $form->addInput($domains);
    }

    public static function personalConfig(Typecho_Widget_Helper_Form $form)
    {
    }

    public static function redirectLinks($content, $widget, $lastResult)
    {
        $domains = Helper::options()->plugin('RedirectLinks')->domains;

        // 获取需要排除的域名列表
        $excludedDomains = explode("\n", $domains);
        $excludedDomains = array_map('trim', $excludedDomains);

        // 获取当前站点的域名
        $currentDomain = $_SERVER['HTTP_HOST'];

        // 构建正则表达式模式，匹配所有非当前域名链接
        $pattern = '/<a(.*?)href=["\'](https?:\/\/(?!' . preg_quote($currentDomain) . ')[^\s"\']+)["\'](.*?)>/i';

        $content = preg_replace_callback($pattern, function ($matches) use ($excludedDomains) {
            $link = $matches[2];

            // 排除需要排除的域名
            $excluded = false;
            foreach ($excludedDomains as $domain) {
                if (!empty($domain) && strpos($link, $domain) !== false) {
                    $excluded = true;
                    break;
                }
            }

            if (!$excluded) {
                // 生成重定向链接，跳转到新创建的页面 `redirected.php`
                $redirectUrl = htmlspecialchars_decode($link, ENT_QUOTES);
                $redirectUrl = str_replace("&amp;", "&", $redirectUrl);
				$currentDomain = $_SERVER['HTTP_HOST'];
                $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
				return '<a' . $matches[1] . 'href="' . $protocol . $currentDomain . '/redirected.php?url=' . urlencode($redirectUrl) . '"' . $matches[3] . ' target="_blank">';

            } else {
                return $matches[0];
            }
        }, $content);

        return $content;
    }
}
