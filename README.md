# LAZYPHP简介

LazyPHP（以下简称LP）是一个轻框架.

之所以开发这么一个框架，是因为其他框架给的太多。在高压力的情况下，ORM和盘根错节的对象树反而将简单的页面请求处理复杂化，在调试和性能上带来反面效果。

LP采用函数式接口封装对象，对内通过面向对象实现代码重用，对外则提供简明扼要的操作函数。开发者甚至不用理解面向对象就能很好的使用，这让一些初级程序员很容易就开发出强壮的应用。

在数据库等模块的加载上，LP采用LazyLoad方式，并用$GLOBALS实现全局单件，在方便和高效之间找到了一个平衡点。这也是LP框架名字中Lazy的来源。
LP在新浪大量使用已经将近3年，每天承载的请求达千万级别。由于LP易读易学，使用LP的开发者之间沟通非常容易，而新同事也可以很快融入进来。

LP3是LP最新的版本，最主要的调整是重新定义了Layout规则，以应对日益增多的Ajax，Mobile和Rest请求。同样是由于这个原因，LP3和之前的版本不兼容，我们建议大家在新项目中采用LP3。

# LP3 实例
基于LP3的全平台开源项目 团队效率工具 TeamToy  http://teamtoy.net/

# LP3简明教程

LP是一个轻框架。它只是打算帮你处理掉每个Web应用都需要重新开始的那部分东西，并不打算成为一个大而全的Lib（我说的真的不是ZendFrameWork）。

LP只包含一个FrontController+Layout系统+20个常用函数。你只需要花上10分钟了解这些东西，就能完全得掌握LP。

## FRONTCONTROLLER
FrontController（以下简称FC）翻译过来叫前端控制器，在LP中，所有的动态请求（不包括静态文件）都会经过FC。使用FC的好处是可以统一控制全部请求，举例而言，你只需要在FC中添加几行代码，就可以精确控制哪些controller和action不可以访问。

LP3的FC你可以看成就是ROOT/index.php(实际上分发逻辑在_lp/lp.init.php),所有的请求都在这里处理。不管你是用户登录还是浏览文章，在LP上用户访问的页面都是index.php。

FC根据Controller和Action对请求进行分组，并调用对应的模块来进行处理。如何定义Controller和Action？最简单的办法是把一个数据表对应到一个Controller，而对这个数据表的相关操作自然就成为了Action。

比如，我们会定义一个叫做User的Controller，而把Login，Logout，Detail 作为User的Action。

我们在访问LP时，把要请求的Controller和Action通过参数c和a传递给FC。

```php
/index.php?c=user&a=login
```

上边这个访问告诉FC去加载名为User的Controller，并调用名为Login的方法。

在实现上，Controller被放到AROOT.controller目录下，以Class形式存在，而Action就是这个Class的一个方法。

下边几行伪代码描述了这个其实很简单的过程：

```php
// FC取得参数
$c = $_REQUEST['c'] ;
$a = $_REQUEST['a'] ;

// controller文件名和Class名
$cont_file = AROOT . ‘controller/’ . $c . ‘/’ . $a . ‘.class.php’;
$class_name =$c .’Controller’ ;

// 载入文件
require_once( $cont_file );

// 调用方法
$o = new $class_name;
call_user_func( array( $o , $a ) );
```

实际上，编写应用的过程就是不断的添加Controller和Action并把它实现。

下边是一个Controller Class的样子：

```php
class defaultController extends appController
{
	function __construct()
	{
		parent::__construct();
	}

	function index()
	{
		$data['title'] = $data['top_title'] = ‘首页’;
		render( $data );
	}
}
```

## MVC和LAYOUT
LP是遵循MVC模式的，它的业务逻辑和显示逻辑是完全分离的。Controller处理了业务逻辑，我们使用模板来处理显示逻辑。

LP所有的模板都被放在AROOT.view下边，通过在Controller中使用Render函数来渲染模板。

以下是Render函数的伪代码：

```php
function render( $data = NULL , $layout = NULL , $sharp = ‘default’ )
{
$layout_file = AROOT . ‘view/layout/’ . $layout . ‘/’ . $sharp . ‘.tpl.html’;
@extract( $data );
require( $layout_file );
}
```

