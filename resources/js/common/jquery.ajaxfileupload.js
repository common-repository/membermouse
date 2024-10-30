
mmJQuery.extend({
	

    createUploadIframe: function(id, uri)
	{
			//create frame
            var frameId = 'jUploadFrame' + id;
            
            if(window.ActiveXObject) {
                var io = document.createElement('<iframe id="' + frameId + '" name="' + frameId + '" />');
                if(typeof uri== 'boolean'){
                    io.src = 'javascript:false';
                }
                else if(typeof uri== 'string'){
                    io.src = uri;
                }
            }
            else {
                var io = document.createElement('iframe');
                io.id = frameId;
                io.name = frameId;
            }
            io.style.position = 'absolute';
            io.style.top = '-1000px';
            io.style.left = '-1000px';

            document.body.appendChild(io);

            return io			
    },
    createUploadForm: function(id, fileElementId)
	{
		//create form	
		var formId = 'jUploadForm' + id;
		var fileId = 'jUploadFile' + id;
		var form = mmJQuery('<form  action="" method="POST" name="' + formId + '" id="' + formId + '" enctype="multipart/form-data"></form>');	
		var oldElement = mmJQuery('#' + fileElementId);
		var newElement = mmJQuery(oldElement).clone();
		mmJQuery(oldElement).attr('id', fileId);
		mmJQuery(oldElement).before(newElement);
		mmJQuery(oldElement).appendTo(form);
		//set attributes
		mmJQuery(form).css('position', 'absolute');
		mmJQuery(form).css('top', '-1200px');
		mmJQuery(form).css('left', '-1200px');
		mmJQuery(form).appendTo('body');		
		return form;
    },

    ajaxFileUpload: function(s) {
        // TODO introduce global settings, allowing the client to modify them for all requests, not only timeout		
        s = mmJQuery.extend({}, mmJQuery.ajaxSettings, s);
        var id = new Date().getTime()        
		var form = mmJQuery.createUploadForm(id, s.fileElementId);
		var io = mmJQuery.createUploadIframe(id, s.secureuri);
		var frameId = 'jUploadFrame' + id;
		var formId = 'jUploadForm' + id;		
        // Watch for a new set of requests
        if ( s.global && ! mmJQuery.active++ )
		{
			mmJQuery.event.trigger( "ajaxStart" );
		}            
        var requestDone = false;
        // Create the request object
        var xml = {}   
        if ( s.global )
            mmJQuery.event.trigger("ajaxSend", [xml, s]);
        // Wait for a response to come back
        var uploadCallback = function(isTimeout)
		{			
			var io = document.getElementById(frameId);
            try 
			{				
				if(io.contentWindow)
				{
					 xml.responseText = io.contentWindow.document.body?io.contentWindow.document.body.innerHTML:null;
                	 xml.responseXML = io.contentWindow.document.XMLDocument?io.contentWindow.document.XMLDocument:io.contentWindow.document;
					 
				}else if(io.contentDocument)
				{
					 xml.responseText = io.contentDocument.document.body?io.contentDocument.document.body.innerHTML:null;
                	xml.responseXML = io.contentDocument.document.XMLDocument?io.contentDocument.document.XMLDocument:io.contentDocument.document;
				}						
            }catch(e)
			{
				mmJQuery.handleError(s, xml, null, e);
			}
            if ( xml || isTimeout == "timeout") 
			{				
                requestDone = true;
                var status;
                try {
                    status = isTimeout != "timeout" ? "success" : "error";
                    // Make sure that the request was successful or notmodified
                    if ( status != "error" )
					{
                        // process the data (runs the xml through httpData regardless of callback)
                        var data = mmJQuery.uploadHttpData( xml, s.dataType );    
                        // If a local callback was specified, fire it and pass it the data
                        if ( s.success )
                            s.success( data, status );
    
                        // Fire the global callback
                        if( s.global )
                            mmJQuery.event.trigger( "ajaxSuccess", [xml, s] );
                    } else
                        mmJQuery.handleError(s, xml, status);
                } catch(e) 
				{
                    status = "error";
                    mmJQuery.handleError(s, xml, status, e);
                }

                // The request was completed
                if( s.global )
                    mmJQuery.event.trigger( "ajaxComplete", [xml, s] );

                // Handle the global AJAX counter
                if ( s.global && ! --mmJQuery.active )
                    mmJQuery.event.trigger( "ajaxStop" );

                // Process result
                if ( s.complete )
                    s.complete(xml, status);

                mmJQuery(io).unbind()

                setTimeout(function()
									{	try 
										{
											mmJQuery(io).remove();
											mmJQuery(form).remove();	
											
										} catch(e) 
										{
											mmJQuery.handleError(s, xml, null, e);
										}									

									}, 100)

                xml = null

            }
        }
        // Timeout checker
        if ( s.timeout > 0 ) 
		{
            setTimeout(function(){
                // Check to see if the request is still happening
                if( !requestDone ) uploadCallback( "timeout" );
            }, s.timeout);
        }
        try 
		{
           // var io = mmJQuery('#' + frameId);
			var form = mmJQuery('#' + formId);
			mmJQuery(form).attr('action', s.url);
			mmJQuery(form).attr('method', 'POST');
			mmJQuery(form).attr('target', frameId);
            if(s.data)
            {
                for(var eachvar in s.data)
                {
                    mmJQuery(form).append("<input type='hidden' name='"+eachvar+"' value='"+s.data[eachvar]+"' />");
                }
            }
            if(form.encoding)
			{
                form.encoding = 'multipart/form-data';				
            }
            else
			{				
                form.enctype = 'multipart/form-data';
            }			
            mmJQuery(form).submit();

        } catch(e) 
		{			
            mmJQuery.handleError(s, xml, null, e);
        }
        if(window.attachEvent){
            document.getElementById(frameId).attachEvent('onload', uploadCallback);
        }
        else{
            document.getElementById(frameId).addEventListener('load', uploadCallback, false);
        } 		
        return {abort: function () {}};	

    },

    uploadHttpData: function( r, type ) {
        var data = !type;
        data = type == "xml" || data ? r.responseXML : r.responseText;
        // If the type is "script", eval it in global context
        if ( type == "script" )
            mmJQuery.globalEval( data );
        // Get the JavaScript object, if JSON is used.
        if ( type == "json" )
            eval( "data = " + data );
        // evaluate scripts within html
        if ( type == "html" )
            mmJQuery("<div>").html(data).evalScripts();
			//alert(mmJQuery('param', data).each(function(){alert(mmJQuery(this).attr('value'));}));
        return data;
    }
})

