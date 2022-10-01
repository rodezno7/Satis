/*
	Proinf.net - 2019-VI-21

	Color selector with a click. 126 color hexagons.

	interface = piColorMap(options) - Create an hexagonal SVG color selector
		options:
			circle: = Round hexagons with a black circle?
			scale: = Initial size ratio. Default 1.0
		interface:
			.svg = The <svg> tag. Has to be inserted in the DOM
			.color = Change or gets current color
			.onHover = Callback send color below mouse
			.onClick = Callback send color when click
			.width = Pixel width of <svg>
			.height = Pixel height of <svg>
			.scale = Size ratio of <svg>

	interface = piColorPopup(options) - Shows the colormap in a popup
		options:
			dark: = Obscure background?
		interface:
			.show() = Popup appears
			.hide() = Popup disappears
			.toggle() = Conmutes visibility state
			.color = Change or gets current color
			.onClick = Callback send color when click

	interface = piColorButton(options) - Associate a <button> with the piColorPopup()
		interface:
			.add(buttons) = add one or more buttons
			.refresh(buttons) = shows the color stored in buttons
		Example: <button data-color="#F0AAC0">

	interface = piColorInput(options) - Associate a <input> with the piColorPopup()
		The input must be of type "picolor"
		Example: <input type="picolor" value="#9BAA0C">
*/

