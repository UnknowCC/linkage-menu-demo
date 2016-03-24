/*创建网页异步处理核心对象XMLHttpRequest对象*/
function creatXHR() {
	if (typeof XMLHttpRequest != "undefined") {
		return new XMLHttpRequest();
	} else if (typeof ActiveXObject != "undefined") {
		// 对IE7之前的浏览器版本进行兼容判断
		if (typeof arguments.callee.activeXString != "string") {
			var versions = ["MSXML2.XMLHttp.6.0", "MSXML2.XMLHttp.3.0", "MSXML2.XMLHttp"];
			var i, len;
			for (i = 0, len = versions.length; i < len; i++) {
				try {
					new ActiveXObject(versions);
					arguments.callee.activeXString = versions[i];
					break;
				} catch (ex) {
					// catch exception but do nothing
				}
			}
		}
		return new ActiveXObject(arguments.callee.activeXString);
	} else {
		throw new Error("No XML object available.");
	}
}

/**
 * 为请求地址设置辅助函数
 * @param {string} url   请求地址
 * @param {string} name  参数名称
 * @param {string} value 参数值
 */
function addUrlParam(url, name, value) {
	url += (url.indexOf("?") == -1 ? "?" : "&");
	url += encodeURIComponent(name) + '=' + encodeURIComponent(value);
	return url;
}

/**
 * 自定义函数
 * @example
 * <button onclick="change()"></button>
 * @return {mixed} 
 */
function change() {
	var xhr = creatXHR();
	xhr.onreadystatechange = function() {
		if (xhr.readyState == 4) {
			if ((xhr.status >= 200 && xhr.status < 300) || xhr.status == 304) {
				alert(xhr.responseText);
			} else {
				alert("Request was unsuccessful: " + xhr.status);
			}
		}
	};

	var url = 'subMenuTest.php';
	url = addUrlParam(url, 'id', 1);
	// GET 请求应用实例
	xhr.open('get', url, true);
	xhr.send(null);
}