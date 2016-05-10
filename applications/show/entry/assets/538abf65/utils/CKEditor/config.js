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

	//Ĭ��Ƥ��
	config.skin = 'moono';
	
	//Ĭ������
	config.language = 'zh-cn';
	
	// ���ÿ��
    //config.width = 400;
    //config.height = 400;
	
	// Remove some buttons, provided by the standard plugins, which we don't
	// need to have in the Standard(s) toolbar.
	config.removeButtons = 'Underline,Subscript,Superscript';

	// Se the most common block elements.
	config.format_tags = 'p;h1;h2;h3;pre';

	// Make dialogs simpler.
	config.removeDialogTabs = 'image:advanced;link:advanced';
		
	// ������ɫ
	//config.uiColor = '#123';
	
	//�������Ƿ���Ա�����
	config.toolbarCanCollapse = true;
	//������Ĭ���Ƿ�չ��
    config.toolbarStartupExpanded = true;
	
	//��������λ��
    config.toolbarLocation = 'top';//��ѡ��bottom top
	
	//ȡ������ק�Ըı�ߴ硱���� plugins/resize/plugin.js
    config.resize_enabled = true;
	//�ı��С�����߶�
    config.resize_maxHeight = 3000;
    //�ı��С�������
    config.resize_maxWidth = 3000;
    //�ı��С����С�߶�
    config.resize_minHeight = 250;
    //�ı��С����С���
    config.resize_minWidth = 750;
	//���ύ�����д˱༭���ı�ʱ���Ƿ��Զ�����Ԫ���ڵ�����
    config.autoUpdateElement = true;
	//������ʹ�þ���Ŀ¼�������Ŀ¼��Ϊ��Ϊ���Ŀ¼
    config.baseHref = '';
	//�༭����z-indexֵ
    config.baseFloatZIndex = 10000;

	
};
