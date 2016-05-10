/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here.
	// For the complete reference:
	// http://docs.ckeditor.com/#!/api/CKEDITOR.config

	// The toolbar groups arrangement, optimized for two toolbar rows.
	config.toolbarGroups = [
		{ name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
		{ name: 'editing',     groups: [ 'find', 'selection', 'spellchecker' ] },
		{ name: 'links' },
		{ name: 'insert' },
		{ name: 'forms' },
		{ name: 'tools' },
		{ name: 'document',	   groups: [ 'mode', 'document', 'doctools' ] },
		{ name: 'others' },
		'/',
		{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
		{ name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ] },
		{ name: 'styles' },
		{ name: 'colors' },
	];

	//默认皮肤
	config.skin = 'moono';
	
	//默认语言
	config.language = 'zh-cn';
	
	// 设置宽高
    //config.width = 400;
    //config.height = 400;
	
	// Remove some buttons, provided by the standard plugins, which we don't
	// need to have in the Standard(s) toolbar.
	config.removeButtons = 'Underline,Subscript,Superscript';

	// Se the most common block elements.
	config.format_tags = 'p;h1;h2;h3;pre';

	// Make dialogs simpler.
	config.removeDialogTabs = 'image:advanced;link:advanced';
		
	// 背景颜色
	//config.uiColor = '#123';
	
	//工具栏是否可以被收缩
	config.toolbarCanCollapse = true;
	//工具栏默认是否展开
    config.toolbarStartupExpanded = true;
	
	//工具栏的位置
    config.toolbarLocation = 'top';//可选：bottom top
	
	//取消“拖拽以改变尺寸”功能 plugins/resize/plugin.js
    config.resize_enabled = true;
	//改变大小的最大高度
    config.resize_maxHeight = 3000;
    //改变大小的最大宽度
    config.resize_maxWidth = 3000;
    //改变大小的最小高度
    config.resize_minHeight = 250;
    //改变大小的最小宽度
    config.resize_minWidth = 750;
	//当提交包含有此编辑器的表单时，是否自动更新元素内的数据
    config.autoUpdateElement = true;
	//设置是使用绝对目录还是相对目录，为空为相对目录
    config.baseHref = '';
	//编辑器的z-index值
    config.baseFloatZIndex = 10000;

	
};
