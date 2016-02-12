$(document).ready(function(){
	
	/**
	 * loadData 
	 */
	var loadData = {};
	
	loadData.bindEssentials = function() {
		$('#tree-data').find('ul:first-child').attr('class', 'easyui-tree').attr( 'data-options', 'animate:true' );
		
		$('.trunk-wa').each(function(){
			$(this).parents('li').find(' > span > a > .trunk-t').removeClass('trunk-t').addClass('trunk-wa');
		});
		
		$('.trunk-f').each(function(){
			$(this).parents('li').find(' > span > a > .trunk-t').removeClass('trunk-t').addClass('trunk-wa');	
		});
		
		$('.stage-wa').each(function(){
			$(this).parents('li').find(' > span > a > .stage-t').removeClass('stage-t').addClass('stage-wa');	
		});
		
		$('.stage-f').each(function(){
			$(this).parents('li').find(' > span > a > .stage-t').removeClass('stage-t').addClass('stage-wa');	
		});
		
		$('.prod-wa').each(function(){
			$(this).parents('li').find(' > span > a > .prod-t').removeClass('prod-t').addClass('prod-wa');	
		});
		
		$('.prod-f').each(function(){
			$(this).parents('li').find(' > span > a > .prod-t').removeClass('prod-t').addClass('prod-wa');	
		});
 	}
	
	/**
	 * svnDirectory 
	 */
		
	var svnDirectory = {};
	svnDirectory.bindEssentials = function() {
		$('.easyui-tree').tree({
			onClick: function(node){
				$('#dir-trunk-data').html('');
				$('#dir-stage-data').html('');
				$('#dir-prod-data').html('');
				$('#log-trunk-data').html('');
				$('#log-stage-data').html('');
				$('#log-prod-data').html('');

				strElement = node.text;
				
				var re = /data-path=[\"](.*?)[\"]/g; 
				arrobjMatch = re.exec(strElement);
				strPath = arrobjMatch[1];
				
				arrstrPath = strPath.split('/');
				
				strHtml = '';
				$(arrstrPath).each(function(index,value){
					strHtml += '<li><a>'+value+'</a></li>';
				});
				
				$('.breadcrumb').html( strHtml );
				
				svnDirectory.generateDirectory( strPath );
			}
		});
	}

	svnDirectory.ajaxRequest = function( strPathType ) {
		$.ajax({
			url: url + '&path_type=' + strPathType.toUpperCase(),
			success: function( strJsonData ) {
				$('#dir-'+strPathType+'-data').html( svnDirectory.generateDirectoryTable( strJsonData, strPathType ) );
			}
		}).done(function(){
			$('#table-dir-'+strPathType).DataTable({
				'scrollY':			'250px',
				'scrollCollapse':	true,
				'paging':			false
			});
		});
	}

	svnDirectory.generateDirectory = function( strPath ){
		url = '/svn/get-directory?path=' + strPath;
		$('#dir-data h3').show();
		$('#dir-trunk-data').html( branchCompare.strLoader );
		$('#dir-stage-data').html( branchCompare.strLoader );
		$('#dir-prod-data').html( branchCompare.strLoader );
		
		svnDirectory.ajaxRequest('trunk');
		svnDirectory.ajaxRequest('stage');
		svnDirectory.ajaxRequest('prod');
	}
	
	svnDirectory.generateDirectoryTable = function( strJsonData, strHeading ){
		arrData = $.parseJSON( strJsonData );
		
		strHtml = '<h3 style="text-align:center;">' + strHeading.toUpperCase() + '</h3>';
		if( typeof arrData['msg'] == 'undefined' ){
			strHtml += '<table id="table-dir-'+strHeading+'">'
			strHtml += '<thead><th>Files</th><th>Revision</th><th>author</th></tr></thead><tbody>';
			
			$(arrData['list']['entry']).each(function(index, objFile){
				strHtml += '<tr class="info-dir" id="'+ strHeading +'-dir|'+ objFile['name'] + '">';
				strHtml += '<td class="file"><i class="icon-'+objFile['@attributes']['kind'] + '"></i>' + objFile['name'] + '</td>';
				strHtml += '<td>' + objFile['commit']['@attributes']['revision'] + '</td>';
				strHtml += '<td>' + objFile['commit']['author'] + '</td>'; 
			});
			strHtml += '</tbody></table>';
		} else {
			strHtml += '<h5 style="text-align:center">' + arrData['msg'] + '</h5>';
		}
		return strHtml;
	}

	/**
	 * codeCompare 
	 */

	var codeCompare = {};
	
	codeCompare.openWindow = function( strLeftPathType, strRightPathType ) {
		url = '/diff?path=' + $('#tree-data .tree-node-selected .tree-title a').attr('data-path');
		url += '&path_type_left=' + strLeftPathType;
		url += '&path_type_right=' + strRightPathType;
		window.open( url, 'Code Compare', 'left=50, top=50, width=1000, height=550' );
	}
	
	codeCompare.openWindowWinMerge = function( strLeftPathType, strRightPathType ) {
		strUrl = '/diff/win-merge?path=' + $('#tree-data .tree-node-selected .tree-title a').attr('data-path');
		strUrl += '&path_type_left=' + strLeftPathType;
		strUrl += '&path_type_right=' + strRightPathType;
		$.ajax({
			url: strUrl
		});
	}
	
	codeCompare.bindEssentials = function(){
		$('#diff-ts').click(function(){
			if( false == codeCompare.validation() ) return;

			codeCompare.openWindow( 'TRUNK', 'STAGE' );
		});
		
		$('#diff-tp').click(function(){
			if( false == codeCompare.validation() ) return;

			codeCompare.openWindow( 'TRUNK', 'PROD' );
		});

		$('#diff-sp').click(function(){
			if( false == codeCompare.validation() ) return;
				
			codeCompare.openWindow( 'STAGE', 'PROD' );
		});
		
		$('#diff-ts-win').click(function(){
			if( false == codeCompare.validation() ) return;
			
			codeCompare.openWindowWinMerge( 'TRUNK', 'STAGE' );
		});
		
		$('#diff-tp-win').click(function(){
			if( false == codeCompare.validation() ) return;
			
			codeCompare.openWindowWinMerge( 'TRUNK', 'STAGE' );
		});	
		
		$('#diff-sp-win').click(function(){
			if( false == codeCompare.validation() ) return;
			
			codeCompare.openWindowWinMerge( 'TRUNK', 'STAGE' );
		});	
	}

	codeCompare.validation = function() {
		if( $('#tree-data .tree-node-selected .tree-folder').length > 0 ) {
			alert('Please select a file to compare');
			return false;
		}
		if( typeof $('#tree-data .tree-node-selected .tree-title a').attr('data-path') == 'undefined' ) { 
			alert('Please select a file or folder' );
			return false;
		}
		return true;
	}

	/**
	 * refreshData 
	 */
	var refreshData = {};
	refreshData.step = 0;
	
	refreshData.ajaxRequest = function( strId, strUrl ) {
		$.ajax({
			url: strUrl,
			success: function(data) {
				$('#' + strId).html('<p style="text-align:center">' + data + '</p>');
			}
		}).done(function(){
			refreshData.step++;
			intPercent = 4 + ( refreshData.step * 32 ); 
			$('#refresh-status .progress-bar' ).css('width', intPercent + '%' );
			$('#refresh-status .progress-bar' ).html( intPercent + '%' );
			if( 3 == refreshData.step ) {
				refreshData.step = 0;
				location.reload();
			}
		});
	}
	
	refreshData.bindEssentials = function() {
		$('#refresh-data').click( function(event) {
			event.preventDefault();
			$(this).attr( 'disabled', 'true' );
			$('#trunk-data').html('<p style="text-align:center"><img style="width:24px"src="/img/loading.gif"></p>');
			$('#stage-data').html('<p style="text-align:center">Waiting...</p>');
			$('#prod-data').html('<p style="text-align:center">Waiting...</p>');
			$('#refresh-status .refresh-msg').hide();
			$('#refresh-status .progress').show();

			$('#refresh-status .progress-bar' ).css('width', '4%' );
			$('#refresh-status .progress-bar' ).html( '4%' );

			refreshData.ajaxRequest( 'trunk-data', '/compare/trunk-update' );
			refreshData.ajaxRequest( 'stage-data', '/compare/stage-update' );
			refreshData.ajaxRequest( 'prod-data', '/compare/prod-update' );
			
		});
	}
	
	/**
	 * svnLog
	 */
	
	var svnLog = {};
	
	svnLog.escape = function( myid ) {
		return myid.replace( /(:|\.|\[|\]|,)/g, "\\$1" );
	}
	
	svnLog.bindEssentials = function() {
		$('#dir-data').on('click', 'tbody tr.info-dir', function() {
			$('#dir-data').find('tr.selected').removeClass('selected');
			
			strId = $(this).attr('id');
			arrstrId = strId.split('|');
			
			$( '#trunk-dir\\|' + svnLog.escape( arrstrId[1] ) ).addClass('selected');
			$( '#stage-dir\\|' + svnLog.escape( arrstrId[1] ) ).addClass('selected');
			$( '#prod-dir\\|' + svnLog.escape( arrstrId[1] ) ).addClass('selected');
			
			var strPath = $('#tree-data .tree-node-selected .tree-title a').attr('data-path');
			var strIsFile = $('#tree-data .tree-node-selected .tree-title a').attr('data-file');
			
			if( 1 != strIsFile ) {
				strPath += '/' + $(this).find('.file').text();
			}
			strUrl = '/svn/get-log?path=' + strPath;
			$('#log-data h3').show();
			$('#log-trunk-data').html( branchCompare.strLoader );
			$('#log-stage-data').html( branchCompare.strLoader );
			$('#log-prod-data').html( branchCompare.strLoader );
		
			svnLog.ajaxRequest( strUrl, 'trunk' ),
			svnLog.ajaxRequest( strUrl, 'stage' ),
			svnLog.ajaxRequest( strUrl, 'prod' )
			svnLog.bindEvents();
		});
	}

	svnLog.bindEvents = function( strPathType ) {
		detailRows = [];
		$('#log-data').off('click').on( 'click', 'tbody tr td.details-control', function () {
			var objTr = $(this).closest('tr');
			strdata = 'Message:<br>';
			strdata += objTr.attr('data-msg'); 
			var idx = $.inArray( objTr.attr('id'), detailRows );
			if ( idx === -1 ) {
				objTr.addClass( 'details' );
				detailRows.push( objTr.attr('id') );
				childRow = '<tr class="child-row"><td colspan="4">'
				childRow += strdata;
				childRow += '</td></tr>'
				objTr.after( childRow );
			} else {
				detailRows.splice( idx, 1 );
				objTr.removeClass( 'details' );
				objTr.next().remove();
			}
		});
	} 
	
	svnLog.ajaxRequest = function( strUrl, strPathType ) {
		$.ajax({
			url: strUrl + '&path_type=' + strPathType.toUpperCase(),
			success: function( strJsonData ) {
				$('#log-' + strPathType + '-data').html( svnLog.generateLogTable( strJsonData, strPathType ) );
			}
		}).done(function(){
			$('#table-log-'+strPathType ).DataTable({
				'scrollY': '250px',
				'scrollCollapse': false,
				'paging': false,
				'order': [[1, 'desc']]
			});
			$('#table-log-'+strPathType).find('a').each(function(){
				$(this).bt({
					trigger: 'click',
					ajaxPath: ['$(this).attr("data-link")'],
					positions: ['left', 'right'], 
					padding: 10, 
					cornerRadius: 10,
					shadow: true,
				    shadowOffsetX: 3,
				    shadowOffsetY: 3,
				    shadowBlur: 8,
					width: '100%', 
					fill: '#FFF', 
					strokeStyle: '#B9090B', 
					cssStyles: {
						fontFamily: '"lucida grande",tahoma,verdana,arial,sans-serif', 
						fontSize: '13px'
					}
				});
			});
		});
	}
	
	svnLog.generateLogTable = function( strJsonData, strHeading ) {
		arrtableData = $.parseJSON( strJsonData );
		strHtml = '<h3 style="text-align:center;">' + strHeading.toUpperCase() + '</h3>';
		
		if( typeof arrtableData['msg'] == 'undefined' ){
			strHtml += '<table id="table-log-'+strHeading+'">'
			strHtml += '<thead><th></th><th>Revision</th><th>Author</th><th>Date</th></tr></thead><tbody>';
			var id = 0;
			$(arrtableData.logentry).each(function( index, objSvnLog ){
				strHtml += '<tr id="row_'+strHeading+'_'+id+'" data-msg="'+objSvnLog['msg']+'">';
				strHtml += '<td class="details-control"></td>';
				strHtml += '<td class="revision" valign="top"><a href="javascript:;" data-link="/svn/revision-log?rev_id=' + objSvnLog['@attributes']['revision'] + '">' + objSvnLog['@attributes']['revision'] + '</a></td>';
				strHtml += '<td valign="top">' + objSvnLog['author'] + '</td>';
				strHtml += '<td valign="top">' + objSvnLog['date'].replace('T', '<br>') + '</td>';
				strHtml += '</tr>';
				id++;
			});
			strHtml += '</tbody></table>';
		} else {
			strHtml += '<h5 style="text-align:center">' + arrtableData['msg'] + '</h5>';
		}
		return strHtml;
	}
	
	/*
	 * brachCompare 
	 */
	var branchCompare = {};
	branchCompare.strLoader = '<p style="text-align:center"><img style="width:32px"src="/img/loading.gif"></p>';
	branchCompare.bindEssentials = function() {
		$('#svn-update:not(.loading)').click(function(){
			$('#svn-update').html( '<img class="img-circle" style="width:75%" src="/img/loading.gif"><p>Updating...</p>' );
			$('#svn-update').addClass('loading');
			$.ajax({
				url: '/svn/update'
			}).done(function(){
				$('#svn-update').removeClass('loading');
				$('#svn-update').html( '<img src="/img/update.png" class="img-circle" width="60">' );	
			});
		});
		$('#light-tr').click(function(){
			$('.easyui-tree a').css('background-color', '');
			$('i.trunk-wa').closest('a').css('background-color', 'lightgreen');
		});

		$('#light-st').click(function(){
			$('.easyui-tree a').css('background-color', '');
			$('i.stage-wa').closest('a').css('background-color', 'yellow');
		});
		
		$('#light-pr').click(function(){
			$('.easyui-tree a').css('background-color', '');
			$('i.prod-wa').closest('a').css('background-color', 'lightpink');
		});
		
		$('#beanstalk-tr').click(function(){
			var path = $('#tree-data .tree-node-selected .tree-title a').attr('data-path');
			urlTrunk = 'https://gf.beanstalkapp.com/code/browse/trunk' + path;
			window.open( urlTrunk, '', 'left=50, top=50, width=1000, height=550' );
		});
		
		$('#beanstalk-st').click(function(){
			var path = $('#tree-data .tree-node-selected .tree-title a').attr('data-path');
			urlStage = 'https://gf.beanstalkapp.com/code/browse/branches/GF-STAGE' + path;
			window.open( urlStage, '', 'left=50, top=50, width=1000, height=550' );
		});
		
		$('#beanstalk-pr').click(function(){
			var path = $('#tree-data .tree-node-selected .tree-title a').attr('data-path');
			urlProd = 'https://gf.beanstalkapp.com/code/browse/branches/GF-PROD' + path;
			window.open( urlProd, '', 'left=50, top=50, width=1000, height=550' );
		});
		
		loadData.bindEssentials();
		$('#mask').hide();
		refreshData.bindEssentials();
		codeCompare.bindEssentials();
		svnDirectory.bindEssentials();
		svnLog.bindEssentials();
	}
	branchCompare.bindEssentials();
});