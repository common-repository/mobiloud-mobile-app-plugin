/* @version 4.3.9 */

async function getJSON(url, callback) {
	var xhr = new XMLHttpRequest();
	if ( ml_list && ml_list.is_subscribed && -1 === url.indexOf('user_is_subscribed=true') ) {
		url += '&user_is_subscribed=true';
	}
	xhr.open( 'GET', url, true );
	if ( ml_list && ml_list.is_subscribed ) {
		xhr.setRequestHeader( 'x-ml-is-user-subscribed', 'true' );
	}
	xhr.responseType = 'json';
	xhr.onload       = function() {
		var status = xhr.status;
		if (status == 200) {
			callback( null, xhr.response );
		} else {
			callback( status );
		}
	};
	xhr.send();
	return 1;
};

var ml_possible_scripts = [];
var ml_list_ads_counter = 0;
function get_empty_class( value, add_class_tag ) {
	return '' === value ? ( 'undefined' !== typeof( add_class_tag ) && add_class_tag ? ' class="article-list__meta-empty"' : ' article-list__meta-empty' ) : '';
}
function createItemContent( article ) {
	// Return a DOM element here.
	var thumb;
	var wrap = ons.createElement( '<div class="article-list__wrap"></div>' );
	var list_item_classes = [ 'article-list__article' ];
	if ( 'undefined' === typeof( article['raw_html'] ) ) {
		// Check for a custom create item content solution using predefined function with name "ml_create_item_content" and use it if defined.
		if ( 'function' === typeof ml_list_create_item_content ) {
			try {
				/**
				* Allow to implement any custom layout using article fields.
				* Please define custom function ml_list_create_item_content( article_object ) and return a result
				* of call ons.createElement( '<ons-list-item class="article-list__article list-item"> </ons-list-item>' ) with any desired content inside.
				*
				* @since 4.2.3
				*
				* @param object article_object
				* @return mixed
				*/
				return ml_list_create_item_content( article );
			} catch( e ) {
				console.log( 'Error while createItemContentCustom()', article, 'Exception: ', e );
				// will try create content with a standard code.
			}
		}

		// article item.
		list_item_classes.push( 'post-id-' + article.post_id );
		list_item_classes.push( 'author-id-' + article.author.author_id );

		// Article image
		if ( null == article.featured_image ) {
			// no featured image set
			article.featured_image = { 'big-thumb': { url: defaultThumb } };
		}
		var image_url = article.featured_image["big-thumb"].url;
		var image_srcset = article.featured_image_resp ? article.featured_image_resp.srcset || '' : '';
		var image_sizes = article.featured_image_resp ? article.featured_image_resp.sizes || '' : '';
		var image_low = article.featured_image_resp && article.featured_image_resp.low !== image_url ? article.featured_image_resp.low || '' : '';
		thumb = ons.createElement( '<figure class="article-list__thumb'+get_empty_class( image_url )+'"><img class="bg_image lazyload blur-up"'
			+ 'data-src="' + image_url + '"'
			+ ( image_srcset && image_sizes ? ' data-srcset="' + image_srcset + '" data-sizes="' + image_sizes + '"' : '')
			+ ( image_low ? ' src="' + image_low + '"' + ( image_srcset ? ' srcset="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw=="' : '' ): '')
			+ ( image_low ? ' data-lowsrc="' + image_low + '"' : '')
			+ ' /></figure>' );
		wrap.append( thumb );

		// create article fields.
		var date_html = '';
		if ( article['date_ago'] || '') {
			date_html = '<p class="article-list__meta_date article-list__meta_date_ago' + get_empty_class( article.date_ago ) + '">' + article.date_ago + '</p>';
		} else if ( article['date_display'] || '' ) {
			date_html = '<p class="article-list__meta_date article-list__meta_date_string' + get_empty_class( article.date_display ) + '">' + article.date_display + '</p>';
		}
		var title_html = '<h2' + get_empty_class( article.title, true ) + '>' + article.title + '</h2>';
		var excerpt_html = '<p' + get_empty_class( article.excerpt, true ) + '>' + article.excerpt + '</p>';
		var cat_html = ''; // first category.
		var cats_html = ''; // all categories.
		if (article.categories[0] != null) {
			var cats = [];
			var cats_names = [];
			for ( var k in article.categories ) {
				var cat_class = 'cat-id-' + article.categories[k].cat_id;
				list_item_classes.push( cat_class );
				cats_names.push( article.categories[k].name );
				cats.push( '<a tappable data-ml-cat-id="' + article.categories[k].cat_id + '" class="category ' + cat_class + get_empty_class( article.categories[k].name ) + '">' + article.categories[k].name + '</a>' );
			}
			cat_html = '<div class="article-list__meta_category' + get_empty_class( cats[0] ) + '">' + cats[0] + '</div>';
			cats_html = '<div class="article-list__meta_categories">' + cats.join(' ') + get_empty_class( cats_names.join( '' ) ) + '</div>';
		}
		var author_html = '<div class="article-list__meta_author' + get_empty_class( article.author.name || '' ) + '">' + ( article.author.name || '' )+ '</div>';
		var custom_html = '<div class="article-list__meta_custom_field' + get_empty_class( article.custom1 || '' ) + '">' + ( article.custom1 || '' ) + '</div>';
		var comments_html = '<div class="article-list__meta_comments' + (0 + article['comments-count'] == 0 ? ' article-list__meta_comments-none' : ' article-list__meta_comments-has' ) + '">' + article['comments-count-text'] + '</div>';
		var cat_author_html = cat_html + author_html;

		var html = {
			title: title_html, // Title.
			date: date_html, // Date, just a date or a pretty date.
			excerpt: excerpt_html, // Excerpt.
			category: cat_html, // First category.
			categories: cats_html, // All categories.
			author: author_html, // Author.
			custom: custom_html, // Custom field.
			comments: comments_html, // Number of comments.
			'category-author' : cat_author_html, // First category and author in single line.
		};
		// put fields in desired order.
		var main_content = [];
		var meta_content = [];
		for ( var k in ml_list.main_order) {
			var name = ml_list.main_order[k];
			/**
			* Allow to implement any non standard fields using custom function ml_render_list_fields( field_name, article_object ).
			*/
			if ( 'undefined' === typeof( html[name] ) ) {
				if ( 'function' === typeof( 'ml_render_list_fields' ) ) {
					try {
						/**
						* Allow to implement any custom fields using filter.
						* Please define custom function ml_render_list_fields( field_name, article_object ) and return a string.
						*
						* @since 4.2.0
						*
						* @param string field_name
						* @param object article_object
						* @return string|null
						*/
						var value = ml_render_list_fields( name, article );
						if ( 'string' === typeof( value ) ) {
							html[name] = value;
						}
					} catch ( e ) {
						// Does not allow custom code to fail everything. Skip invalid result, just show in at the console.
						console.log( 'error in ml_render_list_fields() call: ', e );
					}

				}
			}
			if ( 'undefined' !== typeof( html[name] ) ) {
				main_content.push( html[name] );
			}
		}
		for ( var k in ml_list.meta_order ) {
			var name = ml_list.meta_order[k];
			if ( 'undefined' !== typeof( html[name] ) ) {
				meta_content.push( html[name] );
			}
		}

		// Article text fields.
		var content = ons.createElement( '<div class="article-list__content">' + main_content.join( '' ) + '</div>' );
		wrap.append( content );

		// Article meta fields.
		var meta = ons.createElement( '<div class="article-list__meta">' + meta_content.join('') + '</div>' );

		// Article element.
		var swipe_list_cat = article.swipe && article.swipe.listCat ? article.swipe.listCat : '';
		var listItem = ons.createElement( '<ons-list-item class="' + list_item_classes.join( ' ' ) + '" tappable data-ml-post-id="' + article.post_id + '" data-ml-list-cat="' + swipe_list_cat + '" data-ml-post-permalink="' + article.permalink + '" data-ml-opening-method="' + article.opening_method + '"></ons-list-item>' );
		listItem.append( wrap );
		listItem.append( meta );
	} else {
		// Create List Ad content.
		list_item_classes.push( 'list-ad-content' );
		// Article element.
		var listItem = ons.createElement( '<ons-list-item class="' + list_item_classes.join( ' ' ) + '"> </ons-list-item>' ); // space in content required.
		// modify Ad content if custom function ml_list_filter_ad_content() exists.
		if ( 'function' === typeof ml_list_filter_ad_content ) {
			try {
				/**
				* Allow to modify Ad content using filter.
				*
				* @since 4.2.3
				*
				* @param string ad_content
				* @return string
				*/
				article.raw_html = ml_list_filter_ad_content( article.raw_html );
			} catch ( e ) {
				console.log( e );
			}
		}
		listItem.innerHTML = ( article.raw_html || '' ).replace(/###ML_COUNTER###/g, '' + ml_list_ads_counter );
		ml_possible_scripts.push( listItem );
		ml_list_ads_counter++;
	}

	return listItem;
}

function renderList( data ) {
	var spinner = document.getElementById( 'loading-more' );
	var posts   = data['posts'].length,
	list    = document.getElementById( 'article-list' );
	ml_possible_scripts = [];

	for ( var i = 0; i < posts; i++ ) {
		// this function also put possible scripts into ml_possible_scripts variable.
		var article = createItemContent( data['posts'][i] );

		article.addEventListener(
			'click', function () {
				if ( ! this.classList.contains( 'list-ad-content' ) ) {
					if ( this.getAttribute( 'data-ml-opening-method' ) == 'internal' ) {
						var postTitle = this.querySelector( '.article-list__content h2' ).innerText;
						nativeFunctions.handleLink( this.getAttribute( 'data-ml-post-permalink' ), postTitle, 'internal' );
					} else {
						nativeFunctions.handlePost( this.getAttribute( 'data-ml-post-id' ), this.getAttribute( 'data-ml-list-cat' ) || '' );
					}
				}
			}
		);

		list.appendChild( article );
		rendered++;
	}
	// execute posible scripts.
	if ( ml_possible_scripts.length ) {
		for ( var k in ml_possible_scripts ) {
			exec_body_scripts( ml_possible_scripts[k] );
		}
	}
	document.querySelectorAll( '.article-list__article.is-placeholder' ).forEach( e => e.parentNode.removeChild( e ) );
	list.classList.add( 'rendered' );
	spinner.style.display = 'none';
}

async function getNewArticles(url, offset, params) {
	var spinner           = document.getElementById( 'loading-more' );
	spinner.style.display = 'block';
	return new Promise(
		function (resolve, reject) {
			getJSON(
				url + 'ml-api/v2/posts?' + params + '&offset=' + offset,
				function( err, data ) {
					if ( err != null ) {
						// console.log( 'Something went wrong: ' + err );
						spinner.style.display = 'none';
						resolve( 0 );
					} else {
						renderList( data );
						mlPostsData['posts']  = mlPostsData['posts'].concat( data['posts'] );
						var loaded = data['posts'].length - ( data['ad_count'] || 0 ); // do not count Ads.
						spinner.style.display = 'none';
						resolve( loaded );
					}
				}
			)
		}
	);
}

window.lazySizesConfig = window.lazySizesConfig || {};
window.lazySizesConfig.expand = 50;
window.lazySizesConfig.blurupMode = 'auto';
document.addEventListener('lazyloaded', function(e){
	let img = e.target;
	let node = e.target.parentElement;
	if(e.target.naturalHeight){
		let calculated_height = img.width / node.clientWidth * node.clientHeight;
		if ( ml_list.resize_images || 0 ) {
			node.style.height = '' + ( node.clientHeight / calculated_height * img.height )+ 'px';
			node.style.paddingTop = '0';
		} else {
			if ( calculated_height > img.height ) {
				// node.style.height = '' + ( node.clientHeight / calculated_height * img.height )+ 'px';
				node.style.height = '' + node.clientHeight + 'px';
				img.style.height = '100%';
				img.style.width = 'auto';
			} else {
				node.style.height = '' + node.clientHeight + 'px';
			}
			node.style.paddingTop = '0';
		}
	}
	node.classList.add( 'lazyfinished' );
});
/*! picturefill - v3.0.2 - 2016-02-12
* https://scottjehl.github.io/picturefill/
* Copyright (c) 2016 https://github.com/scottjehl/picturefill/blob/master/Authors.txt; Licensed MIT
*/
!function(a){var b=navigator.userAgent;a.HTMLPictureElement&&/ecko/.test(b)&&b.match(/rv\:(\d+)/)&&RegExp.$1<45&&addEventListener("resize",function(){var b,c=document.createElement("source"),d=function(a){var b,d,e=a.parentNode;"PICTURE"===e.nodeName.toUpperCase()?(b=c.cloneNode(),e.insertBefore(b,e.firstElementChild),setTimeout(function(){e.removeChild(b)})):(!a._pfLastSize||a.offsetWidth>a._pfLastSize)&&(a._pfLastSize=a.offsetWidth,d=a.sizes,a.sizes+=",100vw",setTimeout(function(){a.sizes=d}))},e=function(){var a,b=document.querySelectorAll("picture > img, img[srcset][sizes]");for(a=0;a<b.length;a++)d(b[a])},f=function(){clearTimeout(b),b=setTimeout(e,99)},g=a.matchMedia&&matchMedia("(orientation: landscape)"),h=function(){f(),g&&g.addListener&&g.addListener(f)};return c.srcset="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==",/^[c|i]|d$/.test(document.readyState||"")?h():document.addEventListener("DOMContentLoaded",h),f}())}(window),function(a,b,c){"use strict";function d(a){return" "===a||"	"===a||"\n"===a||"\f"===a||"\r"===a}function e(b,c){var d=new a.Image;return d.onerror=function(){A[b]=!1,ba()},d.onload=function(){A[b]=1===d.width,ba()},d.src=c,"pending"}function f(){M=!1,P=a.devicePixelRatio,N={},O={},s.DPR=P||1,Q.width=Math.max(a.innerWidth||0,z.clientWidth),Q.height=Math.max(a.innerHeight||0,z.clientHeight),Q.vw=Q.width/100,Q.vh=Q.height/100,r=[Q.height,Q.width,P].join("-"),Q.em=s.getEmValue(),Q.rem=Q.em}function g(a,b,c,d){var e,f,g,h;return"saveData"===B.algorithm?a>2.7?h=c+1:(f=b-c,e=Math.pow(a-.6,1.5),g=f*e,d&&(g+=.1*e),h=a+g):h=c>1?Math.sqrt(a*b):a,h>c}function h(a){var b,c=s.getSet(a),d=!1;"pending"!==c&&(d=r,c&&(b=s.setRes(c),s.applySetCandidate(b,a))),a[s.ns].evaled=d}function i(a,b){return a.res-b.res}function j(a,b,c){var d;return!c&&b&&(c=a[s.ns].sets,c=c&&c[c.length-1]),d=k(b,c),d&&(b=s.makeUrl(b),a[s.ns].curSrc=b,a[s.ns].curCan=d,d.res||aa(d,d.set.sizes)),d}function k(a,b){var c,d,e;if(a&&b)for(e=s.parseSet(b),a=s.makeUrl(a),c=0;c<e.length;c++)if(a===s.makeUrl(e[c].url)){d=e[c];break}return d}function l(a,b){var c,d,e,f,g=a.getElementsByTagName("source");for(c=0,d=g.length;d>c;c++)e=g[c],e[s.ns]=!0,f=e.getAttribute("srcset"),f&&b.push({srcset:f,media:e.getAttribute("media"),type:e.getAttribute("type"),sizes:e.getAttribute("sizes")})}function m(a,b){function c(b){var c,d=b.exec(a.substring(m));return d?(c=d[0],m+=c.length,c):void 0}function e(){var a,c,d,e,f,i,j,k,l,m=!1,o={};for(e=0;e<h.length;e++)f=h[e],i=f[f.length-1],j=f.substring(0,f.length-1),k=parseInt(j,10),l=parseFloat(j),X.test(j)&&"w"===i?((a||c)&&(m=!0),0===k?m=!0:a=k):Y.test(j)&&"x"===i?((a||c||d)&&(m=!0),0>l?m=!0:c=l):X.test(j)&&"h"===i?((d||c)&&(m=!0),0===k?m=!0:d=k):m=!0;m||(o.url=g,a&&(o.w=a),c&&(o.d=c),d&&(o.h=d),d||c||a||(o.d=1),1===o.d&&(b.has1x=!0),o.set=b,n.push(o))}function f(){for(c(T),i="",j="in descriptor";;){if(k=a.charAt(m),"in descriptor"===j)if(d(k))i&&(h.push(i),i="",j="after descriptor");else{if(","===k)return m+=1,i&&h.push(i),void e();if("("===k)i+=k,j="in parens";else{if(""===k)return i&&h.push(i),void e();i+=k}}else if("in parens"===j)if(")"===k)i+=k,j="in descriptor";else{if(""===k)return h.push(i),void e();i+=k}else if("after descriptor"===j)if(d(k));else{if(""===k)return void e();j="in descriptor",m-=1}m+=1}}for(var g,h,i,j,k,l=a.length,m=0,n=[];;){if(c(U),m>=l)return n;g=c(V),h=[],","===g.slice(-1)?(g=g.replace(W,""),e()):f()}}function n(a){function b(a){function b(){f&&(g.push(f),f="")}function c(){g[0]&&(h.push(g),g=[])}for(var e,f="",g=[],h=[],i=0,j=0,k=!1;;){if(e=a.charAt(j),""===e)return b(),c(),h;if(k){if("*"===e&&"/"===a[j+1]){k=!1,j+=2,b();continue}j+=1}else{if(d(e)){if(a.charAt(j-1)&&d(a.charAt(j-1))||!f){j+=1;continue}if(0===i){b(),j+=1;continue}e=" "}else if("("===e)i+=1;else if(")"===e)i-=1;else{if(","===e){b(),c(),j+=1;continue}if("/"===e&&"*"===a.charAt(j+1)){k=!0,j+=2;continue}}f+=e,j+=1}}}function c(a){return k.test(a)&&parseFloat(a)>=0?!0:l.test(a)?!0:"0"===a||"-0"===a||"+0"===a?!0:!1}var e,f,g,h,i,j,k=/^(?:[+-]?[0-9]+|[0-9]*\.[0-9]+)(?:[eE][+-]?[0-9]+)?(?:ch|cm|em|ex|in|mm|pc|pt|px|rem|vh|vmin|vmax|vw)$/i,l=/^calc\((?:[0-9a-z \.\+\-\*\/\(\)]+)\)$/i;for(f=b(a),g=f.length,e=0;g>e;e++)if(h=f[e],i=h[h.length-1],c(i)){if(j=i,h.pop(),0===h.length)return j;if(h=h.join(" "),s.matchesMedia(h))return j}return"100vw"}b.createElement("picture");var o,p,q,r,s={},t=!1,u=function(){},v=b.createElement("img"),w=v.getAttribute,x=v.setAttribute,y=v.removeAttribute,z=b.documentElement,A={},B={algorithm:""},C="data-pfsrc",D=C+"set",E=navigator.userAgent,F=/rident/.test(E)||/ecko/.test(E)&&E.match(/rv\:(\d+)/)&&RegExp.$1>35,G="currentSrc",H=/\s+\+?\d+(e\d+)?w/,I=/(\([^)]+\))?\s*(.+)/,J=a.picturefillCFG,K="position:absolute;left:0;visibility:hidden;display:block;padding:0;border:none;font-size:1em;width:1em;overflow:hidden;clip:rect(0px, 0px, 0px, 0px)",L="font-size:100%!important;",M=!0,N={},O={},P=a.devicePixelRatio,Q={px:1,"in":96},R=b.createElement("a"),S=!1,T=/^[ \t\n\r\u000c]+/,U=/^[, \t\n\r\u000c]+/,V=/^[^ \t\n\r\u000c]+/,W=/[,]+$/,X=/^\d+$/,Y=/^-?(?:[0-9]+|[0-9]*\.[0-9]+)(?:[eE][+-]?[0-9]+)?$/,Z=function(a,b,c,d){a.addEventListener?a.addEventListener(b,c,d||!1):a.attachEvent&&a.attachEvent("on"+b,c)},$=function(a){var b={};return function(c){return c in b||(b[c]=a(c)),b[c]}},_=function(){var a=/^([\d\.]+)(em|vw|px)$/,b=function(){for(var a=arguments,b=0,c=a[0];++b in a;)c=c.replace(a[b],a[++b]);return c},c=$(function(a){return"return "+b((a||"").toLowerCase(),/\band\b/g,"&&",/,/g,"||",/min-([a-z-\s]+):/g,"e.$1>=",/max-([a-z-\s]+):/g,"e.$1<=",/calc([^)]+)/g,"($1)",/(\d+[\.]*[\d]*)([a-z]+)/g,"($1 * e.$2)",/^(?!(e.[a-z]|[0-9\.&=|><\+\-\*\(\)\/])).*/gi,"")+";"});return function(b,d){var e;if(!(b in N))if(N[b]=!1,d&&(e=b.match(a)))N[b]=e[1]*Q[e[2]];else try{N[b]=new Function("e",c(b))(Q)}catch(f){}return N[b]}}(),aa=function(a,b){return a.w?(a.cWidth=s.calcListLength(b||"100vw"),a.res=a.w/a.cWidth):a.res=a.d,a},ba=function(a){if(t){var c,d,e,f=a||{};if(f.elements&&1===f.elements.nodeType&&("IMG"===f.elements.nodeName.toUpperCase()?f.elements=[f.elements]:(f.context=f.elements,f.elements=null)),c=f.elements||s.qsa(f.context||b,f.reevaluate||f.reselect?s.sel:s.selShort),e=c.length){for(s.setupRun(f),S=!0,d=0;e>d;d++)s.fillImg(c[d],f);s.teardownRun(f)}}};o=a.console&&console.warn?function(a){console.warn(a)}:u,G in v||(G="src"),A["image/jpeg"]=!0,A["image/gif"]=!0,A["image/png"]=!0,A["image/svg+xml"]=b.implementation.hasFeature("http://www.w3.org/TR/SVG11/feature#Image","1.1"),s.ns=("pf"+(new Date).getTime()).substr(0,9),s.supSrcset="srcset"in v,s.supSizes="sizes"in v,s.supPicture=!!a.HTMLPictureElement,s.supSrcset&&s.supPicture&&!s.supSizes&&!function(a){v.srcset="data:,a",a.src="data:,a",s.supSrcset=v.complete===a.complete,s.supPicture=s.supSrcset&&s.supPicture}(b.createElement("img")),s.supSrcset&&!s.supSizes?!function(){var a="data:image/gif;base64,R0lGODlhAgABAPAAAP///wAAACH5BAAAAAAALAAAAAACAAEAAAICBAoAOw==",c="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==",d=b.createElement("img"),e=function(){var a=d.width;2===a&&(s.supSizes=!0),q=s.supSrcset&&!s.supSizes,t=!0,setTimeout(ba)};d.onload=e,d.onerror=e,d.setAttribute("sizes","9px"),d.srcset=c+" 1w,"+a+" 9w",d.src=c}():t=!0,s.selShort="picture>img,img[srcset]",s.sel=s.selShort,s.cfg=B,s.DPR=P||1,s.u=Q,s.types=A,s.setSize=u,s.makeUrl=$(function(a){return R.href=a,R.href}),s.qsa=function(a,b){return"querySelector"in a?a.querySelectorAll(b):[]},s.matchesMedia=function(){return a.matchMedia&&(matchMedia("(min-width: 0.1em)")||{}).matches?s.matchesMedia=function(a){return!a||matchMedia(a).matches}:s.matchesMedia=s.mMQ,s.matchesMedia.apply(this,arguments)},s.mMQ=function(a){return a?_(a):!0},s.calcLength=function(a){var b=_(a,!0)||!1;return 0>b&&(b=!1),b},s.supportsType=function(a){return a?A[a]:!0},s.parseSize=$(function(a){var b=(a||"").match(I);return{media:b&&b[1],length:b&&b[2]}}),s.parseSet=function(a){return a.cands||(a.cands=m(a.srcset,a)),a.cands},s.getEmValue=function(){var a;if(!p&&(a=b.body)){var c=b.createElement("div"),d=z.style.cssText,e=a.style.cssText;c.style.cssText=K,z.style.cssText=L,a.style.cssText=L,a.appendChild(c),p=c.offsetWidth,a.removeChild(c),p=parseFloat(p,10),z.style.cssText=d,a.style.cssText=e}return p||16},s.calcListLength=function(a){if(!(a in O)||B.uT){var b=s.calcLength(n(a));O[a]=b?b:Q.width}return O[a]},s.setRes=function(a){var b;if(a){b=s.parseSet(a);for(var c=0,d=b.length;d>c;c++)aa(b[c],a.sizes)}return b},s.setRes.res=aa,s.applySetCandidate=function(a,b){if(a.length){var c,d,e,f,h,k,l,m,n,o=b[s.ns],p=s.DPR;if(k=o.curSrc||b[G],l=o.curCan||j(b,k,a[0].set),l&&l.set===a[0].set&&(n=F&&!b.complete&&l.res-.1>p,n||(l.cached=!0,l.res>=p&&(h=l))),!h)for(a.sort(i),f=a.length,h=a[f-1],d=0;f>d;d++)if(c=a[d],c.res>=p){e=d-1,h=a[e]&&(n||k!==s.makeUrl(c.url))&&g(a[e].res,c.res,p,a[e].cached)?a[e]:c;break}h&&(m=s.makeUrl(h.url),o.curSrc=m,o.curCan=h,m!==k&&s.setSrc(b,h),s.setSize(b))}},s.setSrc=function(a,b){var c;a.src=b.url,"image/svg+xml"===b.set.type&&(c=a.style.width,a.style.width=a.offsetWidth+1+"px",a.offsetWidth+1&&(a.style.width=c))},s.getSet=function(a){var b,c,d,e=!1,f=a[s.ns].sets;for(b=0;b<f.length&&!e;b++)if(c=f[b],c.srcset&&s.matchesMedia(c.media)&&(d=s.supportsType(c.type))){"pending"===d&&(c=d),e=c;break}return e},s.parseSets=function(a,b,d){var e,f,g,h,i=b&&"PICTURE"===b.nodeName.toUpperCase(),j=a[s.ns];(j.src===c||d.src)&&(j.src=w.call(a,"src"),j.src?x.call(a,C,j.src):y.call(a,C)),(j.srcset===c||d.srcset||!s.supSrcset||a.srcset)&&(e=w.call(a,"srcset"),j.srcset=e,h=!0),j.sets=[],i&&(j.pic=!0,l(b,j.sets)),j.srcset?(f={srcset:j.srcset,sizes:w.call(a,"sizes")},j.sets.push(f),g=(q||j.src)&&H.test(j.srcset||""),g||!j.src||k(j.src,f)||f.has1x||(f.srcset+=", "+j.src,f.cands.push({url:j.src,d:1,set:f}))):j.src&&j.sets.push({srcset:j.src,sizes:null}),j.curCan=null,j.curSrc=c,j.supported=!(i||f&&!s.supSrcset||g&&!s.supSizes),h&&s.supSrcset&&!j.supported&&(e?(x.call(a,D,e),a.srcset=""):y.call(a,D)),j.supported&&!j.srcset&&(!j.src&&a.src||a.src!==s.makeUrl(j.src))&&(null===j.src?a.removeAttribute("src"):a.src=j.src),j.parsed=!0},s.fillImg=function(a,b){var c,d=b.reselect||b.reevaluate;a[s.ns]||(a[s.ns]={}),c=a[s.ns],(d||c.evaled!==r)&&((!c.parsed||b.reevaluate)&&s.parseSets(a,a.parentNode,b),c.supported?c.evaled=r:h(a))},s.setupRun=function(){(!S||M||P!==a.devicePixelRatio)&&f()},s.supPicture?(ba=u,s.fillImg=u):!function(){var c,d=a.attachEvent?/d$|^c/:/d$|^c|^i/,e=function(){var a=b.readyState||"";f=setTimeout(e,"loading"===a?200:999),b.body&&(s.fillImgs(),c=c||d.test(a),c&&clearTimeout(f))},f=setTimeout(e,b.body?9:99),g=function(a,b){var c,d,e=function(){var f=new Date-d;b>f?c=setTimeout(e,b-f):(c=null,a())};return function(){d=new Date,c||(c=setTimeout(e,b))}},h=z.clientHeight,i=function(){M=Math.max(a.innerWidth||0,z.clientWidth)!==Q.width||z.clientHeight!==h,h=z.clientHeight,M&&s.fillImgs()};Z(a,"resize",g(i,99)),Z(b,"readystatechange",e)}(),s.picturefill=ba,s.fillImgs=ba,s.teardownRun=u,ba._=s,a.picturefillCFG={pf:s,push:function(a){var b=a.shift();"function"==typeof s[b]?s[b].apply(s,a):(B[b]=a[0],S&&s.fillImgs({reselect:!0}))}};for(;J&&J.length;)a.picturefillCFG.push(J.shift());a.picturefill=ba,"object"==typeof module&&"object"==typeof module.exports?module.exports=ba:"function"==typeof define&&define.amd&&define("picturefill",function(){return ba}),s.supPicture||(A["image/webp"]=e("image/webp","data:image/webp;base64,UklGRkoAAABXRUJQVlA4WAoAAAAQAAAAAAAAAAAAQUxQSAwAAAABBxAR/Q9ERP8DAABWUDggGAAAADABAJ0BKgEAAQADADQlpAADcAD++/1QAA=="))}(window,document);

