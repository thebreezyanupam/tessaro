/**
 * Slab: a terrazzo floor drawn in WebGL2.
 *
 * Two stages:
 *  - bake: the stone pattern (expensive, Voronoi chips) goes to a texture once per recipe.
 *  - show: cement, grinding and polish are composited from that texture every frame it changes.
 *
 * A separate mask texture records where the floor has been ground by hand.
 */
(() => {
	'use strict';

	const VERT = '#version 300 es\nin vec2 p;out vec2 v;void main(){v=p*.5+.5;gl_Position=vec4(p,0.,1.);}';

	const COMMON = [
		'#version 300 es',
		'precision highp float;',
		'in vec2 v;out vec4 o;',
		'float h12(vec2 p){vec3 q=fract(vec3(p.xyx)*.1031);q+=dot(q,q.yzx+33.33);return fract((q.x+q.y)*q.z);}',
		'vec2 h22(vec2 p){vec3 q=fract(vec3(p.xyx)*vec3(.1031,.103,.0973));q+=dot(q,q.yzx+33.33);return fract((q.xx+q.yz)*q.zy);}',
		'float vn(vec2 p){vec2 i=floor(p),f=fract(p);f=f*f*(3.-2.*f);return mix(mix(h12(i),h12(i+vec2(1,0)),f.x),mix(h12(i+vec2(0,1)),h12(i+vec2(1,1)),f.x),f.y);}',
		'float fbm(vec2 p){float a=.5,s=0.;for(int i=0;i<4;i++){s+=a*vn(p);p=p*2.03+7.1;a*=.5;}return s;}'
	].join('\n');

	const BAKE = COMMON + `
uniform vec2 uWorld;
uniform float uSeed,uSize,uDensity,uGrid,uHasLetters,uPx;
uniform vec3 uBinder,uInlay,uBrass;
uniform vec3 uStones[8];
uniform int uN;
uniform sampler2D uLetters;

/* Distance to the edge of the nearest Voronoi cell: gives crisp, angular, chip-like shapes. */
float cell(vec2 x,out vec2 id){
	vec2 n=floor(x),f=fract(x),mg=vec2(0),mr=vec2(0);
	float md=8.;
	for(int j=-1;j<=1;j++)for(int i=-1;i<=1;i++){
		vec2 g=vec2(i,j),r=g+h22(n+g)-f;
		float d=dot(r,r);
		if(d<md){md=d;mr=r;mg=g;}
	}
	md=8.;
	for(int j=-2;j<=2;j++)for(int i=-2;i<=2;i++){
		vec2 g=mg+vec2(i,j),r=g+h22(n+g)-f;
		if(dot(mr-r,mr-r)>.00001)md=min(md,dot(.5*(mr+r),normalize(r-mr)));
	}
	id=n+mg;
	return md;
}

vec4 chips(vec2 w,float size,float keep,float g0,float g1,float off){
	float s=1./size;
	vec2 q=w*s+uSeed*1.37+off;
	q+=(vec2(vn(q*2.3),vn(q*2.3+9.))-.5)*.16;
	vec2 id;
	float d=cell(q,id);
	if(h12(id+off+.5)>keep*uDensity)return vec4(0.);
	float gap=mix(g0,g1,h12(id+7.1));
	float a=smoothstep(gap,gap+uPx*s*1.4,d);
	int k=int(floor(h12(id+3.3+off)*float(uN)));
	k=min(k,uN-1);
	vec3 c=uStones[k];
	float vein=fbm(q*3.2+id*11.);
	c*=.92+.2*h12(id+1.9);
	c=mix(c,c*.7,smoothstep(.56,.72,vein)*.55);
	c=mix(c,min(c+.14,vec3(1.)),smoothstep(.34,.2,vein)*.4);
	return vec4(c,a);
}

void main(){
	vec2 w=v*uWorld;
	float inL=0.,strip=0.;
	if(uHasLetters>.5){
		vec4 L=texture(uLetters,vec2(v.x,1.-v.y));
		inL=L.r;
		strip=L.g;
	}
	vec3 binder=mix(uBinder,uInlay,inL);
	binder*=.975+.05*vn(w/3.)+.03*(fbm(w/90.+uSeed)-.5);
	vec3 c=binder;
	float mat=.1;

	vec4 dust=chips(w,uSize*.11,.7,.04,.2,3.);
	dust.a*=mix(1.,.3,inL);
	c=mix(c,mix(dust.rgb,uInlay*1.5,inL*.8),dust.a);mat=mix(mat,.5,dust.a);

	vec4 small=chips(w,uSize*.29,.5,.03,.18,11.);
	small.a*=1.-inL;
	c=mix(c,small.rgb,small.a);mat=mix(mat,.5,small.a);

	vec4 mid=chips(w,uSize*.58,.38,.03,.16,23.);
	mid.a*=1.-inL;
	c=mix(c,mid.rgb,mid.a);mat=mix(mat,.5,mid.a);

	vec4 big=chips(w,uSize,.3,.02,.14,37.);
	big.a*=1.-inL;
	c=mix(c,big.rgb,big.a);mat=mix(mat,.5,big.a);

	if(uGrid>0.){
		vec2 m=abs(mod(w+uGrid*.5,uGrid)-uGrid*.5);
		float line=1.-smoothstep(1.,1.+uPx*1.4,min(m.x,m.y));
		strip=max(strip,line);
	}
	vec3 brass=uBrass*(.92+.16*vn(w*vec2(.3,.03)));
	c=mix(c,brass,strip);
	mat=mix(mat,1.,strip);
	o=vec4(c,mat);
}`;

	const SHOW = COMMON + `
uniform sampler2D uT,uM;
uniform vec2 uWorld,uPtr;
uniform float uReveal,uPolish,uWet,uChips,uScroll,uPtrA;

void main(){
	vec4 t=texture(uT,v);
	float mat=t.a;
	float m=texture(uM,v).r;
	vec2 w=v*uWorld;
	float asp=uWorld.x/uWorld.y;

	/* The grind front spreads from the middle of the slab, irregular at the edge. */
	vec2 cv=(v-.5)*vec2(asp,1.)/max(asp,1.);
	float rad=length(cv)*1.42;
	float n=fbm(w/140.);
	float wipe=1.-smoothstep(uReveal-.12,uReveal,rad+(n-.5)*.3);
	float G=max(m,wipe);
	float g=smoothstep(.03,.4,G);
	float pf=smoothstep(.4,.98,G)*uPolish;

	/* Cement: trowel-marked, flecked. */
	vec3 cement=vec3(.56,.55,.52)*(.9+.2*fbm(w/190.));
	cement*=.97+.06*fbm(w/9.);
	cement-=.035*step(.86,h12(floor(w/2.)));
	cement+=.04*step(.985,h12(floor(w/3.)+4.));
	float chipM=smoothstep(.3,.5,mat)*(1.-smoothstep(.85,.95,mat));
	cement=mix(cement,cement*.6,uWet);
	cement=mix(cement,t.rgb*mix(.82,.62,uWet),chipM*uChips);

	/* Ground but unpolished: chalky and even. */
	float l=dot(t.rgb,vec3(.3,.59,.11));
	vec3 dull=mix(t.rgb,vec3(l),.42)*.76+.15;

	vec3 col=mix(cement,dull,g);
	col=mix(col,t.rgb,pf);

	/* Polished stone gets a soft, cloudy sheen with no hard edges. */
	float gloss=max(pf,uWet*(1.-g)*.8);
	float sheen=smoothstep(.4,.8,fbm(v*vec2(asp,1.)*1.2+vec2(uScroll*.45,3.)));
	col+=sheen*.05*gloss*vec3(1.,.97,.9);

	/* A low lamp follows the pointer. */
	vec2 dp=(v-uPtr)*uWorld;
	float pl=exp(-dot(dp,dp)/(2.*pow(min(uWorld.x,uWorld.y)*.2,2.)));
	col+=pl*uPtrA*gloss*.13*vec3(1.,.95,.84);

	/* Brass catches more light than stone. */
	if(mat>.9){
		col+=gloss*(sheen*.18+pl*uPtrA*.5)*vec3(1.,.86,.58);
		col*=mix(.75,1.,g);
	}
	o=vec4(col,1.);
}`;

	const BRUSH = COMMON + `
uniform vec2 uA,uB,uWorld;
uniform float uR;
void main(){
	vec2 p=v*uWorld,a=uA*uWorld,b=uB*uWorld,ba=b-a;
	float t=clamp(dot(p-a,ba)/max(dot(ba,ba),.001),0.,1.);
	float d=length(p-a-ba*t);
	float e=smoothstep(uR,uR*.55,d);
	o=vec4(vec3(e*.75),e*.75);
}`;

	const COPY = COMMON + 'uniform sampler2D uT;void main(){o=texture(uT,v);}';

	function rgb(hex) {
		const n = parseInt(String(hex).replace('#', ''), 16);
		return [((n >> 16) & 255) / 255, ((n >> 8) & 255) / 255, (n & 255) / 255];
	}

	function program(gl, vs, fs) {
		function sh(type, src) {
			const s = gl.createShader(type);
			gl.shaderSource(s, src);
			gl.compileShader(s);
			if (!gl.getShaderParameter(s, gl.COMPILE_STATUS)) {
				throw new Error(gl.getShaderInfoLog(s));
			}
			return s;
		}
		const p = gl.createProgram();
		gl.attachShader(p, sh(gl.VERTEX_SHADER, vs));
		gl.attachShader(p, sh(gl.FRAGMENT_SHADER, fs));
		gl.bindAttribLocation(p, 0, 'p');
		gl.linkProgram(p);
		if (!gl.getProgramParameter(p, gl.LINK_STATUS)) {
			throw new Error(gl.getProgramInfoLog(p));
		}
		const cache = {};
		return {
			p: p,
			u: (name) => {
				if (!(name in cache)) {
					cache[name] = gl.getUniformLocation(p, name);
				}
				return cache[name];
			}
		};
	}

	class Slab {
		constructor(canvas, opts) {
			this.canvas = canvas;
			this.o = Object.assign({
				seed: 1, binder: '#EAE2D0', stones: ['#A8402E', '#1B1A1C', '#F1EEE6'],
				size: 60, density: 1, grid: 0, inlay: '#6E2318', brass: '#B8934F',
				letters: null, lettersTall: null, fit: 0.88, maxDpr: 1.5
			}, opts || {});
			this.u = { reveal: 2, polish: 1, wet: 0, chips: 0, scroll: 0, px: 0.5, py: 0.5, pa: 0 };
			this.dirty = true;
			this.w = 0;
			this.h = 0;

			const gl = canvas.getContext('webgl2', { antialias: false, alpha: false, powerPreference: 'high-performance' });
			if (!gl) {
				throw new Error('WebGL2 unavailable');
			}
			this.gl = gl;
			this.pBake = program(gl, VERT, BAKE);
			this.pShow = program(gl, VERT, SHOW);
			this.pBrush = program(gl, VERT, BRUSH);
			this.pCopy = program(gl, VERT, COPY);

			const vao = gl.createVertexArray();
			gl.bindVertexArray(vao);
			const buf = gl.createBuffer();
			gl.bindBuffer(gl.ARRAY_BUFFER, buf);
			gl.bufferData(gl.ARRAY_BUFFER, new Float32Array([-1, -1, 3, -1, -1, 3]), gl.STATIC_DRAW);
			gl.enableVertexAttribArray(0);
			gl.vertexAttribPointer(0, 2, gl.FLOAT, false, 0, 0);
			this.letterTex = gl.createTexture();
		}

		target(w, h) {
			const gl = this.gl;
			const tex = gl.createTexture();
			gl.bindTexture(gl.TEXTURE_2D, tex);
			gl.texImage2D(gl.TEXTURE_2D, 0, gl.RGBA8, w, h, 0, gl.RGBA, gl.UNSIGNED_BYTE, null);
			gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_MIN_FILTER, gl.LINEAR);
			gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_MAG_FILTER, gl.LINEAR);
			gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_WRAP_S, gl.CLAMP_TO_EDGE);
			gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_WRAP_T, gl.CLAMP_TO_EDGE);
			const fb = gl.createFramebuffer();
			gl.bindFramebuffer(gl.FRAMEBUFFER, fb);
			gl.framebufferTexture2D(gl.FRAMEBUFFER, gl.COLOR_ATTACHMENT0, gl.TEXTURE_2D, tex, 0);
			return { tex: tex, fb: fb, w: w, h: h };
		}

		free(t) {
			if (t) {
				this.gl.deleteTexture(t.tex);
				this.gl.deleteFramebuffer(t.fb);
			}
		}

		/** Size to the element (or explicit CSS pixels), keeping any grinding already done. */
		resize(cssW, cssH) {
			const el = this.canvas;
			const w = Math.max(2, Math.round(cssW || el.clientWidth));
			const h = Math.max(2, Math.round(cssH || el.clientHeight));
			if (w === this.w && h === this.h) {
				return false;
			}
			const dpr = Math.min(window.devicePixelRatio || 1, this.o.maxDpr);
			const scale = Math.min(dpr, 2048 / Math.max(w, h));
			const W = Math.round(w * scale);
			const H = Math.round(h * scale);
			this.w = w;
			this.h = h;
			this.W = W;
			this.H = H;
			this.scale = scale;
			el.width = W;
			el.height = H;

			const gl = this.gl;
			const oldMask = this.mask;
			this.free(this.bakeT);
			this.bakeT = this.target(W, H);
			this.mask = this.target(Math.ceil(W / 2), Math.ceil(H / 2));
			gl.bindFramebuffer(gl.FRAMEBUFFER, this.mask.fb);
			gl.viewport(0, 0, this.mask.w, this.mask.h);
			gl.disable(gl.BLEND);
			if (oldMask) {
				gl.useProgram(this.pCopy.p);
				gl.activeTexture(gl.TEXTURE0);
				gl.bindTexture(gl.TEXTURE_2D, oldMask.tex);
				gl.uniform1i(this.pCopy.u('uT'), 0);
				gl.drawArrays(gl.TRIANGLES, 0, 3);
				this.free(oldMask);
			} else {
				gl.clearColor(0, 0, 0, 0);
				gl.clear(gl.COLOR_BUFFER_BIT);
			}
			this.bake();
			return true;
		}

		drawLetters() {
			const o = this.o;
			let lines = o.letters;
			if (o.lettersTall && this.w / this.h < 0.95) {
				lines = o.lettersTall;
			}
			const c = document.createElement('canvas');
			c.width = this.W;
			c.height = this.H;
			const x = c.getContext('2d');
			x.fillStyle = '#000';
			x.fillRect(0, 0, c.width, c.height);
			const family = '"Anybody"';
			const targetW = c.width * o.fit;
			const maxH = c.height * 0.66;
			const leading = 0.98;

			function setFont(px) {
				x.font = '900 ' + px + 'px ' + family;
				if ('fontStretch' in x) {
					x.fontStretch = 'expanded';
				}
			}

			// Each line is sized to the same measure, like a sign-painter's lockup.
			const metrics = lines.map((line) => {
				setFont(200);
				const width = x.measureText(line).width;
				return { line: line, px: Math.min(200 * targetW / width, c.height) };
			});
			let total = metrics.reduce((s, m) => s + m.px * leading * 0.8, 0);
			const fitScale = Math.min(1, maxH / total);
			metrics.forEach((m) => { m.px *= fitScale; });
			total *= fitScale;

			// The brass strip sits outside the letter. Stroking inside would also trace the
			// overlapping contours a variable font keeps within its glyphs (R, A, &).
			const strip = Math.max(2, Math.round(3 * this.scale));
			const edge = document.createElement('canvas');
			edge.width = c.width;
			edge.height = c.height;
			const ex = edge.getContext('2d');
			let y = (c.height - total) / 2 - c.height * 0.02;
			x.textAlign = ex.textAlign = 'center';
			x.textBaseline = ex.textBaseline = 'alphabetic';
			ex.lineJoin = 'round';
			x.globalCompositeOperation = 'lighter';
			metrics.forEach((m) => {
				y += m.px * leading * 0.8;
				setFont(m.px);
				ex.font = x.font;
				if ('fontStretch' in ex) {
					ex.fontStretch = 'expanded';
				}
				x.fillStyle = '#f00';
				x.fillText(m.line, c.width / 2, y);
				ex.globalCompositeOperation = 'source-over';
				ex.strokeStyle = '#0f0';
				ex.lineWidth = strip * 2;
				ex.strokeText(m.line, c.width / 2, y);
				ex.globalCompositeOperation = 'destination-out';
				ex.fillStyle = '#000';
				ex.fillText(m.line, c.width / 2, y);
			});
			x.drawImage(edge, 0, 0);

			const gl = this.gl;
			gl.activeTexture(gl.TEXTURE1);
			gl.bindTexture(gl.TEXTURE_2D, this.letterTex);
			gl.pixelStorei(gl.UNPACK_PREMULTIPLY_ALPHA_WEBGL, false);
			gl.texImage2D(gl.TEXTURE_2D, 0, gl.RGBA8, gl.RGBA, gl.UNSIGNED_BYTE, c);
			gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_MIN_FILTER, gl.LINEAR);
			gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_MAG_FILTER, gl.LINEAR);
			gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_WRAP_S, gl.CLAMP_TO_EDGE);
			gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_WRAP_T, gl.CLAMP_TO_EDGE);
		}

		/** Render the stone pattern for the current recipe. */
		bake() {
			if (!this.bakeT) {
				return;
			}
			const gl = this.gl;
			const o = this.o;
			const P = this.pBake;
			const has = o.letters && o.letters.length ? 1 : 0;
			if (has) {
				this.drawLetters();
			}
			gl.bindFramebuffer(gl.FRAMEBUFFER, this.bakeT.fb);
			gl.viewport(0, 0, this.W, this.H);
			gl.disable(gl.BLEND);
			gl.useProgram(P.p);
			gl.uniform2f(P.u('uWorld'), this.w, this.h);
			gl.uniform1f(P.u('uPx'), 1 / this.scale);
			gl.uniform1f(P.u('uSeed'), o.seed);
			// Full-bleed floors scale their stone with the screen; samples keep true size.
			const fluid = o.live ? Math.max(0.55, Math.min(1.35, this.w / 1280)) : 1;
			gl.uniform1f(P.u('uSize'), o.size * fluid);
			gl.uniform1f(P.u('uDensity'), o.density);
			gl.uniform1f(P.u('uGrid'), o.grid || 0);
			gl.uniform1f(P.u('uHasLetters'), has);
			gl.uniform3fv(P.u('uBinder'), rgb(o.binder));
			gl.uniform3fv(P.u('uInlay'), rgb(o.inlay));
			gl.uniform3fv(P.u('uBrass'), rgb(o.brass));
			const stones = o.stones.slice(0, 8);
			let flat = [];
			stones.forEach((s) => { flat = flat.concat(rgb(s)); });
			while (flat.length < 24) {
				flat.push(0);
			}
			gl.uniform3fv(P.u('uStones'), flat);
			gl.uniform1i(P.u('uN'), Math.max(1, stones.length));
			gl.activeTexture(gl.TEXTURE1);
			gl.bindTexture(gl.TEXTURE_2D, this.letterTex);
			gl.uniform1i(P.u('uLetters'), 1);
			gl.drawArrays(gl.TRIANGLES, 0, 3);
			this.dirty = true;
		}

		/** Change the recipe and re-bake. */
		setMix(mix) {
			Object.assign(this.o, mix);
			this.bake();
		}

		/** Grind a stroke. Coordinates are 0 to 1 with y up; radius is in CSS pixels. */
		paint(ax, ay, bx, by, r) {
			if (!this.mask) {
				return;
			}
			const gl = this.gl;
			const P = this.pBrush;
			gl.bindFramebuffer(gl.FRAMEBUFFER, this.mask.fb);
			gl.viewport(0, 0, this.mask.w, this.mask.h);
			gl.enable(gl.BLEND);
			gl.blendFunc(gl.ONE, gl.ONE_MINUS_SRC_COLOR);
			gl.useProgram(P.p);
			gl.uniform2f(P.u('uA'), ax, ay);
			gl.uniform2f(P.u('uB'), bx, by);
			gl.uniform2f(P.u('uWorld'), this.w, this.h);
			gl.uniform1f(P.u('uR'), r);
			gl.drawArrays(gl.TRIANGLES, 0, 3);
			gl.disable(gl.BLEND);
			this.dirty = true;
		}

		render() {
			if (!this.bakeT) {
				return;
			}
			const gl = this.gl;
			const P = this.pShow;
			const u = this.u;
			gl.bindFramebuffer(gl.FRAMEBUFFER, null);
			gl.viewport(0, 0, this.W, this.H);
			gl.disable(gl.BLEND);
			gl.useProgram(P.p);
			gl.activeTexture(gl.TEXTURE0);
			gl.bindTexture(gl.TEXTURE_2D, this.bakeT.tex);
			gl.uniform1i(P.u('uT'), 0);
			gl.activeTexture(gl.TEXTURE1);
			gl.bindTexture(gl.TEXTURE_2D, this.mask.tex);
			gl.uniform1i(P.u('uM'), 1);
			gl.uniform2f(P.u('uWorld'), this.w, this.h);
			gl.uniform2f(P.u('uPtr'), u.px, u.py);
			gl.uniform1f(P.u('uPtrA'), u.pa);
			gl.uniform1f(P.u('uReveal'), u.reveal);
			gl.uniform1f(P.u('uPolish'), u.polish);
			gl.uniform1f(P.u('uWet'), u.wet);
			gl.uniform1f(P.u('uChips'), u.chips);
			gl.uniform1f(P.u('uScroll'), u.scroll);
			gl.drawArrays(gl.TRIANGLES, 0, 3);
			this.dirty = false;
		}
	}

	window.TDSlab = Slab;
})();
