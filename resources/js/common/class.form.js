var MM_Form = Class.extend({
  
  init: function(field_wrapper){
    this.divwrapper = field_wrapper;
    this.postvars = "";
    
  },  
  dump: function()
  {
    alert(this.postvars);
  },
  getFields: function() 
  {
       var $inputs = mmJQuery('#'+this.divwrapper+' input, #'+this.divwrapper+' textarea, #'+this.divwrapper+' input:radio, #'+this.divwrapper+' input:checkbox, #'+this.divwrapper+' select');

       var values = {};

       $inputs.each(function(i, el) {
    	   var elem_name = el.id.replace(/-/g,"_");
    	   
    	   if(mmJQuery(el).val() != null) {
           		values[elem_name] = mmJQuery(el).val();
    	   }
       });

       for(var eachvar in values)
       {
           this.postvars+=eachvar+": "+values[eachvar]+"\n";
       }
       
       return values;
  }
});