function piColorMap(options={}){

	// Options

	const withCircle = 'circle' in options? options.circle : true;
	let scale = 'scale' in options? parseFloat(options.scale) : 1;

	// Constants

	const XMAX = 234;
	const YMAX = 199;
	const GAP = 1;
	const WIDTH = GAP+XMAX+GAP; // 236
	const HEIGHT = withCircle? WIDTH : GAP+YMAX+GAP; // 236|201

	// Data imported from a <map> at https://www.w3schools.com/colors/colors_picker.asp

	const DATA = [ // Bounds (0,0)-(234,199)
		{ color:'#003366', vertex:[[63,0],[72,4],[72,15],[63,19],[54,15],[54,4]] }, // Up
		{ color:'#336699', vertex:[[81,0],[90,4],[90,15],[81,19],[72,15],[72,4]] },
		{ color:'#3366CC', vertex:[[99,0],[108,4],[108,15],[99,19],[90,15],[90,4]] },
		{ color:'#003399', vertex:[[117,0],[126,4],[126,15],[117,19],[108,15],[108,4]] },
		{ color:'#000099', vertex:[[135,0],[144,4],[144,15],[135,19],[126,15],[126,4]] },
		{ color:'#0000CC', vertex:[[153,0],[162,4],[162,15],[153,19],[144,15],[144,4]] },
		{ color:'#000066', vertex:[[171,0],[180,4],[180,15],[171,19],[162,15],[162,4]] },
		{ color:'#006666', vertex:[[54,15],[63,19],[63,30],[54,34],[45,30],[45,19]] },
		{ color:'#006699', vertex:[[72,15],[81,19],[81,30],[72,34],[63,30],[63,19]] },
		{ color:'#0099CC', vertex:[[90,15],[99,19],[99,30],[90,34],[81,30],[81,19]] },
		{ color:'#0066CC', vertex:[[108,15],[117,19],[117,30],[108,34],[99,30],[99,19]] },
		{ color:'#0033CC', vertex:[[126,15],[135,19],[135,30],[126,34],[117,30],[117,19]] },
		{ color:'#0000FF', vertex:[[144,15],[153,19],[153,30],[144,34],[135,30],[135,19]] },
		{ color:'#3333FF', vertex:[[162,15],[171,19],[171,30],[162,34],[153,30],[153,19]] },
		{ color:'#333399', vertex:[[180,15],[189,19],[189,30],[180,34],[171,30],[171,19]] },
		{ color:'#669999', vertex:[[45,30],[54,34],[54,45],[45,49],[36,45],[36,34]] },
		{ color:'#009999', vertex:[[63,30],[72,34],[72,45],[63,49],[54,45],[54,34]] },
		{ color:'#33CCCC', vertex:[[81,30],[90,34],[90,45],[81,49],[72,45],[72,34]] },
		{ color:'#00CCFF', vertex:[[99,30],[108,34],[108,45],[99,49],[90,45],[90,34]] },
		{ color:'#0099FF', vertex:[[117,30],[126,34],[126,45],[117,49],[108,45],[108,34]] },
		{ color:'#0066FF', vertex:[[135,30],[144,34],[144,45],[135,49],[126,45],[126,34]] },
		{ color:'#3366FF', vertex:[[153,30],[162,34],[162,45],[153,49],[144,45],[144,34]] },
		{ color:'#3333CC', vertex:[[171,30],[180,34],[180,45],[171,49],[162,45],[162,34]] },
		{ color:'#666699', vertex:[[189,30],[198,34],[198,45],[189,49],[180,45],[180,34]] },
		{ color:'#339966', vertex:[[36,45],[45,49],[45,60],[36,64],[27,60],[27,49]] },
		{ color:'#00CC99', vertex:[[54,45],[63,49],[63,60],[54,64],[45,60],[45,49]] },
		{ color:'#00FFCC', vertex:[[72,45],[81,49],[81,60],[72,64],[63,60],[63,49]] },
		{ color:'#00FFFF', vertex:[[90,45],[99,49],[99,60],[90,64],[81,60],[81,49]] },
		{ color:'#33CCFF', vertex:[[108,45],[117,49],[117,60],[108,64],[99,60],[99,49]] },
		{ color:'#3399FF', vertex:[[126,45],[135,49],[135,60],[126,64],[117,60],[117,49]] },
		{ color:'#6699FF', vertex:[[144,45],[153,49],[153,60],[144,64],[135,60],[135,49]] },
		{ color:'#6666FF', vertex:[[162,45],[171,49],[171,60],[162,64],[153,60],[153,49]] },
		{ color:'#6600FF', vertex:[[180,45],[189,49],[189,60],[180,64],[171,60],[171,49]] },
		{ color:'#6600CC', vertex:[[198,45],[207,49],[207,60],[198,64],[189,60],[189,49]] },
		{ color:'#339933', vertex:[[27,60],[36,64],[36,75],[27,79],[18,75],[18,64]] },
		{ color:'#00CC66', vertex:[[45,60],[54,64],[54,75],[45,79],[36,75],[36,64]] },
		{ color:'#00FF99', vertex:[[63,60],[72,64],[72,75],[63,79],[54,75],[54,64]] },
		{ color:'#66FFCC', vertex:[[81,60],[90,64],[90,75],[81,79],[72,75],[72,64]] },
		{ color:'#66FFFF', vertex:[[99,60],[108,64],[108,75],[99,79],[90,75],[90,64]] },
		{ color:'#66CCFF', vertex:[[117,60],[126,64],[126,75],[117,79],[108,75],[108,64]] },
		{ color:'#99CCFF', vertex:[[135,60],[144,64],[144,75],[135,79],[126,75],[126,64]] },
		{ color:'#9999FF', vertex:[[153,60],[162,64],[162,75],[153,79],[144,75],[144,64]] },
		{ color:'#9966FF', vertex:[[171,60],[180,64],[180,75],[171,79],[162,75],[162,64]] },
		{ color:'#9933FF', vertex:[[189,60],[198,64],[198,75],[189,79],[180,75],[180,64]] },
		{ color:'#9900FF', vertex:[[207,60],[216,64],[216,75],[207,79],[198,75],[198,64]] },
		{ color:'#006600', vertex:[[18,75],[27,79],[27,90],[18,94],[9,90],[9,79]] },
		{ color:'#00CC00', vertex:[[36,75],[45,79],[45,90],[36,94],[27,90],[27,79]] },
		{ color:'#00FF00', vertex:[[54,75],[63,79],[63,90],[54,94],[45,90],[45,79]] },
		{ color:'#66FF99', vertex:[[72,75],[81,79],[81,90],[72,94],[63,90],[63,79]] },
		{ color:'#99FFCC', vertex:[[90,75],[99,79],[99,90],[90,94],[81,90],[81,79]] },
		{ color:'#CCFFFF', vertex:[[108,75],[117,79],[117,90],[108,94],[99,90],[99,79]] },
		{ color:'#CCCCFF', vertex:[[126,75],[135,79],[135,90],[126,94],[117,90],[117,79]] },
		{ color:'#CC99FF', vertex:[[144,75],[153,79],[153,90],[144,94],[135,90],[135,79]] },
		{ color:'#CC66FF', vertex:[[162,75],[171,79],[171,90],[162,94],[153,90],[153,79]] },
		{ color:'#CC33FF', vertex:[[180,75],[189,79],[189,90],[180,94],[171,90],[171,79]] },
		{ color:'#CC00FF', vertex:[[198,75],[207,79],[207,90],[198,94],[189,90],[189,79]] },
		{ color:'#9900CC', vertex:[[216,75],[225,79],[225,90],[216,94],[207,90],[207,79]] },
		{ color:'#003300', vertex:[[9,90],[18,94],[18,105],[9,109],[0,105],[0,94]] }, // Left
		{ color:'#009933', vertex:[[27,90],[36,94],[36,105],[27,109],[18,105],[18,94]] },
		{ color:'#33CC33', vertex:[[45,90],[54,94],[54,105],[45,109],[36,105],[36,94]] },
		{ color:'#66FF66', vertex:[[63,90],[72,94],[72,105],[63,109],[54,105],[54,94]] },
		{ color:'#99FF99', vertex:[[81,90],[90,94],[90,105],[81,109],[72,105],[72,94]] },
		{ color:'#CCFFCC', vertex:[[99,90],[108,94],[108,105],[99,109],[90,105],[90,94]] },
		{ color:'#FFFFFF', vertex:[[117,90],[126,94],[126,105],[117,109],[108,105],[108,94]] },
		{ color:'#FFCCFF', vertex:[[135,90],[144,94],[144,105],[135,109],[126,105],[126,94]] },
		{ color:'#FF99FF', vertex:[[153,90],[162,94],[162,105],[153,109],[144,105],[144,94]] },
		{ color:'#FF66FF', vertex:[[171,90],[180,94],[180,105],[171,109],[162,105],[162,94]] },
		{ color:'#FF00FF', vertex:[[189,90],[198,94],[198,105],[189,109],[180,105],[180,94]] },
		{ color:'#CC00CC', vertex:[[207,90],[216,94],[216,105],[207,109],[198,105],[198,94]] },
		{ color:'#660066', vertex:[[225,90],[234,94],[234,105],[225,109],[216,105],[216,94]] }, // Right
		{ color:'#336600', vertex:[[18,105],[27,109],[27,120],[18,124],[9,120],[9,109]] },
		{ color:'#009900', vertex:[[36,105],[45,109],[45,120],[36,124],[27,120],[27,109]] },
		{ color:'#66FF33', vertex:[[54,105],[63,109],[63,120],[54,124],[45,120],[45,109]] },
		{ color:'#99FF66', vertex:[[72,105],[81,109],[81,120],[72,124],[63,120],[63,109]] },
		{ color:'#CCFF99', vertex:[[90,105],[99,109],[99,120],[90,124],[81,120],[81,109]] },
		{ color:'#FFFFCC', vertex:[[108,105],[117,109],[117,120],[108,124],[99,120],[99,109]] },
		{ color:'#FFCCCC', vertex:[[126,105],[135,109],[135,120],[126,124],[117,120],[117,109]] },
		{ color:'#FF99CC', vertex:[[144,105],[153,109],[153,120],[144,124],[135,120],[135,109]] },
		{ color:'#FF66CC', vertex:[[162,105],[171,109],[171,120],[162,124],[153,120],[153,109]] },
		{ color:'#FF33CC', vertex:[[180,105],[189,109],[189,120],[180,124],[171,120],[171,109]] },
		{ color:'#CC0099', vertex:[[198,105],[207,109],[207,120],[198,124],[189,120],[189,109]] },
		{ color:'#993399', vertex:[[216,105],[225,109],[225,120],[216,124],[207,120],[207,109]] },
		{ color:'#333300', vertex:[[27,120],[36,124],[36,135],[27,139],[18,135],[18,124]] },
		{ color:'#669900', vertex:[[45,120],[54,124],[54,135],[45,139],[36,135],[36,124]] },
		{ color:'#99FF33', vertex:[[63,120],[72,124],[72,135],[63,139],[54,135],[54,124]] },
		{ color:'#CCFF66', vertex:[[81,120],[90,124],[90,135],[81,139],[72,135],[72,124]] },
		{ color:'#FFFF99', vertex:[[99,120],[108,124],[108,135],[99,139],[90,135],[90,124]] },
		{ color:'#FFCC99', vertex:[[117,120],[126,124],[126,135],[117,139],[108,135],[108,124]] },
		{ color:'#FF9999', vertex:[[135,120],[144,124],[144,135],[135,139],[126,135],[126,124]] },
		{ color:'#FF6699', vertex:[[153,120],[162,124],[162,135],[153,139],[144,135],[144,124]] },
		{ color:'#FF3399', vertex:[[171,120],[180,124],[180,135],[171,139],[162,135],[162,124]] },
		{ color:'#CC3399', vertex:[[189,120],[198,124],[198,135],[189,139],[180,135],[180,124]] },
		{ color:'#990099', vertex:[[207,120],[216,124],[216,135],[207,139],[198,135],[198,124]] },
		{ color:'#666633', vertex:[[36,135],[45,139],[45,150],[36,154],[27,150],[27,139]] },
		{ color:'#99CC00', vertex:[[54,135],[63,139],[63,150],[54,154],[45,150],[45,139]] },
		{ color:'#CCFF33', vertex:[[72,135],[81,139],[81,150],[72,154],[63,150],[63,139]] },
		{ color:'#FFFF66', vertex:[[90,135],[99,139],[99,150],[90,154],[81,150],[81,139]] },
		{ color:'#FFCC66', vertex:[[108,135],[117,139],[117,150],[108,154],[99,150],[99,139]] },
		{ color:'#FF9966', vertex:[[126,135],[135,139],[135,150],[126,154],[117,150],[117,139]] },
		{ color:'#FF6666', vertex:[[144,135],[153,139],[153,150],[144,154],[135,150],[135,139]] },
		{ color:'#FF0066', vertex:[[162,135],[171,139],[171,150],[162,154],[153,150],[153,139]] },
		{ color:'#CC6699', vertex:[[180,135],[189,139],[189,150],[180,154],[171,150],[171,139]] },
		{ color:'#993366', vertex:[[198,135],[207,139],[207,150],[198,154],[189,150],[189,139]] },
		{ color:'#999966', vertex:[[45,150],[54,154],[54,165],[45,169],[36,165],[36,154]] },
		{ color:'#CCCC00', vertex:[[63,150],[72,154],[72,165],[63,169],[54,165],[54,154]] },
		{ color:'#FFFF00', vertex:[[81,150],[90,154],[90,165],[81,169],[72,165],[72,154]] },
		{ color:'#FFCC00', vertex:[[99,150],[108,154],[108,165],[99,169],[90,165],[90,154]] },
		{ color:'#FF9933', vertex:[[117,150],[126,154],[126,165],[117,169],[108,165],[108,154]] },
		{ color:'#FF6600', vertex:[[135,150],[144,154],[144,165],[135,169],[126,165],[126,154]] },
		{ color:'#FF5050', vertex:[[153,150],[162,154],[162,165],[153,169],[144,165],[144,154]] },
		{ color:'#CC0066', vertex:[[171,150],[180,154],[180,165],[171,169],[162,165],[162,154]] },
		{ color:'#660033', vertex:[[189,150],[198,154],[198,165],[189,169],[180,165],[180,154]] },
		{ color:'#996633', vertex:[[54,165],[63,169],[63,180],[54,184],[45,180],[45,169]] },
		{ color:'#CC9900', vertex:[[72,165],[81,169],[81,180],[72,184],[63,180],[63,169]] },
		{ color:'#FF9900', vertex:[[90,165],[99,169],[99,180],[90,184],[81,180],[81,169]] },
		{ color:'#CC6600', vertex:[[108,165],[117,169],[117,180],[108,184],[99,180],[99,169]] },
		{ color:'#FF3300', vertex:[[126,165],[135,169],[135,180],[126,184],[117,180],[117,169]] },
		{ color:'#FF0000', vertex:[[144,165],[153,169],[153,180],[144,184],[135,180],[135,169]] },
		{ color:'#CC0000', vertex:[[162,165],[171,169],[171,180],[162,184],[153,180],[153,169]] },
		{ color:'#990033', vertex:[[180,165],[189,169],[189,180],[180,184],[171,180],[171,169]] },
		{ color:'#663300', vertex:[[63,180],[72,184],[72,195],[63,199],[54,195],[54,184]] },
		{ color:'#996600', vertex:[[81,180],[90,184],[90,195],[81,199],[72,195],[72,184]] },
		{ color:'#CC3300', vertex:[[99,180],[108,184],[108,195],[99,199],[90,195],[90,184]] },
		{ color:'#993300', vertex:[[117,180],[126,184],[126,195],[117,199],[108,195],[108,184]] },
		{ color:'#990000', vertex:[[135,180],[144,184],[144,195],[135,199],[126,195],[126,184]] },
		{ color:'#800000', vertex:[[153,180],[162,184],[162,195],[153,199],[144,195],[144,184]] },
		{ color:'#993333', vertex:[[171,180],[180,184],[180,195],[171,199],[162,195],[162,184]] } // Bottom
	];

	// Elements

	const svg = createSVG();
	const selection = createSelection();

	// Colors

	let colorHover = '#000000';
	let colorClick = '#000000';

	// Callbacks

	let onHover = color => {}; //console.log(colorHover);
	let onClick = color => {}; //console.log(colorClick);

	// Main

	listenSVG();

	// Interface

	return {
		get svg() { return svg },
		get color() { return colorClick },
		set color(value) { setColor(value) },
		set onHover (callback) { onHover = callback },
		set onClick (callback) { onClick = callback },
		get width() { return WIDTH },
		get height() { return HEIGHT },
		set scale(ratio) { setScale(ratio) },
		clickColor,
	};

	// SVG constructor

	function createSVG() {
		const xmlns = "http://www.w3.org/2000/svg";
		const svg = document.createElementNS(xmlns, 'svg');
		svg.setAttribute('width', WIDTH*scale);
		svg.setAttribute('height', HEIGHT*scale);
		svg.setAttribute('viewBox', `0 0 ${WIDTH} ${HEIGHT}`);
		if (withCircle) svg.appendChild(createCircle());
		for(const item of DATA) {
			svg.appendChild(createPath(item));
		}
		return svg;

		function createCircle() {
			const circle = document.createElementNS(xmlns, 'circle');
			circle.setAttribute('r', WIDTH/2);
			circle.setAttribute('cy', HEIGHT/2);
			circle.setAttribute('cx', WIDTH/2);
			circle.setAttribute('style', 'fill:#000000');
			return circle;
		}
		function createPath(item) { // Ex: <path d="M 63,0 72,4 72,15 63,19 54,15 54,4 z" style="fill:#003366" />
			item.path = document.createElementNS(xmlns, 'path');
			moveVertex(item.vertex);
			item.path.setAttribute('d', `M ${item.vertex.join(' ')} z`);
			item.path.setAttribute('style', `fill:${item.color};stroke:${item.color};stroke-width:1`);
			return item.path;
		}
		function moveVertex(vertex) {
			const offset = withCircle? (XMAX-YMAX)/2 : 0;
			for (let i=0; i<vertex.length; i++) {
				vertex[i][0] += GAP;
				vertex[i][1] += GAP + offset;
			}
		}
	}

	// SVG selection

	function createSelection() {
		const xmlns = "http://www.w3.org/2000/svg";
		const path = document.createElementNS(xmlns, 'path');
		path.setAttribute('style', 'fill:transparent;stroke:#000000;stroke-width:2');
		svg.appendChild(path);
		return path;
	}

	// Change

	function setColor(newColor) {
		const node = getNodeFrom(newColor);
		selectPath(node);
		if (node) colorClick = newColor;
	}

	function clickColor(newColor) {
		const node = getNodeFrom(newColor);
		if (node) clickPath(node);
		else selectPath(null);
	}

	function getNodeFrom(color) {
		for (const item of DATA) {
			if (item.color == color)
				return item.path;
		}
		return null;
	}

	function setScale(ratio) {
		scale = ratio;
		svg.setAttribute('width', WIDTH*scale);
		svg.setAttribute('height', HEIGHT*scale);
	}

	// Listen events

	function listenSVG() {
		const NODE_NAMES = ['path','circle'];
		svg.addEventListener('mouseover', function(event) {
			const node = event.target;
			if (NODE_NAMES.includes(node.nodeName) && node != selection) hoverPath(node);
		});
		svg.addEventListener('click', function(event) {
			const node = event.target;
			if (NODE_NAMES.includes(node.nodeName) && node != selection) clickPath(node);
		});
		svg.addEventListener('mouseout', function(event) {
			const node = event.target;
			if (node != selection) if (onHover) onHover(colorClick);
		});
	}
	function hoverPath(node) {
		colorHover = scanColor(node.style.fill);
		if (onHover) onHover(colorHover);
	}
	function clickPath(node) {
		hoverPath(node);
		selectPath(node);
		colorClick = scanColor(node.style.fill);
		if (onClick) onClick(colorClick);
	}
	function selectPath(node) {
		if (node) selection.setAttribute('d', node.getAttribute('d'));
		else selection.removeAttribute('d'); // hide it
	}

	// Color management

	function scanColor(color) {
		if (color.substr(0,3) == 'rgb') return rgbToHex(explodeRgbStyle(color)).toUpperCase();
		else if (color.substr(0, 1) == '#') return color.toUpperCase();
		else return color;
	}
	function explodeRgbStyle(color) { // 'rgb(0, 128, 255)' --> {red:0, green:128, blue:255}
		const items = /([0-9]+)[\s,]+([0-9]+)[\s,]+([0-9]+)/.exec(color);
		return items? {
			red: parseInt(items[1]),
			green: parseInt(items[2]),
			blue: parseInt(items[3])
		} : null;
	}
	function rgbToHex(rgb) { // Example: {red:0, green:51, blue:255} --> '#0033ff'
		return "#" + ((1 << 24) + (rgb.red << 16) + (rgb.green << 8) + rgb.blue).toString(16).slice(1);
	}

};

