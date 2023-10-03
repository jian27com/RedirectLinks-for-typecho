<?php
// 获取 URL 参数中的原始地址
$redirectUrl = $_GET['url'];
?>

<!-- 在此处添加你的网站流量统计代-->
<script>
var _hmt = _hmt || [];
(function() {
  var hm = document.createElement("script");
  hm.src = "https://hm.baidu.com/hm.js?53e28d97e89d589353531642cc0266f3";
  var s = document.getElementsByTagName("script")[0]; 
  s.parentNode.insertBefore(hm, s);
})();
</script>
<?php
header("Location: " . $redirectUrl);
exit();
?>
