var MM_Ajax = Class.extend({
  
  init: function(custom_url, module, action, method){
    //this.url = (!custom_url)?'admin-ajax.php':custom_url;
    this.url = wpadmin_url+'admin-ajax.php';
	this.module = module;
    this.action = action;
    this.useLoader = true;
    this.method = method;
    this.postvars = "";
    this.dataType = 'json';
    this.response = "";
    if(isAdministrationSection != undefined){
    	if(!isAdministrationSection){
    		this.lockarea = "main";
    	}
    	else{
    		this.lockarea = "wpbody-content";
    	}
    }
    else{
    	this.lockarea = "wpbody-content";
    }
  },
  
  dump: function(type)
  {
    if(type=='post')
        alert("class.ajax.js:\n\n"+this.postvars);
    else if(type=='response')
        alert("class.ajax.js:\n\n"+this.response);
  },
  
  send: function(data, lockdiv, returnobj, returnfunc, datatype) 
  {
        this.postvars = "";
        this.response = "";
        for(var eachvar in data)
        {
            this.postvars += eachvar+": "+data[eachvar]+"\n";
        }
        if(!lockdiv)
            lockdiv = 'body';
         
        //testing purposes only
        //this.dump('post');
        //erase 
        //alert(this.url + " "+this.action);
        
        data.method = this.method;
        data.action = this.action;
        data.module = this.module;
        
        var responseType = this.dataType;
        if(datatype != undefined)
        {
        	responseType = datatype;
        }
        
        var self = this;
        this.startLoader();
        
        var r = doAjax( 
        {		
            data:			data,
            lock:			mmJQuery(''+lockdiv)[0],
            url: 			this.url,
            dataType:		responseType,
            onSuccess: 		function(data)
                            {
        						self.stopLoader();
                                for(var eachvar in data)
                                {
                                    this.response += eachvar+": "+data[eachvar]+"\n";
                                }
                                eval(returnobj+"."+returnfunc+"(data)");
                            },
            onError: 		function(e){ 
                            	self.stopLoader();
                                alert("Error: "+e);
                            }		
        } );
        
        doAddAjax(r, this.module+"Request");
  },

  createLoaderDiv: function()
  {	
	mmJQuery("<div id=\"mm-progressbar-container\" style='position:absolute;left: 38%; top:30%; z-index: 10; filter: alpha(opacity=100);opacity:1;' ><div id=\"mm-progressbar\" style=\"width:350px\"></div></div>").hide().appendTo("body").fadeIn();
  },
  
  lock: function(){
	  if(mmJQuery("#"+this.lockarea).length){
		  mmJQuery("#"+this.lockarea).attr("style","filter: alpha(opacity=30);opacity:0.3;");
		  mmJQuery("#"+this.lockarea).attr("disabled","disabled"); 
	  } 
  },
  
  unlock: function(){
	  if(mmJQuery("#"+this.lockarea).length){
		  mmJQuery("#"+this.lockarea).attr("style","filter: alpha(opacity=100);opacity:1;");
		  mmJQuery("#"+this.lockarea).attr("disabled","");  
	  } 
  },
  
  startLoader: function(lockarea){
	  // if it exists on page, it means we are using it independantly
	  if(!this.useLoader){
		 return false;
	  }
	  if(mmJQuery("#mm-progressbar-container").length){
		  return false;
	  }
	  
	  mmJQuery("<style type='text/css'> .ui-progressbar-value { background-image: url('"+globalurl+"/resources/images/pbar-animated.gif');} </style>").appendTo("head");
	  this.createLoaderDiv();
	  if(lockarea!= undefined){
		  this.lockarea = lockarea;
	  }
	  this.lock();
	  
	  mmJQuery(function() {
			mmJQuery("#mm-progressbar").progressbar({
				value: 100
			});
		});
  },
  
  stopLoader: function(){
	  this.unlock();
	  // remove it so it won't interfere with anything else and skew results with start loader.
	  mmJQuery("#mm-progressbar-container").remove();
  },
});