可以看到，Render接受一个$data数组，然后将数组中的数据extract出来，这样一个原本是$data['user']的数据在模板里边就能通过$user访问了。而载入模板部分更简单，只是直接require。这是因为LP直接使用PHP来做模板的解释引擎。我们推荐大家在模板中采用PHP的短标签语法以保持高的可读性：

```php
<?php if( $user['level'] >= 9 ): ?>
亲，你是鹳狸猿
<?php else: ?>
亲，你是平民，天黑请闭眼
<?php endif; ?>
```

上边是一个使用短标签的模板的例子。if，foreach都可以这样写。切忌在模板中使用{}，这会让你的模板看起来很奇葩。

为了实现模板的重用，我们引入了Layout系统。

来看一个经常遇到的例子，网站头部导航和页脚版权部分的重用。

最简单和最容易想到的处理办法是这样的：将头部保存为header.tpl.html，将页脚保存为footer.tpl.html。然后直接在模板中用include函数载入即可。

这样很OK，但是当我们有10个模板要处理的时候，你会发现每个模板都要去include header和footer。而在这些模板中，header和footer其实是不变的，变的是中间的部分。

于是我们为这些相同模板建立一个通用的模板文件，叫做sharpA.tpl.html，在styleA中我们指定好header和footer，然后sharpA根据FC接收到的C和A变量（还记得吧）去加载对应子目录下模板。这样我们只需要创建C和A对应的模板就可以了。下边是一个典型的sharp模板。
<pre>
&lt;html&gt;
&lt;body&gt;
&lt;div id="hd" &gt;&lt;?php @include_once( dirname(__FILE__) ) . DS . 'header.tpl.html'; ?&gt;&lt;/div&gt;

&lt;div id="bd"&gt;
&lt;div id="side"&gt;
&lt;?php
include( AROOT . 'view' . DS . 'layout' . DS . g('layout') . DS . 'side' . DS . g('c') . DS . g('a') . '.tpl.html' );

?&gt;
&lt;/div&gt;
&lt;div id="main"&gt;
&lt;?php
include( AROOT . 'view' . DS . 'layout' . DS . g('layout') . DS . 'main' . DS . g('c') . DS . g('a') . '.tpl.html' );
?&gt;
&lt;/div&gt;
&lt;/div&gt;
&lt;/div&gt;
&lt;div id="ft"&gt;&lt;?php @include_once( dirname(__FILE__) ) . DS . 'footer.tpl.html'; ?&gt;&lt;/div&gt;

&lt;/body&gt;
&lt;/html&gt;
</pre> 

当一个sharp满足不了需求时，我们可以再创建sharpB.tpl.html。styleB可以选择性的共享sharpA的header和footer，也可以载入自己特定的header。我们把sharpA，sharpB…等通用模板放到一个目录下，叫做一个Layout。

目前Layout按照访问方式分为Web，Ajax，Mobile和Rest四种。当你为你的游戏机或者电视机创建一组特定风格的sharp模板时，你可以创建一个名叫TV或者PFP的Layout目录。

切换Sharp和Layout非常简单，只需要修改Render函数中第二和第三个参数就可以了。在没有指定的情况下，LP3会启用Layout下的default sharp模板，同时还会根据请求的方式，自动加载Web，Mobile或者Ajax Layout。

再回过头来说MVC，我们已经了解了C和V在LP的使用。而M就是根据请求参数，从数据库或者其他地方取得数据的过程。在LP3之前，数据是直接在Controller中查询数据库取得的。

```php
function show()

{

$uid =  intval($_REQUEST['uid']);

if( $uid < 1 ) return info_page(‘错误的uid’);

$data['user'] = get_line( “SELECT * FROM `user` WHERE `uid` = ‘” . $uid . “‘ LIMIT 1″ );

render( $data );

}

```

这种方式将SQL散落在各个Action中，不利于重用和修改。所以在LP3中，我们采用专门的model文件来放置Controller中用到的数据操作函数。还是用上边的例子，我们假设这是一个名为User的Controller的Show Action。那么在LP3中我们推荐的做法如下：

首先在 AROOT/model目录下创建一个名为user.function.php的文件。

然后在文件中写入获取用户信息的函数：

```php
function get_user_info_by_id( $uid )

{

return get_line( “SELECT `name` ,`email` , `bod` FROM `user` WHERE `uid` = ” . intval($uid) . ” LIMIT 1 ” )

}
```

