easyspider
==========
  A simple spider framework.

### 安装方法

    git clone https://github.com/shanhuhai/easyspider.git
    cd easyspider/bin
    php createTask.php taskName.conf(任务配置文件)
    cd ../task/taskName/
    
> 详细用法请参考demo

### 任务配置文件格式
    
	配置文件的语法为yaml 1.0 demo 位置 bin/conf/demo.conf

	taskName: 任务名称
	domain: 任务目标网站地址后面不要加 /
	charset: 任务目标网站编码
	fileDomain: 远程文件本地话后本地使用的域
	lists: 多个列表分多行，从左到右用` ``` ` 分割，依次为
	列表地址规则、列表首页地址、采集页数、源名称、本地名称、本地catid
	 
	parseCss: 获取list时用到的css Dom解析规则，与jQuery Sizzle 规则一致，如果为空则调用TaskList::parseList()方法
	listType: 源列表是json还是html
	updateEnd: 在更新模式下要抓取几页数据，开始更新模式在 tasks/demo/config.php 中将` EP_UPDATE_MODE ` 设为true 
	dislocation: 页数和分页实际需要错位的个数
	fields:  需要抓取字段，系统默认提供了title（标题）source（源地址）thumb_source(缩略图源地址) thumb（本地化后缩略图的地址）created（内容抓取时间）catid（本地的栏目id），在TaskPage::saveData()方法下$data 中将包含上数据，你可以在入库前将不需要的数据unset掉

### demo
	cd task/demo
	php fetchList.php //抓取列表
	php fetchPage.php //抓取内页

注：如果在常量debug 为 true 时执行脚本将会返回抓取的第一条记录的内容
	
### 文件描述

    在任务目录下
    config.php 为整个任务的配置文件
    fetchList.php 抓取列表脚本
    fetchPage.php 抓取内页脚本
    update.php 更新模式下的抓取脚本
    lib/tasklist.php 当前任务实现的列表抓取类
    lib/taskPage.php 当前任务实现的内页抓取类