/*! lazysizes - v5.2.0
* https://github.com/aFarkas/lazysizes/
* Copyright (c) 2015 Alexander Farkas; Licensed MIT
*/
!function(a,b){var c=b(a,a.document,Date);a.lazySizes=c,"object"==typeof module&&module.exports&&(module.exports=c)}("undefined"!=typeof window?window:{},function(a,b,c){"use strict";var d,e;if(function(){var b,c={lazyClass:"lazyload",loadedClass:"lazyloaded",loadingClass:"lazyloading",preloadClass:"lazypreload",errorClass:"lazyerror",autosizesClass:"lazyautosizes",srcAttr:"data-src",srcsetAttr:"data-srcset",sizesAttr:"data-sizes",minSize:40,customMedia:{},init:!0,expFactor:1.5,hFac:.8,loadMode:2,loadHidden:!0,ricTimeout:0,throttleDelay:125};e=a.lazySizesConfig||a.lazysizesConfig||{};for(b in c)b in e||(e[b]=c[b])}(),!b||!b.getElementsByClassName)return{init:function(){},cfg:e,noSupport:!0};var f=b.documentElement,g=a.HTMLPictureElement,h="addEventListener",i="getAttribute",j=a[h].bind(a),k=a.setTimeout,l=a.requestAnimationFrame||k,m=a.requestIdleCallback,n=/^picture$/i,o=["load","error","lazyincluded","_lazyloaded"],p={},q=Array.prototype.forEach,r=function(a,b){return p[b]||(p[b]=new RegExp("(\\s|^)"+b+"(\\s|$)")),p[b].test(a[i]("class")||"")&&p[b]},s=function(a,b){r(a,b)||a.setAttribute("class",(a[i]("class")||"").trim()+" "+b)},t=function(a,b){var c;(c=r(a,b))&&a.setAttribute("class",(a[i]("class")||"").replace(c," "))},u=function(a,b,c){var d=c?h:"removeEventListener";c&&u(a,b),o.forEach(function(c){a[d](c,b)})},v=function(a,c,e,f,g){var h=b.createEvent("Event");return e||(e={}),e.instance=d,h.initEvent(c,!f,!g),h.detail=e,a.dispatchEvent(h),h},w=function(b,c){var d;!g&&(d=a.picturefill||e.pf)?(c&&c.src&&!b[i]("srcset")&&b.setAttribute("srcset",c.src),d({reevaluate:!0,elements:[b]})):c&&c.src&&(b.src=c.src)},x=function(a,b){return(getComputedStyle(a,null)||{})[b]},y=function(a,b,c){for(c=c||a.offsetWidth;c<e.minSize&&b&&!a._lazysizesWidth;)c=b.offsetWidth,b=b.parentNode;return c},z=function(){var a,c,d=[],e=[],f=d,g=function(){var b=f;for(f=d.length?e:d,a=!0,c=!1;b.length;)b.shift()();a=!1},h=function(d,e){a&&!e?d.apply(this,arguments):(f.push(d),c||(c=!0,(b.hidden?k:l)(g)))};return h._lsFlush=g,h}(),A=function(a,b){return b?function(){z(a)}:function(){var b=this,c=arguments;z(function(){a.apply(b,c)})}},B=function(a){var b,d=0,f=e.throttleDelay,g=e.ricTimeout,h=function(){b=!1,d=c.now(),a()},i=m&&g>49?function(){m(h,{timeout:g}),g!==e.ricTimeout&&(g=e.ricTimeout)}:A(function(){k(h)},!0);return function(a){var e;(a=!0===a)&&(g=33),b||(b=!0,e=f-(c.now()-d),e<0&&(e=0),a||e<9?i():k(i,e))}},C=function(a){var b,d,e=99,f=function(){b=null,a()},g=function(){var a=c.now()-d;a<e?k(g,e-a):(m||f)(f)};return function(){d=c.now(),b||(b=k(g,e))}},D=function(){var g,m,o,p,y,D,F,G,H,I,J,K,L=/^img$/i,M=/^iframe$/i,N="onscroll"in a&&!/(gle|ing)bot/.test(navigator.userAgent),O=0,P=0,Q=0,R=-1,S=function(a){Q--,(!a||Q<0||!a.target)&&(Q=0)},T=function(a){return null==K&&(K="hidden"==x(b.body,"visibility")),K||!("hidden"==x(a.parentNode,"visibility")&&"hidden"==x(a,"visibility"))},U=function(a,c){var d,e=a,g=T(a);for(G-=c,J+=c,H-=c,I+=c;g&&(e=e.offsetParent)&&e!=b.body&&e!=f;)(g=(x(e,"opacity")||1)>0)&&"visible"!=x(e,"overflow")&&(d=e.getBoundingClientRect(),g=I>d.left&&H<d.right&&J>d.top-1&&G<d.bottom+1);return g},V=function(){var a,c,h,j,k,l,n,o,q,r,s,t,u=d.elements;if((p=e.loadMode)&&Q<8&&(a=u.length)){for(c=0,R++;c<a;c++)if(u[c]&&!u[c]._lazyRace)if(!N||d.prematureUnveil&&d.prematureUnveil(u[c]))ba(u[c]);else if((o=u[c][i]("data-expand"))&&(l=1*o)||(l=P),r||(r=!e.expand||e.expand<1?f.clientHeight>500&&f.clientWidth>500?500:370:e.expand,d._defEx=r,s=r*e.expFactor,t=e.hFac,K=null,P<s&&Q<1&&R>2&&p>2&&!b.hidden?(P=s,R=0):P=p>1&&R>1&&Q<6?r:O),q!==l&&(D=innerWidth+l*t,F=innerHeight+l,n=-1*l,q=l),h=u[c].getBoundingClientRect(),(J=h.bottom)>=n&&(G=h.top)<=F&&(I=h.right)>=n*t&&(H=h.left)<=D&&(J||I||H||G)&&(e.loadHidden||T(u[c]))&&(m&&Q<3&&!o&&(p<3||R<4)||U(u[c],l))){if(ba(u[c]),k=!0,Q>9)break}else!k&&m&&!j&&Q<4&&R<4&&p>2&&(g[0]||e.preloadAfterLoad)&&(g[0]||!o&&(J||I||H||G||"auto"!=u[c][i](e.sizesAttr)))&&(j=g[0]||u[c]);j&&!k&&ba(j)}},W=B(V),X=function(a){var b=a.target;if(b._lazyCache)return void delete b._lazyCache;S(a),s(b,e.loadedClass),t(b,e.loadingClass),u(b,Z),v(b,"lazyloaded")},Y=A(X),Z=function(a){Y({target:a.target})},$=function(a,b){try{a.contentWindow.location.replace(b)}catch(c){a.src=b}},_=function(a){var b,c=a[i](e.srcsetAttr);(b=e.customMedia[a[i]("data-media")||a[i]("media")])&&a.setAttribute("media",b),c&&a.setAttribute("srcset",c)},aa=A(function(a,b,c,d,f){var g,h,j,l,m,p;(m=v(a,"lazybeforeunveil",b)).defaultPrevented||(d&&(c?s(a,e.autosizesClass):a.setAttribute("sizes",d)),h=a[i](e.srcsetAttr),g=a[i](e.srcAttr),f&&(j=a.parentNode,l=j&&n.test(j.nodeName||"")),p=b.firesLoad||"src"in a&&(h||g||l),m={target:a},s(a,e.loadingClass),p&&(clearTimeout(o),o=k(S,2500),u(a,Z,!0)),l&&q.call(j.getElementsByTagName("source"),_),h?a.setAttribute("srcset",h):g&&!l&&(M.test(a.nodeName)?$(a,g):a.src=g),f&&(h||l)&&w(a,{src:g})),a._lazyRace&&delete a._lazyRace,t(a,e.lazyClass),z(function(){var b=a.complete&&a.naturalWidth>1;p&&!b||(b&&s(a,"ls-is-cached"),X(m),a._lazyCache=!0,k(function(){"_lazyCache"in a&&delete a._lazyCache},9)),"lazy"==a.loading&&Q--},!0)}),ba=function(a){if(!a._lazyRace){var b,c=L.test(a.nodeName),d=c&&(a[i](e.sizesAttr)||a[i]("sizes")),f="auto"==d;(!f&&m||!c||!a[i]("src")&&!a.srcset||a.complete||r(a,e.errorClass)||!r(a,e.lazyClass))&&(b=v(a,"lazyunveilread").detail,f&&E.updateElem(a,!0,a.offsetWidth),a._lazyRace=!0,Q++,aa(a,b,f,d,c))}},ca=C(function(){e.loadMode=3,W()}),da=function(){3==e.loadMode&&(e.loadMode=2),ca()},ea=function(){if(!m){if(c.now()-y<999)return void k(ea,999);m=!0,e.loadMode=3,W(),j("scroll",da,!0)}};return{_:function(){y=c.now(),d.elements=b.getElementsByClassName(e.lazyClass),g=b.getElementsByClassName(e.lazyClass+" "+e.preloadClass),j("scroll",W,!0),j("resize",W,!0),j("pageshow",function(a){if(a.persisted){var c=b.querySelectorAll("."+e.loadingClass);c.length&&c.forEach&&l(function(){c.forEach(function(a){a.complete&&ba(a)})})}}),a.MutationObserver?new MutationObserver(W).observe(f,{childList:!0,subtree:!0,attributes:!0}):(f[h]("DOMNodeInserted",W,!0),f[h]("DOMAttrModified",W,!0),setInterval(W,999)),j("hashchange",W,!0),["focus","mouseover","click","load","transitionend","animationend"].forEach(function(a){b[h](a,W,!0)}),/d$|^c/.test(b.readyState)?ea():(j("load",ea),b[h]("DOMContentLoaded",W),k(ea,2e4)),d.elements.length?(V(),z._lsFlush()):W()},checkElems:W,unveil:ba,_aLSL:da}}(),E=function(){var a,c=A(function(a,b,c,d){var e,f,g;if(a._lazysizesWidth=d,d+="px",a.setAttribute("sizes",d),n.test(b.nodeName||""))for(e=b.getElementsByTagName("source"),f=0,g=e.length;f<g;f++)e[f].setAttribute("sizes",d);c.detail.dataAttr||w(a,c.detail)}),d=function(a,b,d){var e,f=a.parentNode;f&&(d=y(a,f,d),e=v(a,"lazybeforesizes",{width:d,dataAttr:!!b}),e.defaultPrevented||(d=e.detail.width)&&d!==a._lazysizesWidth&&c(a,f,e,d))},f=function(){var b,c=a.length;if(c)for(b=0;b<c;b++)d(a[b])},g=C(f);return{_:function(){a=b.getElementsByClassName(e.autosizesClass),j("resize",g)},checkElems:g,updateElem:d}}(),F=function(){!F.i&&b.getElementsByClassName&&(F.i=!0,E._(),D._())};return k(function(){e.init&&F()}),d={cfg:e,autoSizer:E,loader:D,init:F,uP:w,aC:s,rC:t,hC:r,fire:v,gW:y,rAF:z}});

