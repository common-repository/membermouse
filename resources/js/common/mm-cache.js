/*!
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
var glCache =
{	
	/**
	 * Class version
	 * @type String
	 */
	version: '1.0',
	
	/**
	 * List of cached objects
	 * @type Array
	 */
	cache: [],
	
	/**
	 * Default expire length 
	 * @type Number
	 */
	defaultExpire: 1/* Min */ * 60/* Sec */, 
	
	/**
	 * Add object data to cache
	 * @param {String} id
	 * @param {Object} data
	 * @return {Boolean}
	 */
	setData: function(id, data, expire)
	{		
		if (!id || !data) 
		{
			return false;
		}
		
		this.clear(id);
				
		var now = (new Date()).getTime() / 1000 | 0;
		expire = (expire == 0) ? expire : ( now + ( expire ? expire : this.defaultExpire ) );
		
		this.cache[ id ] = 
		{		
			expire:	expire,
			data:	data
		};
		
		return true;
	},
		
	/**
	 * Get object data from cache
	 * @param {String} id
	 * @return {Object}
	 */
	getData: function(id)
	{
		var cache = this.cache[ id ];
		
		if (!cache) 
		{
			return null;
		}
		else
		{
			var now = (new Date()).getTime() / 1000 | 0;
			
			if (now >= cache.expire && cache.expire != 0)
			{
				this.clear(id);
				return null;
			}
			else
			{
				return cache.data;
			}				
		}		
	},
	
	/**
	 * Clear cached object data
	 * @param {String} id
	 * @return {Boolean}
	 */
	clear:	function(id)
	{
		return delete this.cache[ id ];
	},
	
	/**
	 * Clear all cached objects
	 * @return {Boolean}
	 */
	clearAll: function()
	{	
		for(var i in this.cache)
		{
			if ( !this.clear(i) )
			{
				return false;
			}
		}
		
		return true;
	}
}