user.function.php将在请求参数包含?c=user 时自动加载。所以我们可以把show改为下边的样子：

```php
function show()

{

$uid =  intval($_REQUEST['uid']);

if( $uid < 1 ) return info_page(‘错误的uid’);

$data['user'] = get_user_info_by_id(  $uid  );

render( $data );

}
```

这样在其他的Action，比如User/settings 中，我们可以通过get_user_info_by_id 函数重用代码。通过函数封装重复SQL还有一个好处是方便对SQL进行统一处理，加手工Cache就是一个经常能遇到的需求。

## 常用函数
LP3中的函数主要有3类，迅捷函数，功能函数和数据库函数，一共20个左右。

### 迅捷函数

迅捷函数是一系列的函数缩写：

<pre>
function c( $str ) // 读取配置文件中$str为key的对应的value
function v( $str ) // 取得 $_REQUEST[$str] 的数据，不存在不会报warning
function z( $str ) // strip_tags
function g( $str ) // 取得 $GLOBALS[$str] 的数据
function t( $str ) // trim
function u( $str ) // urlencode
</pre>

### 功能性函数

<pre>
function render( $data = NULL , $layout = NULL , $style = ‘default’ ) // Layout
function info_page( $info ) // 系统提示信息
function ajax_echo( $info ) // 输出提示信息，包含永不过期的header
function uses( $file ); // 载入lib目录下的文件
</pre>

### 数据库函数

<pre>
function s( $str , $db = NULL ) // mysql_real_escape_string
function prepare( $sql , $array ) // 将数组中的变量顺序替换SQL中的？
function db() // 使用config目录下的数据库设置,创建并返回数据库链接
function get_data( $sql , $db = NULL ) // 以二维数组的方式返回$sql对应的结果
function get_line( $sql , $db = NULL ) // 以一维数组的方式返回$sql对应的单行结果
function get_var( $sql , $db = NULL ) // 以变量的方式返回一个数值
function last_id( $db = NULL ) // last id
function run_sql( $sql , $db = NULL ) // 运行sql,不返回结果集
function db_error() // 数据库错误信息
function db_errno() // 数据库错误编号
function close_db( $db ) // 显式关闭数据库链接
</pre>

### 特别说明

其中要详细说明的有两个：

#### C($KEY)和配置文件

LP将应用配置信息保存在AROOT/config/app.config.php下，使用$GLOBALS['config']超全局变量以数组形式保存。使用c($key)的方式，可以在MVC各个地方获取。

#### PREPARE()函数

这个函数是LP3新引入的，主要是希望减少SQL注入的问题。使用方式如下：

<pre>
echo $sql = prepare( “SELECT * FROM `user` WHERE `name` = ?s AND `uid` = ?i AND `level` = ?s LIMIT 1″ , array( “Easy’” , ‘-1′, ’9.56′ ) );
</pre>

输出结果为：

<pre>
SELECT * FROM `user` WHERE `name` = ‘Easy\” AND `uid` = ‘-1′ AND `level` = ’9.56′ LIMIT 1
</pre>

使用prepare函数时要注意：SQL必须使用双引号，【?i】表示整数，【?s】表示整数以外的其他值。prepare会无例外的mysql_real_escape_string，然后在两边加上单引号。

## CSS，JAVASCRIPT和AJAX

LP3采用BootStrap这个流行的前端框架，[你可以从这里看到它的详细介绍](http://twitter.github.com/bootstrap/)。

JavaScript库上，LP3开始换为JQuery。[这里是JQuery API的参考手册](http://api.jquery.com/)。

为了方便不熟悉的同学也能使用好Ajax，LP3自己实现了Ajax传输数据的JS函数。这些函数都放在AROOT/static/script/app.js中。

```javascript
$(‘#标签ID’).load(‘URL’);  // 是由JQuery自身实现的，可以方便的无刷新载入页面。

send_form_in( ‘FROMID’ ); // 将form表单中的数据通过Ajax提交（file类型除外），并将服务器返回的HTML显示在Form表单顶部

send_form_pop(‘FROMID’); //  将form表单中的数据通过Ajax提交（file类型除外），并将服务器返回的HTML显示在浮动图层中
```

好了，这里就是关于LP3 的一切了，希望LP3能让你更快的完成工作。