async function getNewlyPublishedArticles(url, offset, params) {
	return new Promise(
		function ( resolve, reject ) {
			getJSON(
				url + 'ml-api/v2/posts' + ( params || '?' ) + '&offset=' + offset, // without "?" after "v2/posts".
				function( err, data ) {
					if ( err != null ) {
						// console.log( 'Something went wrong: ' + err );
						// spinner.style.display = 'none';
						resolve( 0 );
					} else {

						var newPosts = {
							posts: [],
							posts_ids: [],
						}
						var j        = 0;
						if ( mlFirstData.posts.length ) {
							for ( var i = 0; i < data.posts.length; i++ ) {
								if ('undefined' !== typeof( data.posts[ i ].post_id ) ) {
									if (  data.posts[ i ].post_id !== mlFirstData.posts[ 0 ].post_id ) {
										newPosts.posts.push( data.posts[ i ] );
										newPosts.posts_ids.push( { post_id: data.posts[ i ].post_id });
									} else {
										break;
									}
								}
							}
						} else {
							// add all new content.
							for ( var i = 0; i < data.posts.length; i++ ) {
								if ('undefined' !== typeof( data.posts[ i ].post_id ) ) {
									newPosts.posts.push( data.posts[ i ] );
									newPosts.posts_ids.push( { post_id: data.posts[ i ].post_id });
								}
							}
						}
						mlFirstData[ 'posts' ] = newPosts.posts_ids.concat( mlFirstData.posts );
						mlPostsData[ 'posts' ] = newPosts.posts.concat( mlPostsData.posts );

						var posts = newPosts.posts.length,
						list      = document.getElementById( 'article-list' );

						for ( var i = posts - 1 ; i >= 0; i-- ) {
							var article = createItemContent( newPosts.posts[i] );

							article.classList.add( 'new-item' );

							article.addEventListener(
								'click', function () {

									if ( this.getAttribute( 'data-ml-opening-method' ) == 'internal' ) {
										var postTitle = this.querySelector( '.article-list__content h2' ).innerText;
										nativeFunctions.handleLink( this.getAttribute( 'data-ml-post-permalink' ), postTitle, 'internal' );
									} else {
										nativeFunctions.handlePost( this.getAttribute( 'data-ml-post-id' ), this.getAttribute( 'data-ml-list-cat' ) || '' );
									}
								}
							);

							list.insertBefore( article, list.firstChild );
						}

						// fade in new articles
						setTimeout(
							function() {
								document.querySelectorAll( '.article-list__article.new-item' ).forEach(
									e => {
										if ( e.classList.contains( 'new-item' ) ) {
											e.classList.remove( 'new-item' );
											e.style.height = '0px';
											setTimeout(
												function () {
													e.style.height = 'auto';
												}, 100
											);
										} else {
											e.style.height = '0px';
											container.addEventListener(
												'transitionend', function () {
													e.classList.add( 'new-item' );
												}, {
													once: true
												}
											);
										}
									}
								);
							}, 100
						);

						var loaded = newPosts.posts.length;
						resolve( loaded );
					}
				}
			)
		}
	);
}