//=======================================

function piColorPopup(options={}){

	// Options

	const isDark = 'dark' in options? options.dark : true;

	// Ancestor

	const colormap = piColorMap(options);

	// Elements

	const container = createContainer();

	// Main

	toggle(false);
	document.body.appendChild(container);

	// Interface

	return {
		toggle,
		show: () => toggle(true),
		hide: () => toggle(false),
		get color() { return colormap.color },
		set color(value) { colormap.color = value },
		set onClick (callback) { colormap.onClick = callback },
	};

	// Constructor

	function createContainer() {
		const div = document.createElement('div');
		fullScreen();
		centerContent();
		if (isDark) darkBackground();
		div.addEventListener('click', clickContainer);
		div.appendChild(colormap.svg);
		return div;

		function fullScreen() {
			div.style.position = 'fixed';
			div.style.top = div.style.left = 0;
			div.style.bottom = div.style.right = 0;
		}
		function centerContent() {
			div.style.display = 'flex';
			div.style.justifyContent = 'center';
			div.style.alignItems = 'center';
		}
		function darkBackground() {
			div.style.backgroundColor = 'rgba(0,0,0,0.5)';
		}
	}

	// Events

	function clickContainer(event) {
		toggle(false);
	}

	// Visibility

	function toggle(force=undefined) {
		const visible = force == undefined?
			container.style.display == 'none' : force;
		container.style.display = visible? 'flex' : 'none';
	}

};