function fetchNewlyPublishedArticles() {
	getNewlyPublishedArticles( siteURL, 0, window.location.search )
	.then(
		function (more) {
			ml_infinite_loaded += more;
		}
	);
}

// check for new posts once every 60 seconds
function throttle(fn, timeout) {
	var timer = null;
	return function () {
		if ( ! timer) {
			timer = setTimeout(
				function() {
					fn();
					timer = null;
				}, timeout
			);
		}
	};
}

var mlCheckNewArticlesInterval;

function mlScrollList() {
	if ( document.querySelector( '.page__content' ).scrollTop == 0 ) {
		clearInterval( mlCheckNewArticlesInterval );
		fetchNewlyPublishedArticles();
		mlCheckNewArticlesInterval = setInterval(
			function() {
				fetchNewlyPublishedArticles();
			}, 60000
		);
	} else {
		clearInterval( mlCheckNewArticlesInterval );
	}
}

document.querySelector( '.page__content' ).addEventListener(
	'scroll', function() {
		throttle( mlScrollList(), 500 );
	}
);

/**
* Finds and executes scripts in a newly added element's body.
* Needed since innerHTML does not run scripts.
* https://stackoverflow.com/questions/2592092/executing-script-elements-inserted-with-innerhtml
*
* @param Argument body_el is an element in the dom.
*/
var exec_body_scripts = function( body_el ) {

	function nodeName(elem, name) {
		return elem.nodeName && elem.nodeName.toUpperCase() === name.toUpperCase();
	};

	function evalScript(elem) {
		var data = (elem.text || elem.textContent || elem.innerHTML || "" ),
		head = document.getElementsByTagName("head")[0] ||
		document.documentElement,
		script = document.createElement("script");

		script.type = "text/javascript";
		try {
			// doesn't work on ie...
			script.appendChild(document.createTextNode(data));
		} catch(e) {
			// IE has funky script nodes
			script.text = data;
		}

		head.insertBefore(script, head.firstChild);
		head.removeChild(script);
	};

	// main section of function
	var scripts = [],
	script,
	children_nodes = body_el.childNodes,
	child,
	i;

	for (i = 0; children_nodes[i]; i++) {
		child = children_nodes[i];
		if (nodeName(child, "script" ) &&
			(!child.type || child.type.toLowerCase() === "text/javascript")) {
			scripts.push(child);
		}
	}

	for (i = 0; scripts[i]; i++) {
		script = scripts[i];
		if (script.parentNode) {script.parentNode.removeChild(script);}
		evalScript(scripts[i]);
	}
};