//=======================================

function piColorButton(options={}){

	// Ancestor

	const colorPopup = piColorPopup(options);

	// Interface

	return {
		add,
		refresh,
	};

	// Methods

	function add(argument) {
		process(argument, element => {
			colorizeElement(element);
			addElement(element);
		});
	}

	function refresh(argument) {
		process(argument, element => {
			colorizeElement(element);
		});
	}

	// Private

	function process(argument, callback) {
		if ('length' in argument) {
			for(const element of argument) {
				callback(element);
			}
		} else callback(argument);
	}

	function addElement(element) {
		element.addEventListener('click', event => {
			colorPopup.color = element.dataset.color;
			colorPopup.onClick = color => {
				element.dataset.color = color;
				colorizeElement(element);
			};
			colorPopup.toggle();
		});
	}

	function colorizeElement(element) {
		const color = element.dataset.color || '';
		element.style.backgroundColor = color;
	}

};

//=======================================

function piColorInput(options={}){

	// Options

	const type = 'type' in options? options.type : 'picolor';
	const ratio = 'ratio' in options? options.ratio : -0.25;

	// Ancestor

	let colorPopup = null;

	// Main

	const inputs = document.querySelectorAll(`input[type="${type}"]`);
	add(inputs);

	// Interface

	return {
		add,
		refresh,
	};

	// Methods

	function add(argument) {
		process(argument, input => {
			colorizeInput(input);
			addInput(input);
		});
	}

	function refresh(argument) {
		process(argument, input => {
			colorizeInput(input);
		});
	}

	// Private

	function addInput(input) {
		assertColorPopup();
		input.addEventListener('click', event => {
			if (withinColorSample()) showPopup();
		});
		input.addEventListener('keyup', event => {
			colorizeInput(input);
		});
		input.addEventListener('mousemove', event => {
			input.style.cursor = withinColorSample()? 'pointer' : 'initial';
		});

		function withinColorSample() {
			if (ratio < 0) { // Left side
				return mouseRatioX() < -ratio;
			} else { // Right side
				return mouseRatioX() > (1-ratio);
			}
		}
		function mouseRatioX() {
			const rect = event.target.getBoundingClientRect();
			const withinX = event.clientX - rect.left;
			return withinX / rect.width;
		}
		function showPopup() {
			colorPopup.color = input.value;
			colorPopup.onClick = color => {
				input.value = color;
				colorizeInput(input);
				input.focus();
			};
			colorPopup.toggle();
		}
	}

	function assertColorPopup() {
		if (colorPopup == null) {
			colorPopup = piColorPopup(options);
		}
	}

	function process(argument, callback) {
		if ('length' in argument) {
			for(const input of argument) {
				callback(input);
			}
		} else callback(argument);
	}

	function colorizeInput(input) {
		if (ratio < 0) { // Left side
			const percent = -ratio*100;
			input.style.backgroundImage = `linear-gradient(to right, ${input.value} ${percent}%, transparent ${percent}%)`;
			const rect = input.getBoundingClientRect();
			input.style.boxSizing = 'border-box';
			input.style.paddingLeft = `${-ratio * rect.width}px`;
		} else { // Right side
			const percent = (1-ratio)*100;
			input.style.backgroundImage = `linear-gradient(to right, transparent ${percent}%, ${input.value} ${percent}%)`;
			input.style.paddingLeft = 0;
		}
	}

};