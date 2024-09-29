(() => {
  // node_modules/@codemirror/text/dist/index.js
  var extend = /* @__PURE__ */ "lc,34,7n,7,7b,19,,,,2,,2,,,20,b,1c,l,g,,2t,7,2,6,2,2,,4,z,,u,r,2j,b,1m,9,9,,o,4,,9,,3,,5,17,3,3b,f,,w,1j,,,,4,8,4,,3,7,a,2,t,,1m,,,,2,4,8,,9,,a,2,q,,2,2,1l,,4,2,4,2,2,3,3,,u,2,3,,b,2,1l,,4,5,,2,4,,k,2,m,6,,,1m,,,2,,4,8,,7,3,a,2,u,,1n,,,,c,,9,,14,,3,,1l,3,5,3,,4,7,2,b,2,t,,1m,,2,,2,,3,,5,2,7,2,b,2,s,2,1l,2,,,2,4,8,,9,,a,2,t,,20,,4,,2,3,,,8,,29,,2,7,c,8,2q,,2,9,b,6,22,2,r,,,,,,1j,e,,5,,2,5,b,,10,9,,2u,4,,6,,2,2,2,p,2,4,3,g,4,d,,2,2,6,,f,,jj,3,qa,3,t,3,t,2,u,2,1s,2,,7,8,,2,b,9,,19,3,3b,2,y,,3a,3,4,2,9,,6,3,63,2,2,,1m,,,7,,,,,2,8,6,a,2,,1c,h,1r,4,1c,7,,,5,,14,9,c,2,w,4,2,2,,3,1k,,,2,3,,,3,1m,8,2,2,48,3,,d,,7,4,,6,,3,2,5i,1m,,5,ek,,5f,x,2da,3,3x,,2o,w,fe,6,2x,2,n9w,4,,a,w,2,28,2,7k,,3,,4,,p,2,5,,47,2,q,i,d,,12,8,p,b,1a,3,1c,,2,4,2,2,13,,1v,6,2,2,2,2,c,,8,,1b,,1f,,,3,2,2,5,2,,,16,2,8,,6m,,2,,4,,fn4,,kh,g,g,g,a6,2,gt,,6a,,45,5,1ae,3,,2,5,4,14,3,4,,4l,2,fx,4,ar,2,49,b,4w,,1i,f,1k,3,1d,4,2,2,1x,3,10,5,,8,1q,,c,2,1g,9,a,4,2,,2n,3,2,,,2,6,,4g,,3,8,l,2,1l,2,,,,,m,,e,7,3,5,5f,8,2,3,,,n,,29,,2,6,,,2,,,2,,2,6j,,2,4,6,2,,2,r,2,2d,8,2,,,2,2y,,,,2,6,,,2t,3,2,4,,5,77,9,,2,6t,,a,2,,,4,,40,4,2,2,4,,w,a,14,6,2,4,8,,9,6,2,3,1a,d,,2,ba,7,,6,,,2a,m,2,7,,2,,2,3e,6,3,,,2,,7,,,20,2,3,,,,9n,2,f0b,5,1n,7,t4,,1r,4,29,,f5k,2,43q,,,3,4,5,8,8,2,7,u,4,44,3,1iz,1j,4,1e,8,,e,,m,5,,f,11s,7,,h,2,7,,2,,5,79,7,c5,4,15s,7,31,7,240,5,gx7k,2o,3k,6o".split(",").map((s) => s ? parseInt(s, 36) : 1);
  for (let i = 1; i < extend.length; i++)
    extend[i] += extend[i - 1];
  function isExtendingChar(code) {
    for (let i = 1; i < extend.length; i += 2)
      if (extend[i] > code)
        return extend[i - 1] <= code;
    return false;
  }
  function isRegionalIndicator(code) {
    return code >= 127462 && code <= 127487;
  }
  var ZWJ = 8205;
  function findClusterBreak(str, pos, forward = true, includeExtending = true) {
    return (forward ? nextClusterBreak : prevClusterBreak)(str, pos, includeExtending);
  }
  function nextClusterBreak(str, pos, includeExtending) {
    if (pos == str.length)
      return pos;
    if (pos && surrogateLow(str.charCodeAt(pos)) && surrogateHigh(str.charCodeAt(pos - 1)))
      pos--;
    let prev = codePointAt(str, pos);
    pos += codePointSize(prev);
    while (pos < str.length) {
      let next = codePointAt(str, pos);
      if (prev == ZWJ || next == ZWJ || includeExtending && isExtendingChar(next)) {
        pos += codePointSize(next);
        prev = next;
      } else if (isRegionalIndicator(next)) {
        let countBefore = 0, i = pos - 2;
        while (i >= 0 && isRegionalIndicator(codePointAt(str, i))) {
          countBefore++;
          i -= 2;
        }
        if (countBefore % 2 == 0)
          break;
        else
          pos += 2;
      } else {
        break;
      }
    }
    return pos;
  }
  function prevClusterBreak(str, pos, includeExtending) {
    while (pos > 0) {
      let found = nextClusterBreak(str, pos - 2, includeExtending);
      if (found < pos)
        return found;
      pos--;
    }
    return 0;
  }
  function surrogateLow(ch) {
    return ch >= 56320 && ch < 57344;
  }
  function surrogateHigh(ch) {
    return ch >= 55296 && ch < 56320;
  }
  function codePointAt(str, pos) {
    let code0 = str.charCodeAt(pos);
    if (!surrogateHigh(code0) || pos + 1 == str.length)
      return code0;
    let code1 = str.charCodeAt(pos + 1);
    if (!surrogateLow(code1))
      return code0;
    return (code0 - 55296 << 10) + (code1 - 56320) + 65536;
  }
  function fromCodePoint(code) {
    if (code <= 65535)
      return String.fromCharCode(code);
    code -= 65536;
    return String.fromCharCode((code >> 10) + 55296, (code & 1023) + 56320);
  }
  function codePointSize(code) {
    return code < 65536 ? 1 : 2;
  }
  function countColumn(string2, tabSize, to = string2.length) {
    let n = 0;
    for (let i = 0; i < to; ) {
      if (string2.charCodeAt(i) == 9) {
        n += tabSize - n % tabSize;
        i++;
      } else {
        n++;
        i = findClusterBreak(string2, i);
      }
    }
    return n;
  }
  function findColumn(string2, col, tabSize, strict) {
    for (let i = 0, n = 0; ; ) {
      if (n >= col)
        return i;
      if (i == string2.length)
        break;
      n += string2.charCodeAt(i) == 9 ? tabSize - n % tabSize : 1;
      i = findClusterBreak(string2, i);
    }
    return strict === true ? -1 : string2.length;
  }
  var Text = class {
    constructor() {
    }
    lineAt(pos) {
      if (pos < 0 || pos > this.length)
        throw new RangeError(`Invalid position ${pos} in document of length ${this.length}`);
      return this.lineInner(pos, false, 1, 0);
    }
    line(n) {
      if (n < 1 || n > this.lines)
        throw new RangeError(`Invalid line number ${n} in ${this.lines}-line document`);
      return this.lineInner(n, true, 1, 0);
    }
    replace(from2, to, text) {
      let parts = [];
      this.decompose(0, from2, parts, 2);
      if (text.length)
        text.decompose(0, text.length, parts, 1 | 2);
      this.decompose(to, this.length, parts, 1);
      return TextNode.from(parts, this.length - (to - from2) + text.length);
    }
    append(other) {
      return this.replace(this.length, this.length, other);
    }
    slice(from2, to = this.length) {
      let parts = [];
      this.decompose(from2, to, parts, 0);
      return TextNode.from(parts, to - from2);
    }
    eq(other) {
      if (other == this)
        return true;
      if (other.length != this.length || other.lines != this.lines)
        return false;
      let start = this.scanIdentical(other, 1), end = this.length - this.scanIdentical(other, -1);
      let a = new RawTextCursor(this), b = new RawTextCursor(other);
      for (let skip = start, pos = start; ; ) {
        a.next(skip);
        b.next(skip);
        skip = 0;
        if (a.lineBreak != b.lineBreak || a.done != b.done || a.value != b.value)
          return false;
        pos += a.value.length;
        if (a.done || pos >= end)
          return true;
      }
    }
    iter(dir = 1) {
      return new RawTextCursor(this, dir);
    }
    iterRange(from2, to = this.length) {
      return new PartialTextCursor(this, from2, to);
    }
    iterLines(from2, to) {
      let inner;
      if (from2 == null) {
        inner = this.iter();
      } else {
        if (to == null)
          to = this.lines + 1;
        let start = this.line(from2).from;
        inner = this.iterRange(start, Math.max(start, to == this.lines + 1 ? this.length : to <= 1 ? 0 : this.line(to - 1).to));
      }
      return new LineCursor(inner);
    }
    toString() {
      return this.sliceString(0);
    }
    toJSON() {
      let lines = [];
      this.flatten(lines);
      return lines;
    }
    static of(text) {
      if (text.length == 0)
        throw new RangeError("A document must have at least one line");
      if (text.length == 1 && !text[0])
        return Text.empty;
      return text.length <= 32 ? new TextLeaf(text) : TextNode.from(TextLeaf.split(text, []));
    }
  };
  var TextLeaf = class extends Text {
    constructor(text, length = textLength(text)) {
      super();
      this.text = text;
      this.length = length;
    }
    get lines() {
      return this.text.length;
    }
    get children() {
      return null;
    }
    lineInner(target, isLine, line, offset) {
      for (let i = 0; ; i++) {
        let string2 = this.text[i], end = offset + string2.length;
        if ((isLine ? line : end) >= target)
          return new Line(offset, end, line, string2);
        offset = end + 1;
        line++;
      }
    }
    decompose(from2, to, target, open) {
      let text = from2 <= 0 && to >= this.length ? this : new TextLeaf(sliceText(this.text, from2, to), Math.min(to, this.length) - Math.max(0, from2));
      if (open & 1) {
        let prev = target.pop();
        let joined = appendText(text.text, prev.text.slice(), 0, text.length);
        if (joined.length <= 32) {
          target.push(new TextLeaf(joined, prev.length + text.length));
        } else {
          let mid = joined.length >> 1;
          target.push(new TextLeaf(joined.slice(0, mid)), new TextLeaf(joined.slice(mid)));
        }
      } else {
        target.push(text);
      }
    }
    replace(from2, to, text) {
      if (!(text instanceof TextLeaf))
        return super.replace(from2, to, text);
      let lines = appendText(this.text, appendText(text.text, sliceText(this.text, 0, from2)), to);
      let newLen = this.length + text.length - (to - from2);
      if (lines.length <= 32)
        return new TextLeaf(lines, newLen);
      return TextNode.from(TextLeaf.split(lines, []), newLen);
    }
    sliceString(from2, to = this.length, lineSep = "\n") {
      let result = "";
      for (let pos = 0, i = 0; pos <= to && i < this.text.length; i++) {
        let line = this.text[i], end = pos + line.length;
        if (pos > from2 && i)
          result += lineSep;
        if (from2 < end && to > pos)
          result += line.slice(Math.max(0, from2 - pos), to - pos);
        pos = end + 1;
      }
      return result;
    }
    flatten(target) {
      for (let line of this.text)
        target.push(line);
    }
    scanIdentical() {
      return 0;
    }
    static split(text, target) {
      let part = [], len = -1;
      for (let line of text) {
        part.push(line);
        len += line.length + 1;
        if (part.length == 32) {
          target.push(new TextLeaf(part, len));
          part = [];
          len = -1;
        }
      }
      if (len > -1)
        target.push(new TextLeaf(part, len));
      return target;
    }
  };
  var TextNode = class extends Text {
    constructor(children, length) {
      super();
      this.children = children;
      this.length = length;
      this.lines = 0;
      for (let child of children)
        this.lines += child.lines;
    }
    lineInner(target, isLine, line, offset) {
      for (let i = 0; ; i++) {
        let child = this.children[i], end = offset + child.length, endLine = line + child.lines - 1;
        if ((isLine ? endLine : end) >= target)
          return child.lineInner(target, isLine, line, offset);
        offset = end + 1;
        line = endLine + 1;
      }
    }
    decompose(from2, to, target, open) {
      for (let i = 0, pos = 0; pos <= to && i < this.children.length; i++) {
        let child = this.children[i], end = pos + child.length;
        if (from2 <= end && to >= pos) {
          let childOpen = open & ((pos <= from2 ? 1 : 0) | (end >= to ? 2 : 0));
          if (pos >= from2 && end <= to && !childOpen)
            target.push(child);
          else
            child.decompose(from2 - pos, to - pos, target, childOpen);
        }
        pos = end + 1;
      }
    }
    replace(from2, to, text) {
      if (text.lines < this.lines)
        for (let i = 0, pos = 0; i < this.children.length; i++) {
          let child = this.children[i], end = pos + child.length;
          if (from2 >= pos && to <= end) {
            let updated = child.replace(from2 - pos, to - pos, text);
            let totalLines = this.lines - child.lines + updated.lines;
            if (updated.lines < totalLines >> 5 - 1 && updated.lines > totalLines >> 5 + 1) {
              let copy = this.children.slice();
              copy[i] = updated;
              return new TextNode(copy, this.length - (to - from2) + text.length);
            }
            return super.replace(pos, end, updated);
          }
          pos = end + 1;
        }
      return super.replace(from2, to, text);
    }
    sliceString(from2, to = this.length, lineSep = "\n") {
      let result = "";
      for (let i = 0, pos = 0; i < this.children.length && pos <= to; i++) {
        let child = this.children[i], end = pos + child.length;
        if (pos > from2 && i)
          result += lineSep;
        if (from2 < end && to > pos)
          result += child.sliceString(from2 - pos, to - pos, lineSep);
        pos = end + 1;
      }
      return result;
    }
    flatten(target) {
      for (let child of this.children)
        child.flatten(target);
    }
    scanIdentical(other, dir) {
      if (!(other instanceof TextNode))
        return 0;
      let length = 0;
      let [iA, iB, eA, eB] = dir > 0 ? [0, 0, this.children.length, other.children.length] : [this.children.length - 1, other.children.length - 1, -1, -1];
      for (; ; iA += dir, iB += dir) {
        if (iA == eA || iB == eB)
          return length;
        let chA = this.children[iA], chB = other.children[iB];
        if (chA != chB)
          return length + chA.scanIdentical(chB, dir);
        length += chA.length + 1;
      }
    }
    static from(children, length = children.reduce((l, ch) => l + ch.length + 1, -1)) {
      let lines = 0;
      for (let ch of children)
        lines += ch.lines;
      if (lines < 32) {
        let flat = [];
        for (let ch of children)
          ch.flatten(flat);
        return new TextLeaf(flat, length);
      }
      let chunk = Math.max(32, lines >> 5), maxChunk = chunk << 1, minChunk = chunk >> 1;
      let chunked = [], currentLines = 0, currentLen = -1, currentChunk = [];
      function add2(child) {
        let last;
        if (child.lines > maxChunk && child instanceof TextNode) {
          for (let node of child.children)
            add2(node);
        } else if (child.lines > minChunk && (currentLines > minChunk || !currentLines)) {
          flush();
          chunked.push(child);
        } else if (child instanceof TextLeaf && currentLines && (last = currentChunk[currentChunk.length - 1]) instanceof TextLeaf && child.lines + last.lines <= 32) {
          currentLines += child.lines;
          currentLen += child.length + 1;
          currentChunk[currentChunk.length - 1] = new TextLeaf(last.text.concat(child.text), last.length + 1 + child.length);
        } else {
          if (currentLines + child.lines > chunk)
            flush();
          currentLines += child.lines;
          currentLen += child.length + 1;
          currentChunk.push(child);
        }
      }
      function flush() {
        if (currentLines == 0)
          return;
        chunked.push(currentChunk.length == 1 ? currentChunk[0] : TextNode.from(currentChunk, currentLen));
        currentLen = -1;
        currentLines = currentChunk.length = 0;
      }
      for (let child of children)
        add2(child);
      flush();
      return chunked.length == 1 ? chunked[0] : new TextNode(chunked, length);
    }
  };
  Text.empty = /* @__PURE__ */ new TextLeaf([""], 0);
  function textLength(text) {
    let length = -1;
    for (let line of text)
      length += line.length + 1;
    return length;
  }
  function appendText(text, target, from2 = 0, to = 1e9) {
    for (let pos = 0, i = 0, first = true; i < text.length && pos <= to; i++) {
      let line = text[i], end = pos + line.length;
      if (end >= from2) {
        if (end > to)
          line = line.slice(0, to - pos);
        if (pos < from2)
          line = line.slice(from2 - pos);
        if (first) {
          target[target.length - 1] += line;
          first = false;
        } else
          target.push(line);
      }
      pos = end + 1;
    }
    return target;
  }
  function sliceText(text, from2, to) {
    return appendText(text, [""], from2, to);
  }
  var RawTextCursor = class {
    constructor(text, dir = 1) {
      this.dir = dir;
      this.done = false;
      this.lineBreak = false;
      this.value = "";
      this.nodes = [text];
      this.offsets = [dir > 0 ? 1 : (text instanceof TextLeaf ? text.text.length : text.children.length) << 1];
    }
    nextInner(skip, dir) {
      this.done = this.lineBreak = false;
      for (; ; ) {
        let last = this.nodes.length - 1;
        let top2 = this.nodes[last], offsetValue = this.offsets[last], offset = offsetValue >> 1;
        let size = top2 instanceof TextLeaf ? top2.text.length : top2.children.length;
        if (offset == (dir > 0 ? size : 0)) {
          if (last == 0) {
            this.done = true;
            this.value = "";
            return this;
          }
          if (dir > 0)
            this.offsets[last - 1]++;
          this.nodes.pop();
          this.offsets.pop();
        } else if ((offsetValue & 1) == (dir > 0 ? 0 : 1)) {
          this.offsets[last] += dir;
          if (skip == 0) {
            this.lineBreak = true;
            this.value = "\n";
            return this;
          }
          skip--;
        } else if (top2 instanceof TextLeaf) {
          let next = top2.text[offset + (dir < 0 ? -1 : 0)];
          this.offsets[last] += dir;
          if (next.length > Math.max(0, skip)) {
            this.value = skip == 0 ? next : dir > 0 ? next.slice(skip) : next.slice(0, next.length - skip);
            return this;
          }
          skip -= next.length;
        } else {
          let next = top2.children[offset + (dir < 0 ? -1 : 0)];
          if (skip > next.length) {
            skip -= next.length;
            this.offsets[last] += dir;
          } else {
            if (dir < 0)
              this.offsets[last]--;
            this.nodes.push(next);
            this.offsets.push(dir > 0 ? 1 : (next instanceof TextLeaf ? next.text.length : next.children.length) << 1);
          }
        }
      }
    }
    next(skip = 0) {
      if (skip < 0) {
        this.nextInner(-skip, -this.dir);
        skip = this.value.length;
      }
      return this.nextInner(skip, this.dir);
    }
  };
  var PartialTextCursor = class {
    constructor(text, start, end) {
      this.value = "";
      this.done = false;
      this.cursor = new RawTextCursor(text, start > end ? -1 : 1);
      this.pos = start > end ? text.length : 0;
      this.from = Math.min(start, end);
      this.to = Math.max(start, end);
    }
    nextInner(skip, dir) {
      if (dir < 0 ? this.pos <= this.from : this.pos >= this.to) {
        this.value = "";
        this.done = true;
        return this;
      }
      skip += Math.max(0, dir < 0 ? this.pos - this.to : this.from - this.pos);
      let limit = dir < 0 ? this.pos - this.from : this.to - this.pos;
      if (skip > limit)
        skip = limit;
      limit -= skip;
      let { value } = this.cursor.next(skip);
      this.pos += (value.length + skip) * dir;
      this.value = value.length <= limit ? value : dir < 0 ? value.slice(value.length - limit) : value.slice(0, limit);
      this.done = !this.value;
      return this;
    }
    next(skip = 0) {
      if (skip < 0)
        skip = Math.max(skip, this.from - this.pos);
      else if (skip > 0)
        skip = Math.min(skip, this.to - this.pos);
      return this.nextInner(skip, this.cursor.dir);
    }
    get lineBreak() {
      return this.cursor.lineBreak && this.value != "";
    }
  };
  var LineCursor = class {
    constructor(inner) {
      this.inner = inner;
      this.afterBreak = true;
      this.value = "";
      this.done = false;
    }
    next(skip = 0) {
      let { done, lineBreak, value } = this.inner.next(skip);
      if (done) {
        this.done = true;
        this.value = "";
      } else if (lineBreak) {
        if (this.afterBreak) {
          this.value = "";
        } else {
          this.afterBreak = true;
          this.next();
        }
      } else {
        this.value = value;
        this.afterBreak = false;
      }
      return this;
    }
    get lineBreak() {
      return false;
    }
  };
  if (typeof Symbol != "undefined") {
    Text.prototype[Symbol.iterator] = function() {
      return this.iter();
    };
    RawTextCursor.prototype[Symbol.iterator] = PartialTextCursor.prototype[Symbol.iterator] = LineCursor.prototype[Symbol.iterator] = function() {
      return this;
    };
  }
  var Line = class {
    constructor(from2, to, number2, text) {
      this.from = from2;
      this.to = to;
      this.number = number2;
      this.text = text;
    }
    get length() {
      return this.to - this.from;
    }
  };

  // node_modules/@codemirror/state/dist/index.js
  var DefaultSplit = /\r\n?|\n/;
  var MapMode = /* @__PURE__ */ function(MapMode2) {
    MapMode2[MapMode2["Simple"] = 0] = "Simple";
    MapMode2[MapMode2["TrackDel"] = 1] = "TrackDel";
    MapMode2[MapMode2["TrackBefore"] = 2] = "TrackBefore";
    MapMode2[MapMode2["TrackAfter"] = 3] = "TrackAfter";
    return MapMode2;
  }(MapMode || (MapMode = {}));
  var ChangeDesc = class {
    constructor(sections) {
      this.sections = sections;
    }
    get length() {
      let result = 0;
      for (let i = 0; i < this.sections.length; i += 2)
        result += this.sections[i];
      return result;
    }
    get newLength() {
      let result = 0;
      for (let i = 0; i < this.sections.length; i += 2) {
        let ins = this.sections[i + 1];
        result += ins < 0 ? this.sections[i] : ins;
      }
      return result;
    }
    get empty() {
      return this.sections.length == 0 || this.sections.length == 2 && this.sections[1] < 0;
    }
    iterGaps(f) {
      for (let i = 0, posA = 0, posB = 0; i < this.sections.length; ) {
        let len = this.sections[i++], ins = this.sections[i++];
        if (ins < 0) {
          f(posA, posB, len);
          posB += len;
        } else {
          posB += ins;
        }
        posA += len;
      }
    }
    iterChangedRanges(f, individual = false) {
      iterChanges(this, f, individual);
    }
    get invertedDesc() {
      let sections = [];
      for (let i = 0; i < this.sections.length; ) {
        let len = this.sections[i++], ins = this.sections[i++];
        if (ins < 0)
          sections.push(len, ins);
        else
          sections.push(ins, len);
      }
      return new ChangeDesc(sections);
    }
    composeDesc(other) {
      return this.empty ? other : other.empty ? this : composeSets(this, other);
    }
    mapDesc(other, before = false) {
      return other.empty ? this : mapSet(this, other, before);
    }
    mapPos(pos, assoc = -1, mode = MapMode.Simple) {
      let posA = 0, posB = 0;
      for (let i = 0; i < this.sections.length; ) {
        let len = this.sections[i++], ins = this.sections[i++], endA = posA + len;
        if (ins < 0) {
          if (endA > pos)
            return posB + (pos - posA);
          posB += len;
        } else {
          if (mode != MapMode.Simple && endA >= pos && (mode == MapMode.TrackDel && posA < pos && endA > pos || mode == MapMode.TrackBefore && posA < pos || mode == MapMode.TrackAfter && endA > pos))
            return null;
          if (endA > pos || endA == pos && assoc < 0 && !len)
            return pos == posA || assoc < 0 ? posB : posB + ins;
          posB += ins;
        }
        posA = endA;
      }
      if (pos > posA)
        throw new RangeError(`Position ${pos} is out of range for changeset of length ${posA}`);
      return posB;
    }
    touchesRange(from2, to = from2) {
      for (let i = 0, pos = 0; i < this.sections.length && pos <= to; ) {
        let len = this.sections[i++], ins = this.sections[i++], end = pos + len;
        if (ins >= 0 && pos <= to && end >= from2)
          return pos < from2 && end > to ? "cover" : true;
        pos = end;
      }
      return false;
    }
    toString() {
      let result = "";
      for (let i = 0; i < this.sections.length; ) {
        let len = this.sections[i++], ins = this.sections[i++];
        result += (result ? " " : "") + len + (ins >= 0 ? ":" + ins : "");
      }
      return result;
    }
    toJSON() {
      return this.sections;
    }
    static fromJSON(json2) {
      if (!Array.isArray(json2) || json2.length % 2 || json2.some((a) => typeof a != "number"))
        throw new RangeError("Invalid JSON representation of ChangeDesc");
      return new ChangeDesc(json2);
    }
  };
  var ChangeSet = class extends ChangeDesc {
    constructor(sections, inserted) {
      super(sections);
      this.inserted = inserted;
    }
    apply(doc2) {
      if (this.length != doc2.length)
        throw new RangeError("Applying change set to a document with the wrong length");
      iterChanges(this, (fromA, toA, fromB, _toB, text) => doc2 = doc2.replace(fromB, fromB + (toA - fromA), text), false);
      return doc2;
    }
    mapDesc(other, before = false) {
      return mapSet(this, other, before, true);
    }
    invert(doc2) {
      let sections = this.sections.slice(), inserted = [];
      for (let i = 0, pos = 0; i < sections.length; i += 2) {
        let len = sections[i], ins = sections[i + 1];
        if (ins >= 0) {
          sections[i] = ins;
          sections[i + 1] = len;
          let index = i >> 1;
          while (inserted.length < index)
            inserted.push(Text.empty);
          inserted.push(len ? doc2.slice(pos, pos + len) : Text.empty);
        }
        pos += len;
      }
      return new ChangeSet(sections, inserted);
    }
    compose(other) {
      return this.empty ? other : other.empty ? this : composeSets(this, other, true);
    }
    map(other, before = false) {
      return other.empty ? this : mapSet(this, other, before, true);
    }
    iterChanges(f, individual = false) {
      iterChanges(this, f, individual);
    }
    get desc() {
      return new ChangeDesc(this.sections);
    }
    filter(ranges) {
      let resultSections = [], resultInserted = [], filteredSections = [];
      let iter = new SectionIter(this);
      done:
        for (let i = 0, pos = 0; ; ) {
          let next = i == ranges.length ? 1e9 : ranges[i++];
          while (pos < next || pos == next && iter.len == 0) {
            if (iter.done)
              break done;
            let len = Math.min(iter.len, next - pos);
            addSection(filteredSections, len, -1);
            let ins = iter.ins == -1 ? -1 : iter.off == 0 ? iter.ins : 0;
            addSection(resultSections, len, ins);
            if (ins > 0)
              addInsert(resultInserted, resultSections, iter.text);
            iter.forward(len);
            pos += len;
          }
          let end = ranges[i++];
          while (pos < end) {
            if (iter.done)
              break done;
            let len = Math.min(iter.len, end - pos);
            addSection(resultSections, len, -1);
            addSection(filteredSections, len, iter.ins == -1 ? -1 : iter.off == 0 ? iter.ins : 0);
            iter.forward(len);
            pos += len;
          }
        }
      return {
        changes: new ChangeSet(resultSections, resultInserted),
        filtered: new ChangeDesc(filteredSections)
      };
    }
    toJSON() {
      let parts = [];
      for (let i = 0; i < this.sections.length; i += 2) {
        let len = this.sections[i], ins = this.sections[i + 1];
        if (ins < 0)
          parts.push(len);
        else if (ins == 0)
          parts.push([len]);
        else
          parts.push([len].concat(this.inserted[i >> 1].toJSON()));
      }
      return parts;
    }
    static of(changes, length, lineSep) {
      let sections = [], inserted = [], pos = 0;
      let total = null;
      function flush(force = false) {
        if (!force && !sections.length)
          return;
        if (pos < length)
          addSection(sections, length - pos, -1);
        let set = new ChangeSet(sections, inserted);
        total = total ? total.compose(set.map(total)) : set;
        sections = [];
        inserted = [];
        pos = 0;
      }
      function process2(spec) {
        if (Array.isArray(spec)) {
          for (let sub of spec)
            process2(sub);
        } else if (spec instanceof ChangeSet) {
          if (spec.length != length)
            throw new RangeError(`Mismatched change set length (got ${spec.length}, expected ${length})`);
          flush();
          total = total ? total.compose(spec.map(total)) : spec;
        } else {
          let { from: from2, to = from2, insert: insert2 } = spec;
          if (from2 > to || from2 < 0 || to > length)
            throw new RangeError(`Invalid change range ${from2} to ${to} (in doc of length ${length})`);
          let insText = !insert2 ? Text.empty : typeof insert2 == "string" ? Text.of(insert2.split(lineSep || DefaultSplit)) : insert2;
          let insLen = insText.length;
          if (from2 == to && insLen == 0)
            return;
          if (from2 < pos)
            flush();
          if (from2 > pos)
            addSection(sections, from2 - pos, -1);
          addSection(sections, to - from2, insLen);
          addInsert(inserted, sections, insText);
          pos = to;
        }
      }
      process2(changes);
      flush(!total);
      return total;
    }
    static empty(length) {
      return new ChangeSet(length ? [length, -1] : [], []);
    }
    static fromJSON(json2) {
      if (!Array.isArray(json2))
        throw new RangeError("Invalid JSON representation of ChangeSet");
      let sections = [], inserted = [];
      for (let i = 0; i < json2.length; i++) {
        let part = json2[i];
        if (typeof part == "number") {
          sections.push(part, -1);
        } else if (!Array.isArray(part) || typeof part[0] != "number" || part.some((e, i2) => i2 && typeof e != "string")) {
          throw new RangeError("Invalid JSON representation of ChangeSet");
        } else if (part.length == 1) {
          sections.push(part[0], 0);
        } else {
          while (inserted.length < i)
            inserted.push(Text.empty);
          inserted[i] = Text.of(part.slice(1));
          sections.push(part[0], inserted[i].length);
        }
      }
      return new ChangeSet(sections, inserted);
    }
  };
  function addSection(sections, len, ins, forceJoin = false) {
    if (len == 0 && ins <= 0)
      return;
    let last = sections.length - 2;
    if (last >= 0 && ins <= 0 && ins == sections[last + 1])
      sections[last] += len;
    else if (len == 0 && sections[last] == 0)
      sections[last + 1] += ins;
    else if (forceJoin) {
      sections[last] += len;
      sections[last + 1] += ins;
    } else
      sections.push(len, ins);
  }
  function addInsert(values2, sections, value) {
    if (value.length == 0)
      return;
    let index = sections.length - 2 >> 1;
    if (index < values2.length) {
      values2[values2.length - 1] = values2[values2.length - 1].append(value);
    } else {
      while (values2.length < index)
        values2.push(Text.empty);
      values2.push(value);
    }
  }
  function iterChanges(desc, f, individual) {
    let inserted = desc.inserted;
    for (let posA = 0, posB = 0, i = 0; i < desc.sections.length; ) {
      let len = desc.sections[i++], ins = desc.sections[i++];
      if (ins < 0) {
        posA += len;
        posB += len;
      } else {
        let endA = posA, endB = posB, text = Text.empty;
        for (; ; ) {
          endA += len;
          endB += ins;
          if (ins && inserted)
            text = text.append(inserted[i - 2 >> 1]);
          if (individual || i == desc.sections.length || desc.sections[i + 1] < 0)
            break;
          len = desc.sections[i++];
          ins = desc.sections[i++];
        }
        f(posA, endA, posB, endB, text);
        posA = endA;
        posB = endB;
      }
    }
  }
  function mapSet(setA, setB, before, mkSet = false) {
    let sections = [], insert2 = mkSet ? [] : null;
    let a = new SectionIter(setA), b = new SectionIter(setB);
    for (let posA = 0, posB = 0; ; ) {
      if (a.ins == -1) {
        posA += a.len;
        a.next();
      } else if (b.ins == -1 && posB < posA) {
        let skip = Math.min(b.len, posA - posB);
        b.forward(skip);
        addSection(sections, skip, -1);
        posB += skip;
      } else if (b.ins >= 0 && (a.done || posB < posA || posB == posA && (b.len < a.len || b.len == a.len && !before))) {
        addSection(sections, b.ins, -1);
        while (posA > posB && !a.done && posA + a.len < posB + b.len) {
          posA += a.len;
          a.next();
        }
        posB += b.len;
        b.next();
      } else if (a.ins >= 0) {
        let len = 0, end = posA + a.len;
        for (; ; ) {
          if (b.ins >= 0 && posB > posA && posB + b.len < end) {
            len += b.ins;
            posB += b.len;
            b.next();
          } else if (b.ins == -1 && posB < end) {
            let skip = Math.min(b.len, end - posB);
            len += skip;
            b.forward(skip);
            posB += skip;
          } else {
            break;
          }
        }
        addSection(sections, len, a.ins);
        if (insert2)
          addInsert(insert2, sections, a.text);
        posA = end;
        a.next();
      } else if (a.done && b.done) {
        return insert2 ? new ChangeSet(sections, insert2) : new ChangeDesc(sections);
      } else {
        throw new Error("Mismatched change set lengths");
      }
    }
  }
  function composeSets(setA, setB, mkSet = false) {
    let sections = [];
    let insert2 = mkSet ? [] : null;
    let a = new SectionIter(setA), b = new SectionIter(setB);
    for (let open = false; ; ) {
      if (a.done && b.done) {
        return insert2 ? new ChangeSet(sections, insert2) : new ChangeDesc(sections);
      } else if (a.ins == 0) {
        addSection(sections, a.len, 0, open);
        a.next();
      } else if (b.len == 0 && !b.done) {
        addSection(sections, 0, b.ins, open);
        if (insert2)
          addInsert(insert2, sections, b.text);
        b.next();
      } else if (a.done || b.done) {
        throw new Error("Mismatched change set lengths");
      } else {
        let len = Math.min(a.len2, b.len), sectionLen = sections.length;
        if (a.ins == -1) {
          let insB = b.ins == -1 ? -1 : b.off ? 0 : b.ins;
          addSection(sections, len, insB, open);
          if (insert2 && insB)
            addInsert(insert2, sections, b.text);
        } else if (b.ins == -1) {
          addSection(sections, a.off ? 0 : a.len, len, open);
          if (insert2)
            addInsert(insert2, sections, a.textBit(len));
        } else {
          addSection(sections, a.off ? 0 : a.len, b.off ? 0 : b.ins, open);
          if (insert2 && !b.off)
            addInsert(insert2, sections, b.text);
        }
        open = (a.ins > len || b.ins >= 0 && b.len > len) && (open || sections.length > sectionLen);
        a.forward2(len);
        b.forward(len);
      }
    }
  }
  var SectionIter = class {
    constructor(set) {
      this.set = set;
      this.i = 0;
      this.next();
    }
    next() {
      let { sections } = this.set;
      if (this.i < sections.length) {
        this.len = sections[this.i++];
        this.ins = sections[this.i++];
      } else {
        this.len = 0;
        this.ins = -2;
      }
      this.off = 0;
    }
    get done() {
      return this.ins == -2;
    }
    get len2() {
      return this.ins < 0 ? this.len : this.ins;
    }
    get text() {
      let { inserted } = this.set, index = this.i - 2 >> 1;
      return index >= inserted.length ? Text.empty : inserted[index];
    }
    textBit(len) {
      let { inserted } = this.set, index = this.i - 2 >> 1;
      return index >= inserted.length && !len ? Text.empty : inserted[index].slice(this.off, len == null ? void 0 : this.off + len);
    }
    forward(len) {
      if (len == this.len)
        this.next();
      else {
        this.len -= len;
        this.off += len;
      }
    }
    forward2(len) {
      if (this.ins == -1)
        this.forward(len);
      else if (len == this.ins)
        this.next();
      else {
        this.ins -= len;
        this.off += len;
      }
    }
  };
  var SelectionRange = class {
    constructor(from2, to, flags) {
      this.from = from2;
      this.to = to;
      this.flags = flags;
    }
    get anchor() {
      return this.flags & 16 ? this.to : this.from;
    }
    get head() {
      return this.flags & 16 ? this.from : this.to;
    }
    get empty() {
      return this.from == this.to;
    }
    get assoc() {
      return this.flags & 4 ? -1 : this.flags & 8 ? 1 : 0;
    }
    get bidiLevel() {
      let level = this.flags & 3;
      return level == 3 ? null : level;
    }
    get goalColumn() {
      let value = this.flags >> 5;
      return value == 33554431 ? void 0 : value;
    }
    map(change, assoc = -1) {
      let from2, to;
      if (this.empty) {
        from2 = to = change.mapPos(this.from, assoc);
      } else {
        from2 = change.mapPos(this.from, 1);
        to = change.mapPos(this.to, -1);
      }
      return from2 == this.from && to == this.to ? this : new SelectionRange(from2, to, this.flags);
    }
    extend(from2, to = from2) {
      if (from2 <= this.anchor && to >= this.anchor)
        return EditorSelection.range(from2, to);
      let head = Math.abs(from2 - this.anchor) > Math.abs(to - this.anchor) ? from2 : to;
      return EditorSelection.range(this.anchor, head);
    }
    eq(other) {
      return this.anchor == other.anchor && this.head == other.head;
    }
    toJSON() {
      return { anchor: this.anchor, head: this.head };
    }
    static fromJSON(json2) {
      if (!json2 || typeof json2.anchor != "number" || typeof json2.head != "number")
        throw new RangeError("Invalid JSON representation for SelectionRange");
      return EditorSelection.range(json2.anchor, json2.head);
    }
  };
  var EditorSelection = class {
    constructor(ranges, mainIndex = 0) {
      this.ranges = ranges;
      this.mainIndex = mainIndex;
    }
    map(change, assoc = -1) {
      if (change.empty)
        return this;
      return EditorSelection.create(this.ranges.map((r) => r.map(change, assoc)), this.mainIndex);
    }
    eq(other) {
      if (this.ranges.length != other.ranges.length || this.mainIndex != other.mainIndex)
        return false;
      for (let i = 0; i < this.ranges.length; i++)
        if (!this.ranges[i].eq(other.ranges[i]))
          return false;
      return true;
    }
    get main() {
      return this.ranges[this.mainIndex];
    }
    asSingle() {
      return this.ranges.length == 1 ? this : new EditorSelection([this.main]);
    }
    addRange(range, main = true) {
      return EditorSelection.create([range].concat(this.ranges), main ? 0 : this.mainIndex + 1);
    }
    replaceRange(range, which = this.mainIndex) {
      let ranges = this.ranges.slice();
      ranges[which] = range;
      return EditorSelection.create(ranges, this.mainIndex);
    }
    toJSON() {
      return { ranges: this.ranges.map((r) => r.toJSON()), main: this.mainIndex };
    }
    static fromJSON(json2) {
      if (!json2 || !Array.isArray(json2.ranges) || typeof json2.main != "number" || json2.main >= json2.ranges.length)
        throw new RangeError("Invalid JSON representation for EditorSelection");
      return new EditorSelection(json2.ranges.map((r) => SelectionRange.fromJSON(r)), json2.main);
    }
    static single(anchor, head = anchor) {
      return new EditorSelection([EditorSelection.range(anchor, head)], 0);
    }
    static create(ranges, mainIndex = 0) {
      if (ranges.length == 0)
        throw new RangeError("A selection needs at least one range");
      for (let pos = 0, i = 0; i < ranges.length; i++) {
        let range = ranges[i];
        if (range.empty ? range.from <= pos : range.from < pos)
          return normalized(ranges.slice(), mainIndex);
        pos = range.to;
      }
      return new EditorSelection(ranges, mainIndex);
    }
    static cursor(pos, assoc = 0, bidiLevel, goalColumn) {
      return new SelectionRange(pos, pos, (assoc == 0 ? 0 : assoc < 0 ? 4 : 8) | (bidiLevel == null ? 3 : Math.min(2, bidiLevel)) | (goalColumn !== null && goalColumn !== void 0 ? goalColumn : 33554431) << 5);
    }
    static range(anchor, head, goalColumn) {
      let goal = (goalColumn !== null && goalColumn !== void 0 ? goalColumn : 33554431) << 5;
      return head < anchor ? new SelectionRange(head, anchor, 16 | goal | 8) : new SelectionRange(anchor, head, goal | (head > anchor ? 4 : 0));
    }
  };
  function normalized(ranges, mainIndex = 0) {
    let main = ranges[mainIndex];
    ranges.sort((a, b) => a.from - b.from);
    mainIndex = ranges.indexOf(main);
    for (let i = 1; i < ranges.length; i++) {
      let range = ranges[i], prev = ranges[i - 1];
      if (range.empty ? range.from <= prev.to : range.from < prev.to) {
        let from2 = prev.from, to = Math.max(range.to, prev.to);
        if (i <= mainIndex)
          mainIndex--;
        ranges.splice(--i, 2, range.anchor > range.head ? EditorSelection.range(to, from2) : EditorSelection.range(from2, to));
      }
    }
    return new EditorSelection(ranges, mainIndex);
  }
  function checkSelection(selection, docLength) {
    for (let range of selection.ranges)
      if (range.to > docLength)
        throw new RangeError("Selection points outside of document");
  }
  var nextID = 0;
  var Facet = class {
    constructor(combine, compareInput, compare2, isStatic, extensions) {
      this.combine = combine;
      this.compareInput = compareInput;
      this.compare = compare2;
      this.isStatic = isStatic;
      this.extensions = extensions;
      this.id = nextID++;
      this.default = combine([]);
    }
    static define(config2 = {}) {
      return new Facet(config2.combine || ((a) => a), config2.compareInput || ((a, b) => a === b), config2.compare || (!config2.combine ? sameArray : (a, b) => a === b), !!config2.static, config2.enables);
    }
    of(value) {
      return new FacetProvider([], this, 0, value);
    }
    compute(deps, get) {
      if (this.isStatic)
        throw new Error("Can't compute a static facet");
      return new FacetProvider(deps, this, 1, get);
    }
    computeN(deps, get) {
      if (this.isStatic)
        throw new Error("Can't compute a static facet");
      return new FacetProvider(deps, this, 2, get);
    }
    from(field, get) {
      if (!get)
        get = (x) => x;
      return this.compute([field], (state) => get(state.field(field)));
    }
  };
  function sameArray(a, b) {
    return a == b || a.length == b.length && a.every((e, i) => e === b[i]);
  }
  var FacetProvider = class {
    constructor(dependencies, facet, type, value) {
      this.dependencies = dependencies;
      this.facet = facet;
      this.type = type;
      this.value = value;
      this.id = nextID++;
    }
    dynamicSlot(addresses) {
      var _a2;
      let getter = this.value;
      let compare2 = this.facet.compareInput;
      let id2 = this.id, idx = addresses[id2] >> 1, multi = this.type == 2;
      let depDoc = false, depSel = false, depAddrs = [];
      for (let dep of this.dependencies) {
        if (dep == "doc")
          depDoc = true;
        else if (dep == "selection")
          depSel = true;
        else if ((((_a2 = addresses[dep.id]) !== null && _a2 !== void 0 ? _a2 : 1) & 1) == 0)
          depAddrs.push(addresses[dep.id]);
      }
      return {
        create(state) {
          state.values[idx] = getter(state);
          return 1;
        },
        update(state, tr) {
          if (depDoc && tr.docChanged || depSel && (tr.docChanged || tr.selection) || depAddrs.some((addr) => (ensureAddr(state, addr) & 1) > 0)) {
            let newVal = getter(state);
            if (multi ? !compareArray(newVal, state.values[idx], compare2) : !compare2(newVal, state.values[idx])) {
              state.values[idx] = newVal;
              return 1;
            }
          }
          return 0;
        },
        reconfigure(state, oldState) {
          let newVal = getter(state);
          let oldAddr = oldState.config.address[id2];
          if (oldAddr != null) {
            let oldVal = getAddr(oldState, oldAddr);
            if (multi ? compareArray(newVal, oldVal, compare2) : compare2(newVal, oldVal)) {
              state.values[idx] = oldVal;
              return 0;
            }
          }
          state.values[idx] = newVal;
          return 1;
        }
      };
    }
  };
  function compareArray(a, b, compare2) {
    if (a.length != b.length)
      return false;
    for (let i = 0; i < a.length; i++)
      if (!compare2(a[i], b[i]))
        return false;
    return true;
  }
  function dynamicFacetSlot(addresses, facet, providers) {
    let providerAddrs = providers.map((p) => addresses[p.id]);
    let providerTypes = providers.map((p) => p.type);
    let dynamic = providerAddrs.filter((p) => !(p & 1));
    let idx = addresses[facet.id] >> 1;
    function get(state) {
      let values2 = [];
      for (let i = 0; i < providerAddrs.length; i++) {
        let value = getAddr(state, providerAddrs[i]);
        if (providerTypes[i] == 2)
          for (let val of value)
            values2.push(val);
        else
          values2.push(value);
      }
      return facet.combine(values2);
    }
    return {
      create(state) {
        for (let addr of providerAddrs)
          ensureAddr(state, addr);
        state.values[idx] = get(state);
        return 1;
      },
      update(state, tr) {
        if (!dynamic.some((dynAddr) => ensureAddr(state, dynAddr) & 1))
          return 0;
        let value = get(state);
        if (facet.compare(value, state.values[idx]))
          return 0;
        state.values[idx] = value;
        return 1;
      },
      reconfigure(state, oldState) {
        let depChanged = providerAddrs.some((addr) => ensureAddr(state, addr) & 1);
        let oldProviders = oldState.config.facets[facet.id], oldValue = oldState.facet(facet);
        if (oldProviders && !depChanged && sameArray(providers, oldProviders)) {
          state.values[idx] = oldValue;
          return 0;
        }
        let value = get(state);
        if (facet.compare(value, oldValue)) {
          state.values[idx] = oldValue;
          return 0;
        }
        state.values[idx] = value;
        return 1;
      }
    };
  }
  var initField = /* @__PURE__ */ Facet.define({ static: true });
  var StateField = class {
    constructor(id2, createF, updateF, compareF, spec) {
      this.id = id2;
      this.createF = createF;
      this.updateF = updateF;
      this.compareF = compareF;
      this.spec = spec;
      this.provides = void 0;
    }
    static define(config2) {
      let field = new StateField(nextID++, config2.create, config2.update, config2.compare || ((a, b) => a === b), config2);
      if (config2.provide)
        field.provides = config2.provide(field);
      return field;
    }
    create(state) {
      let init = state.facet(initField).find((i) => i.field == this);
      return ((init === null || init === void 0 ? void 0 : init.create) || this.createF)(state);
    }
    slot(addresses) {
      let idx = addresses[this.id] >> 1;
      return {
        create: (state) => {
          state.values[idx] = this.create(state);
          return 1;
        },
        update: (state, tr) => {
          let oldVal = state.values[idx];
          let value = this.updateF(oldVal, tr);
          if (this.compareF(oldVal, value))
            return 0;
          state.values[idx] = value;
          return 1;
        },
        reconfigure: (state, oldState) => {
          if (oldState.config.address[this.id] != null) {
            state.values[idx] = oldState.field(this);
            return 0;
          }
          state.values[idx] = this.create(state);
          return 1;
        }
      };
    }
    init(create) {
      return [this, initField.of({ field: this, create })];
    }
    get extension() {
      return this;
    }
  };
  var Prec_ = { lowest: 4, low: 3, default: 2, high: 1, highest: 0 };
  function prec(value) {
    return (ext) => new PrecExtension(ext, value);
  }
  var Prec = {
    lowest: /* @__PURE__ */ prec(Prec_.lowest),
    low: /* @__PURE__ */ prec(Prec_.low),
    default: /* @__PURE__ */ prec(Prec_.default),
    high: /* @__PURE__ */ prec(Prec_.high),
    highest: /* @__PURE__ */ prec(Prec_.highest),
    fallback: /* @__PURE__ */ prec(Prec_.lowest),
    extend: /* @__PURE__ */ prec(Prec_.high),
    override: /* @__PURE__ */ prec(Prec_.highest)
  };
  var PrecExtension = class {
    constructor(inner, prec2) {
      this.inner = inner;
      this.prec = prec2;
    }
  };
  var Compartment = class {
    of(ext) {
      return new CompartmentInstance(this, ext);
    }
    reconfigure(content2) {
      return Compartment.reconfigure.of({ compartment: this, extension: content2 });
    }
    get(state) {
      return state.config.compartments.get(this);
    }
  };
  var CompartmentInstance = class {
    constructor(compartment, inner) {
      this.compartment = compartment;
      this.inner = inner;
    }
  };
  var Configuration = class {
    constructor(base2, compartments, dynamicSlots, address, staticValues, facets) {
      this.base = base2;
      this.compartments = compartments;
      this.dynamicSlots = dynamicSlots;
      this.address = address;
      this.staticValues = staticValues;
      this.facets = facets;
      this.statusTemplate = [];
      while (this.statusTemplate.length < dynamicSlots.length)
        this.statusTemplate.push(0);
    }
    staticFacet(facet) {
      let addr = this.address[facet.id];
      return addr == null ? facet.default : this.staticValues[addr >> 1];
    }
    static resolve(base2, compartments, oldState) {
      let fields = [];
      let facets = /* @__PURE__ */ Object.create(null);
      let newCompartments = /* @__PURE__ */ new Map();
      for (let ext of flatten(base2, compartments, newCompartments)) {
        if (ext instanceof StateField)
          fields.push(ext);
        else
          (facets[ext.facet.id] || (facets[ext.facet.id] = [])).push(ext);
      }
      let address = /* @__PURE__ */ Object.create(null);
      let staticValues = [];
      let dynamicSlots = [];
      for (let field of fields) {
        address[field.id] = dynamicSlots.length << 1;
        dynamicSlots.push((a) => field.slot(a));
      }
      let oldFacets = oldState === null || oldState === void 0 ? void 0 : oldState.config.facets;
      for (let id2 in facets) {
        let providers = facets[id2], facet = providers[0].facet;
        let oldProviders = oldFacets && oldFacets[id2] || [];
        if (providers.every((p) => p.type == 0)) {
          address[facet.id] = staticValues.length << 1 | 1;
          if (sameArray(oldProviders, providers)) {
            staticValues.push(oldState.facet(facet));
          } else {
            let value = facet.combine(providers.map((p) => p.value));
            staticValues.push(oldState && facet.compare(value, oldState.facet(facet)) ? oldState.facet(facet) : value);
          }
        } else {
          for (let p of providers) {
            if (p.type == 0) {
              address[p.id] = staticValues.length << 1 | 1;
              staticValues.push(p.value);
            } else {
              address[p.id] = dynamicSlots.length << 1;
              dynamicSlots.push((a) => p.dynamicSlot(a));
            }
          }
          address[facet.id] = dynamicSlots.length << 1;
          dynamicSlots.push((a) => dynamicFacetSlot(a, facet, providers));
        }
      }
      let dynamic = dynamicSlots.map((f) => f(address));
      return new Configuration(base2, newCompartments, dynamic, address, staticValues, facets);
    }
  };
  function flatten(extension, compartments, newCompartments) {
    let result = [[], [], [], [], []];
    let seen = /* @__PURE__ */ new Map();
    function inner(ext, prec2) {
      let known = seen.get(ext);
      if (known != null) {
        if (known >= prec2)
          return;
        let found = result[known].indexOf(ext);
        if (found > -1)
          result[known].splice(found, 1);
        if (ext instanceof CompartmentInstance)
          newCompartments.delete(ext.compartment);
      }
      seen.set(ext, prec2);
      if (Array.isArray(ext)) {
        for (let e of ext)
          inner(e, prec2);
      } else if (ext instanceof CompartmentInstance) {
        if (newCompartments.has(ext.compartment))
          throw new RangeError(`Duplicate use of compartment in extensions`);
        let content2 = compartments.get(ext.compartment) || ext.inner;
        newCompartments.set(ext.compartment, content2);
        inner(content2, prec2);
      } else if (ext instanceof PrecExtension) {
        inner(ext.inner, ext.prec);
      } else if (ext instanceof StateField) {
        result[prec2].push(ext);
        if (ext.provides)
          inner(ext.provides, prec2);
      } else if (ext instanceof FacetProvider) {
        result[prec2].push(ext);
        if (ext.facet.extensions)
          inner(ext.facet.extensions, prec2);
      } else {
        let content2 = ext.extension;
        if (!content2)
          throw new Error(`Unrecognized extension value in extension set (${ext}). This sometimes happens because multiple instances of @codemirror/state are loaded, breaking instanceof checks.`);
        inner(content2, prec2);
      }
    }
    inner(extension, Prec_.default);
    return result.reduce((a, b) => a.concat(b));
  }
  function ensureAddr(state, addr) {
    if (addr & 1)
      return 2;
    let idx = addr >> 1;
    let status = state.status[idx];
    if (status == 4)
      throw new Error("Cyclic dependency between fields and/or facets");
    if (status & 2)
      return status;
    state.status[idx] = 4;
    let changed = state.computeSlot(state, state.config.dynamicSlots[idx]);
    return state.status[idx] = 2 | changed;
  }
  function getAddr(state, addr) {
    return addr & 1 ? state.config.staticValues[addr >> 1] : state.values[addr >> 1];
  }
  var languageData = /* @__PURE__ */ Facet.define();
  var allowMultipleSelections = /* @__PURE__ */ Facet.define({
    combine: (values2) => values2.some((v) => v),
    static: true
  });
  var lineSeparator = /* @__PURE__ */ Facet.define({
    combine: (values2) => values2.length ? values2[0] : void 0,
    static: true
  });
  var changeFilter = /* @__PURE__ */ Facet.define();
  var transactionFilter = /* @__PURE__ */ Facet.define();
  var transactionExtender = /* @__PURE__ */ Facet.define();
  var readOnly = /* @__PURE__ */ Facet.define({
    combine: (values2) => values2.length ? values2[0] : false
  });
  var Annotation = class {
    constructor(type, value) {
      this.type = type;
      this.value = value;
    }
    static define() {
      return new AnnotationType();
    }
  };
  var AnnotationType = class {
    of(value) {
      return new Annotation(this, value);
    }
  };
  var StateEffectType = class {
    constructor(map) {
      this.map = map;
    }
    of(value) {
      return new StateEffect(this, value);
    }
  };
  var StateEffect = class {
    constructor(type, value) {
      this.type = type;
      this.value = value;
    }
    map(mapping) {
      let mapped = this.type.map(this.value, mapping);
      return mapped === void 0 ? void 0 : mapped == this.value ? this : new StateEffect(this.type, mapped);
    }
    is(type) {
      return this.type == type;
    }
    static define(spec = {}) {
      return new StateEffectType(spec.map || ((v) => v));
    }
    static mapEffects(effects, mapping) {
      if (!effects.length)
        return effects;
      let result = [];
      for (let effect of effects) {
        let mapped = effect.map(mapping);
        if (mapped)
          result.push(mapped);
      }
      return result;
    }
  };
  StateEffect.reconfigure = /* @__PURE__ */ StateEffect.define();
  StateEffect.appendConfig = /* @__PURE__ */ StateEffect.define();
  var Transaction = class {
    constructor(startState, changes, selection, effects, annotations, scrollIntoView3) {
      this.startState = startState;
      this.changes = changes;
      this.selection = selection;
      this.effects = effects;
      this.annotations = annotations;
      this.scrollIntoView = scrollIntoView3;
      this._doc = null;
      this._state = null;
      if (selection)
        checkSelection(selection, changes.newLength);
      if (!annotations.some((a) => a.type == Transaction.time))
        this.annotations = annotations.concat(Transaction.time.of(Date.now()));
    }
    get newDoc() {
      return this._doc || (this._doc = this.changes.apply(this.startState.doc));
    }
    get newSelection() {
      return this.selection || this.startState.selection.map(this.changes);
    }
    get state() {
      if (!this._state)
        this.startState.applyTransaction(this);
      return this._state;
    }
    annotation(type) {
      for (let ann of this.annotations)
        if (ann.type == type)
          return ann.value;
      return void 0;
    }
    get docChanged() {
      return !this.changes.empty;
    }
    get reconfigured() {
      return this.startState.config != this.state.config;
    }
    isUserEvent(event) {
      let e = this.annotation(Transaction.userEvent);
      return !!(e && (e == event || e.length > event.length && e.slice(0, event.length) == event && e[event.length] == "."));
    }
  };
  Transaction.time = /* @__PURE__ */ Annotation.define();
  Transaction.userEvent = /* @__PURE__ */ Annotation.define();
  Transaction.addToHistory = /* @__PURE__ */ Annotation.define();
  Transaction.remote = /* @__PURE__ */ Annotation.define();
  function joinRanges(a, b) {
    let result = [];
    for (let iA = 0, iB = 0; ; ) {
      let from2, to;
      if (iA < a.length && (iB == b.length || b[iB] >= a[iA])) {
        from2 = a[iA++];
        to = a[iA++];
      } else if (iB < b.length) {
        from2 = b[iB++];
        to = b[iB++];
      } else
        return result;
      if (!result.length || result[result.length - 1] < from2)
        result.push(from2, to);
      else if (result[result.length - 1] < to)
        result[result.length - 1] = to;
    }
  }
  function mergeTransaction(a, b, sequential) {
    var _a2;
    let mapForA, mapForB, changes;
    if (sequential) {
      mapForA = b.changes;
      mapForB = ChangeSet.empty(b.changes.length);
      changes = a.changes.compose(b.changes);
    } else {
      mapForA = b.changes.map(a.changes);
      mapForB = a.changes.mapDesc(b.changes, true);
      changes = a.changes.compose(mapForA);
    }
    return {
      changes,
      selection: b.selection ? b.selection.map(mapForB) : (_a2 = a.selection) === null || _a2 === void 0 ? void 0 : _a2.map(mapForA),
      effects: StateEffect.mapEffects(a.effects, mapForA).concat(StateEffect.mapEffects(b.effects, mapForB)),
      annotations: a.annotations.length ? a.annotations.concat(b.annotations) : b.annotations,
      scrollIntoView: a.scrollIntoView || b.scrollIntoView
    };
  }
  function resolveTransactionInner(state, spec, docSize) {
    let sel = spec.selection, annotations = asArray(spec.annotations);
    if (spec.userEvent)
      annotations = annotations.concat(Transaction.userEvent.of(spec.userEvent));
    return {
      changes: spec.changes instanceof ChangeSet ? spec.changes : ChangeSet.of(spec.changes || [], docSize, state.facet(lineSeparator)),
      selection: sel && (sel instanceof EditorSelection ? sel : EditorSelection.single(sel.anchor, sel.head)),
      effects: asArray(spec.effects),
      annotations,
      scrollIntoView: !!spec.scrollIntoView
    };
  }
  function resolveTransaction(state, specs, filter) {
    let s = resolveTransactionInner(state, specs.length ? specs[0] : {}, state.doc.length);
    if (specs.length && specs[0].filter === false)
      filter = false;
    for (let i = 1; i < specs.length; i++) {
      if (specs[i].filter === false)
        filter = false;
      let seq = !!specs[i].sequential;
      s = mergeTransaction(s, resolveTransactionInner(state, specs[i], seq ? s.changes.newLength : state.doc.length), seq);
    }
    let tr = new Transaction(state, s.changes, s.selection, s.effects, s.annotations, s.scrollIntoView);
    return extendTransaction(filter ? filterTransaction(tr) : tr);
  }
  function filterTransaction(tr) {
    let state = tr.startState;
    let result = true;
    for (let filter of state.facet(changeFilter)) {
      let value = filter(tr);
      if (value === false) {
        result = false;
        break;
      }
      if (Array.isArray(value))
        result = result === true ? value : joinRanges(result, value);
    }
    if (result !== true) {
      let changes, back;
      if (result === false) {
        back = tr.changes.invertedDesc;
        changes = ChangeSet.empty(state.doc.length);
      } else {
        let filtered = tr.changes.filter(result);
        changes = filtered.changes;
        back = filtered.filtered.invertedDesc;
      }
      tr = new Transaction(state, changes, tr.selection && tr.selection.map(back), StateEffect.mapEffects(tr.effects, back), tr.annotations, tr.scrollIntoView);
    }
    let filters = state.facet(transactionFilter);
    for (let i = filters.length - 1; i >= 0; i--) {
      let filtered = filters[i](tr);
      if (filtered instanceof Transaction)
        tr = filtered;
      else if (Array.isArray(filtered) && filtered.length == 1 && filtered[0] instanceof Transaction)
        tr = filtered[0];
      else
        tr = resolveTransaction(state, asArray(filtered), false);
    }
    return tr;
  }
  function extendTransaction(tr) {
    let state = tr.startState, extenders = state.facet(transactionExtender), spec = tr;
    for (let i = extenders.length - 1; i >= 0; i--) {
      let extension = extenders[i](tr);
      if (extension && Object.keys(extension).length)
        spec = mergeTransaction(tr, resolveTransactionInner(state, extension, tr.changes.newLength), true);
    }
    return spec == tr ? tr : new Transaction(state, tr.changes, tr.selection, spec.effects, spec.annotations, spec.scrollIntoView);
  }
  var none = [];
  function asArray(value) {
    return value == null ? none : Array.isArray(value) ? value : [value];
  }
  var CharCategory = /* @__PURE__ */ function(CharCategory2) {
    CharCategory2[CharCategory2["Word"] = 0] = "Word";
    CharCategory2[CharCategory2["Space"] = 1] = "Space";
    CharCategory2[CharCategory2["Other"] = 2] = "Other";
    return CharCategory2;
  }(CharCategory || (CharCategory = {}));
  var nonASCIISingleCaseWordChar = /[\u00df\u0587\u0590-\u05f4\u0600-\u06ff\u3040-\u309f\u30a0-\u30ff\u3400-\u4db5\u4e00-\u9fcc\uac00-\ud7af]/;
  var wordChar;
  try {
    wordChar = /* @__PURE__ */ new RegExp("[\\p{Alphabetic}\\p{Number}_]", "u");
  } catch (_) {
  }
  function hasWordChar(str) {
    if (wordChar)
      return wordChar.test(str);
    for (let i = 0; i < str.length; i++) {
      let ch = str[i];
      if (/\w/.test(ch) || ch > "\x80" && (ch.toUpperCase() != ch.toLowerCase() || nonASCIISingleCaseWordChar.test(ch)))
        return true;
    }
    return false;
  }
  function makeCategorizer(wordChars) {
    return (char) => {
      if (!/\S/.test(char))
        return CharCategory.Space;
      if (hasWordChar(char))
        return CharCategory.Word;
      for (let i = 0; i < wordChars.length; i++)
        if (char.indexOf(wordChars[i]) > -1)
          return CharCategory.Word;
      return CharCategory.Other;
    };
  }
  var EditorState = class {
    constructor(config2, doc2, selection, values2, computeSlot, tr) {
      this.config = config2;
      this.doc = doc2;
      this.selection = selection;
      this.values = values2;
      this.status = config2.statusTemplate.slice();
      this.computeSlot = computeSlot;
      if (tr)
        tr._state = this;
      for (let i = 0; i < this.config.dynamicSlots.length; i++)
        ensureAddr(this, i << 1);
      this.computeSlot = null;
    }
    field(field, require3 = true) {
      let addr = this.config.address[field.id];
      if (addr == null) {
        if (require3)
          throw new RangeError("Field is not present in this state");
        return void 0;
      }
      ensureAddr(this, addr);
      return getAddr(this, addr);
    }
    update(...specs) {
      return resolveTransaction(this, specs, true);
    }
    applyTransaction(tr) {
      let conf = this.config, { base: base2, compartments } = conf;
      for (let effect of tr.effects) {
        if (effect.is(Compartment.reconfigure)) {
          if (conf) {
            compartments = /* @__PURE__ */ new Map();
            conf.compartments.forEach((val, key) => compartments.set(key, val));
            conf = null;
          }
          compartments.set(effect.value.compartment, effect.value.extension);
        } else if (effect.is(StateEffect.reconfigure)) {
          conf = null;
          base2 = effect.value;
        } else if (effect.is(StateEffect.appendConfig)) {
          conf = null;
          base2 = asArray(base2).concat(effect.value);
        }
      }
      let startValues;
      if (!conf) {
        conf = Configuration.resolve(base2, compartments, this);
        let intermediateState = new EditorState(conf, this.doc, this.selection, conf.dynamicSlots.map(() => null), (state, slot) => slot.reconfigure(state, this), null);
        startValues = intermediateState.values;
      } else {
        startValues = tr.startState.values.slice();
      }
      new EditorState(conf, tr.newDoc, tr.newSelection, startValues, (state, slot) => slot.update(state, tr), tr);
    }
    replaceSelection(text) {
      if (typeof text == "string")
        text = this.toText(text);
      return this.changeByRange((range) => ({
        changes: { from: range.from, to: range.to, insert: text },
        range: EditorSelection.cursor(range.from + text.length)
      }));
    }
    changeByRange(f) {
      let sel = this.selection;
      let result1 = f(sel.ranges[0]);
      let changes = this.changes(result1.changes), ranges = [result1.range];
      let effects = asArray(result1.effects);
      for (let i = 1; i < sel.ranges.length; i++) {
        let result = f(sel.ranges[i]);
        let newChanges = this.changes(result.changes), newMapped = newChanges.map(changes);
        for (let j = 0; j < i; j++)
          ranges[j] = ranges[j].map(newMapped);
        let mapBy = changes.mapDesc(newChanges, true);
        ranges.push(result.range.map(mapBy));
        changes = changes.compose(newMapped);
        effects = StateEffect.mapEffects(effects, newMapped).concat(StateEffect.mapEffects(asArray(result.effects), mapBy));
      }
      return {
        changes,
        selection: EditorSelection.create(ranges, sel.mainIndex),
        effects
      };
    }
    changes(spec = []) {
      if (spec instanceof ChangeSet)
        return spec;
      return ChangeSet.of(spec, this.doc.length, this.facet(EditorState.lineSeparator));
    }
    toText(string2) {
      return Text.of(string2.split(this.facet(EditorState.lineSeparator) || DefaultSplit));
    }
    sliceDoc(from2 = 0, to = this.doc.length) {
      return this.doc.sliceString(from2, to, this.lineBreak);
    }
    facet(facet) {
      let addr = this.config.address[facet.id];
      if (addr == null)
        return facet.default;
      ensureAddr(this, addr);
      return getAddr(this, addr);
    }
    toJSON(fields) {
      let result = {
        doc: this.sliceDoc(),
        selection: this.selection.toJSON()
      };
      if (fields)
        for (let prop in fields) {
          let value = fields[prop];
          if (value instanceof StateField)
            result[prop] = value.spec.toJSON(this.field(fields[prop]), this);
        }
      return result;
    }
    static fromJSON(json2, config2 = {}, fields) {
      if (!json2 || typeof json2.doc != "string")
        throw new RangeError("Invalid JSON representation for EditorState");
      let fieldInit = [];
      if (fields)
        for (let prop in fields) {
          let field = fields[prop], value = json2[prop];
          fieldInit.push(field.init((state) => field.spec.fromJSON(value, state)));
        }
      return EditorState.create({
        doc: json2.doc,
        selection: EditorSelection.fromJSON(json2.selection),
        extensions: config2.extensions ? fieldInit.concat([config2.extensions]) : fieldInit
      });
    }
    static create(config2 = {}) {
      let configuration = Configuration.resolve(config2.extensions || [], /* @__PURE__ */ new Map());
      let doc2 = config2.doc instanceof Text ? config2.doc : Text.of((config2.doc || "").split(configuration.staticFacet(EditorState.lineSeparator) || DefaultSplit));
      let selection = !config2.selection ? EditorSelection.single(0) : config2.selection instanceof EditorSelection ? config2.selection : EditorSelection.single(config2.selection.anchor, config2.selection.head);
      checkSelection(selection, doc2.length);
      if (!configuration.staticFacet(allowMultipleSelections))
        selection = selection.asSingle();
      return new EditorState(configuration, doc2, selection, configuration.dynamicSlots.map(() => null), (state, slot) => slot.create(state), null);
    }
    get tabSize() {
      return this.facet(EditorState.tabSize);
    }
    get lineBreak() {
      return this.facet(EditorState.lineSeparator) || "\n";
    }
    get readOnly() {
      return this.facet(readOnly);
    }
    phrase(phrase2) {
      for (let map of this.facet(EditorState.phrases))
        if (Object.prototype.hasOwnProperty.call(map, phrase2))
          return map[phrase2];
      return phrase2;
    }
    languageDataAt(name2, pos, side = -1) {
      let values2 = [];
      for (let provider of this.facet(languageData)) {
        for (let result of provider(this, pos, side)) {
          if (Object.prototype.hasOwnProperty.call(result, name2))
            values2.push(result[name2]);
        }
      }
      return values2;
    }
    charCategorizer(at) {
      return makeCategorizer(this.languageDataAt("wordChars", at).join(""));
    }
    wordAt(pos) {
      let { text, from: from2, length } = this.doc.lineAt(pos);
      let cat = this.charCategorizer(pos);
      let start = pos - from2, end = pos - from2;
      while (start > 0) {
        let prev = findClusterBreak(text, start, false);
        if (cat(text.slice(prev, start)) != CharCategory.Word)
          break;
        start = prev;
      }
      while (end < length) {
        let next = findClusterBreak(text, end);
        if (cat(text.slice(end, next)) != CharCategory.Word)
          break;
        end = next;
      }
      return start == end ? null : EditorSelection.range(start + from2, end + from2);
    }
  };
  EditorState.allowMultipleSelections = allowMultipleSelections;
  EditorState.tabSize = /* @__PURE__ */ Facet.define({
    combine: (values2) => values2.length ? values2[0] : 4
  });
  EditorState.lineSeparator = lineSeparator;
  EditorState.readOnly = readOnly;
  EditorState.phrases = /* @__PURE__ */ Facet.define();
  EditorState.languageData = languageData;
  EditorState.changeFilter = changeFilter;
  EditorState.transactionFilter = transactionFilter;
  EditorState.transactionExtender = transactionExtender;
  Compartment.reconfigure = /* @__PURE__ */ StateEffect.define();
  function combineConfig(configs, defaults3, combine = {}) {
    let result = {};
    for (let config2 of configs)
      for (let key of Object.keys(config2)) {
        let value = config2[key], current = result[key];
        if (current === void 0)
          result[key] = value;
        else if (current === value || value === void 0)
          ;
        else if (Object.hasOwnProperty.call(combine, key))
          result[key] = combine[key](current, value);
        else
          throw new Error("Config merge conflict for field " + key);
      }
    for (let key in defaults3)
      if (result[key] === void 0)
        result[key] = defaults3[key];
    return result;
  }

  // node_modules/style-mod/src/style-mod.js
  var C = "\u037C";
  var COUNT = typeof Symbol == "undefined" ? "__" + C : Symbol.for(C);
  var SET = typeof Symbol == "undefined" ? "__styleSet" + Math.floor(Math.random() * 1e8) : Symbol("styleSet");
  var top = typeof globalThis != "undefined" ? globalThis : typeof window != "undefined" ? window : {};
  var StyleModule = class {
    constructor(spec, options) {
      this.rules = [];
      let { finish } = options || {};
      function splitSelector(selector) {
        return /^@/.test(selector) ? [selector] : selector.split(/,\s*/);
      }
      function render(selectors, spec2, target, isKeyframes) {
        let local = [], isAt = /^@(\w+)\b/.exec(selectors[0]), keyframes = isAt && isAt[1] == "keyframes";
        if (isAt && spec2 == null)
          return target.push(selectors[0] + ";");
        for (let prop in spec2) {
          let value = spec2[prop];
          if (/&/.test(prop)) {
            render(prop.split(/,\s*/).map((part) => selectors.map((sel) => part.replace(/&/, sel))).reduce((a, b) => a.concat(b)), value, target);
          } else if (value && typeof value == "object") {
            if (!isAt)
              throw new RangeError("The value of a property (" + prop + ") should be a primitive value.");
            render(splitSelector(prop), value, local, keyframes);
          } else if (value != null) {
            local.push(prop.replace(/_.*/, "").replace(/[A-Z]/g, (l) => "-" + l.toLowerCase()) + ": " + value + ";");
          }
        }
        if (local.length || keyframes) {
          target.push((finish && !isAt && !isKeyframes ? selectors.map(finish) : selectors).join(", ") + " {" + local.join(" ") + "}");
        }
      }
      for (let prop in spec)
        render(splitSelector(prop), spec[prop], this.rules);
    }
    getRules() {
      return this.rules.join("\n");
    }
    static newName() {
      let id2 = top[COUNT] || 1;
      top[COUNT] = id2 + 1;
      return C + id2.toString(36);
    }
    static mount(root, modules) {
      (root[SET] || new StyleSet(root)).mount(Array.isArray(modules) ? modules : [modules]);
    }
  };
  var adoptedSet = null;
  var StyleSet = class {
    constructor(root) {
      if (!root.head && root.adoptedStyleSheets && typeof CSSStyleSheet != "undefined") {
        if (adoptedSet) {
          root.adoptedStyleSheets = [adoptedSet.sheet].concat(root.adoptedStyleSheets);
          return root[SET] = adoptedSet;
        }
        this.sheet = new CSSStyleSheet();
        root.adoptedStyleSheets = [this.sheet].concat(root.adoptedStyleSheets);
        adoptedSet = this;
      } else {
        this.styleTag = (root.ownerDocument || root).createElement("style");
        let target = root.head || root;
        target.insertBefore(this.styleTag, target.firstChild);
      }
      this.modules = [];
      root[SET] = this;
    }
    mount(modules) {
      let sheet = this.sheet;
      let pos = 0, j = 0;
      for (let i = 0; i < modules.length; i++) {
        let mod = modules[i], index = this.modules.indexOf(mod);
        if (index < j && index > -1) {
          this.modules.splice(index, 1);
          j--;
          index = -1;
        }
        if (index == -1) {
          this.modules.splice(j++, 0, mod);
          if (sheet)
            for (let k = 0; k < mod.rules.length; k++)
              sheet.insertRule(mod.rules[k], pos++);
        } else {
          while (j < index)
            pos += this.modules[j++].rules.length;
          pos += mod.rules.length;
          j++;
        }
      }
      if (!sheet) {
        let text = "";
        for (let i = 0; i < this.modules.length; i++)
          text += this.modules[i].getRules() + "\n";
        this.styleTag.textContent = text;
      }
    }
  };

  // node_modules/@codemirror/rangeset/dist/index.js
  var RangeValue = class {
    eq(other) {
      return this == other;
    }
    range(from2, to = from2) {
      return new Range(from2, to, this);
    }
  };
  RangeValue.prototype.startSide = RangeValue.prototype.endSide = 0;
  RangeValue.prototype.point = false;
  RangeValue.prototype.mapMode = MapMode.TrackDel;
  var Range = class {
    constructor(from2, to, value) {
      this.from = from2;
      this.to = to;
      this.value = value;
    }
  };
  function cmpRange(a, b) {
    return a.from - b.from || a.value.startSide - b.value.startSide;
  }
  var Chunk = class {
    constructor(from2, to, value, maxPoint) {
      this.from = from2;
      this.to = to;
      this.value = value;
      this.maxPoint = maxPoint;
    }
    get length() {
      return this.to[this.to.length - 1];
    }
    findIndex(pos, side, end, startAt = 0) {
      let arr = end ? this.to : this.from;
      for (let lo = startAt, hi = arr.length; ; ) {
        if (lo == hi)
          return lo;
        let mid = lo + hi >> 1;
        let diff = arr[mid] - pos || (end ? this.value[mid].endSide : this.value[mid].startSide) - side;
        if (mid == lo)
          return diff >= 0 ? lo : hi;
        if (diff >= 0)
          hi = mid;
        else
          lo = mid + 1;
      }
    }
    between(offset, from2, to, f) {
      for (let i = this.findIndex(from2, -1e9, true), e = this.findIndex(to, 1e9, false, i); i < e; i++)
        if (f(this.from[i] + offset, this.to[i] + offset, this.value[i]) === false)
          return false;
    }
    map(offset, changes) {
      let value = [], from2 = [], to = [], newPos = -1, maxPoint = -1;
      for (let i = 0; i < this.value.length; i++) {
        let val = this.value[i], curFrom = this.from[i] + offset, curTo = this.to[i] + offset, newFrom, newTo;
        if (curFrom == curTo) {
          let mapped = changes.mapPos(curFrom, val.startSide, val.mapMode);
          if (mapped == null)
            continue;
          newFrom = newTo = mapped;
          if (val.startSide != val.endSide) {
            newTo = changes.mapPos(curFrom, val.endSide);
            if (newTo < newFrom)
              continue;
          }
        } else {
          newFrom = changes.mapPos(curFrom, val.startSide);
          newTo = changes.mapPos(curTo, val.endSide);
          if (newFrom > newTo || newFrom == newTo && val.startSide > 0 && val.endSide <= 0)
            continue;
        }
        if ((newTo - newFrom || val.endSide - val.startSide) < 0)
          continue;
        if (newPos < 0)
          newPos = newFrom;
        if (val.point)
          maxPoint = Math.max(maxPoint, newTo - newFrom);
        value.push(val);
        from2.push(newFrom - newPos);
        to.push(newTo - newPos);
      }
      return { mapped: value.length ? new Chunk(from2, to, value, maxPoint) : null, pos: newPos };
    }
  };
  var RangeSet = class {
    constructor(chunkPos, chunk, nextLayer = RangeSet.empty, maxPoint) {
      this.chunkPos = chunkPos;
      this.chunk = chunk;
      this.nextLayer = nextLayer;
      this.maxPoint = maxPoint;
    }
    get length() {
      let last = this.chunk.length - 1;
      return last < 0 ? 0 : Math.max(this.chunkEnd(last), this.nextLayer.length);
    }
    get size() {
      if (this.isEmpty)
        return 0;
      let size = this.nextLayer.size;
      for (let chunk of this.chunk)
        size += chunk.value.length;
      return size;
    }
    chunkEnd(index) {
      return this.chunkPos[index] + this.chunk[index].length;
    }
    update(updateSpec) {
      let { add: add2 = [], sort = false, filterFrom = 0, filterTo = this.length } = updateSpec;
      let filter = updateSpec.filter;
      if (add2.length == 0 && !filter)
        return this;
      if (sort)
        add2 = add2.slice().sort(cmpRange);
      if (this.isEmpty)
        return add2.length ? RangeSet.of(add2) : this;
      let cur2 = new LayerCursor(this, null, -1).goto(0), i = 0, spill = [];
      let builder = new RangeSetBuilder();
      while (cur2.value || i < add2.length) {
        if (i < add2.length && (cur2.from - add2[i].from || cur2.startSide - add2[i].value.startSide) >= 0) {
          let range = add2[i++];
          if (!builder.addInner(range.from, range.to, range.value))
            spill.push(range);
        } else if (cur2.rangeIndex == 1 && cur2.chunkIndex < this.chunk.length && (i == add2.length || this.chunkEnd(cur2.chunkIndex) < add2[i].from) && (!filter || filterFrom > this.chunkEnd(cur2.chunkIndex) || filterTo < this.chunkPos[cur2.chunkIndex]) && builder.addChunk(this.chunkPos[cur2.chunkIndex], this.chunk[cur2.chunkIndex])) {
          cur2.nextChunk();
        } else {
          if (!filter || filterFrom > cur2.to || filterTo < cur2.from || filter(cur2.from, cur2.to, cur2.value)) {
            if (!builder.addInner(cur2.from, cur2.to, cur2.value))
              spill.push(new Range(cur2.from, cur2.to, cur2.value));
          }
          cur2.next();
        }
      }
      return builder.finishInner(this.nextLayer.isEmpty && !spill.length ? RangeSet.empty : this.nextLayer.update({ add: spill, filter, filterFrom, filterTo }));
    }
    map(changes) {
      if (changes.empty || this.isEmpty)
        return this;
      let chunks = [], chunkPos = [], maxPoint = -1;
      for (let i = 0; i < this.chunk.length; i++) {
        let start = this.chunkPos[i], chunk = this.chunk[i];
        let touch = changes.touchesRange(start, start + chunk.length);
        if (touch === false) {
          maxPoint = Math.max(maxPoint, chunk.maxPoint);
          chunks.push(chunk);
          chunkPos.push(changes.mapPos(start));
        } else if (touch === true) {
          let { mapped, pos } = chunk.map(start, changes);
          if (mapped) {
            maxPoint = Math.max(maxPoint, mapped.maxPoint);
            chunks.push(mapped);
            chunkPos.push(pos);
          }
        }
      }
      let next = this.nextLayer.map(changes);
      return chunks.length == 0 ? next : new RangeSet(chunkPos, chunks, next, maxPoint);
    }
    between(from2, to, f) {
      if (this.isEmpty)
        return;
      for (let i = 0; i < this.chunk.length; i++) {
        let start = this.chunkPos[i], chunk = this.chunk[i];
        if (to >= start && from2 <= start + chunk.length && chunk.between(start, from2 - start, to - start, f) === false)
          return;
      }
      this.nextLayer.between(from2, to, f);
    }
    iter(from2 = 0) {
      return HeapCursor.from([this]).goto(from2);
    }
    get isEmpty() {
      return this.nextLayer == this;
    }
    static iter(sets, from2 = 0) {
      return HeapCursor.from(sets).goto(from2);
    }
    static compare(oldSets, newSets, textDiff, comparator, minPointSize = -1) {
      let a = oldSets.filter((set) => set.maxPoint > 0 || !set.isEmpty && set.maxPoint >= minPointSize);
      let b = newSets.filter((set) => set.maxPoint > 0 || !set.isEmpty && set.maxPoint >= minPointSize);
      let sharedChunks = findSharedChunks(a, b, textDiff);
      let sideA = new SpanCursor(a, sharedChunks, minPointSize);
      let sideB = new SpanCursor(b, sharedChunks, minPointSize);
      textDiff.iterGaps((fromA, fromB, length) => compare(sideA, fromA, sideB, fromB, length, comparator));
      if (textDiff.empty && textDiff.length == 0)
        compare(sideA, 0, sideB, 0, 0, comparator);
    }
    static eq(oldSets, newSets, from2 = 0, to) {
      if (to == null)
        to = 1e9;
      let a = oldSets.filter((set) => !set.isEmpty && newSets.indexOf(set) < 0);
      let b = newSets.filter((set) => !set.isEmpty && oldSets.indexOf(set) < 0);
      if (a.length != b.length)
        return false;
      if (!a.length)
        return true;
      let sharedChunks = findSharedChunks(a, b);
      let sideA = new SpanCursor(a, sharedChunks, 0).goto(from2), sideB = new SpanCursor(b, sharedChunks, 0).goto(from2);
      for (; ; ) {
        if (sideA.to != sideB.to || !sameValues(sideA.active, sideB.active) || sideA.point && (!sideB.point || !sideA.point.eq(sideB.point)))
          return false;
        if (sideA.to > to)
          return true;
        sideA.next();
        sideB.next();
      }
    }
    static spans(sets, from2, to, iterator, minPointSize = -1) {
      var _a2;
      let cursor = new SpanCursor(sets, null, minPointSize, (_a2 = iterator.filterPoint) === null || _a2 === void 0 ? void 0 : _a2.bind(iterator)).goto(from2), pos = from2;
      let open = cursor.openStart;
      for (; ; ) {
        let curTo = Math.min(cursor.to, to);
        if (cursor.point) {
          iterator.point(pos, curTo, cursor.point, cursor.activeForPoint(cursor.to), open);
          open = cursor.openEnd(curTo) + (cursor.to > curTo ? 1 : 0);
        } else if (curTo > pos) {
          iterator.span(pos, curTo, cursor.active, open);
          open = cursor.openEnd(curTo);
        }
        if (cursor.to > to)
          break;
        pos = cursor.to;
        cursor.next();
      }
      return open;
    }
    static of(ranges, sort = false) {
      let build = new RangeSetBuilder();
      for (let range of ranges instanceof Range ? [ranges] : sort ? lazySort(ranges) : ranges)
        build.add(range.from, range.to, range.value);
      return build.finish();
    }
  };
  RangeSet.empty = /* @__PURE__ */ new RangeSet([], [], null, -1);
  function lazySort(ranges) {
    if (ranges.length > 1)
      for (let prev = ranges[0], i = 1; i < ranges.length; i++) {
        let cur2 = ranges[i];
        if (cmpRange(prev, cur2) > 0)
          return ranges.slice().sort(cmpRange);
        prev = cur2;
      }
    return ranges;
  }
  RangeSet.empty.nextLayer = RangeSet.empty;
  var RangeSetBuilder = class {
    constructor() {
      this.chunks = [];
      this.chunkPos = [];
      this.chunkStart = -1;
      this.last = null;
      this.lastFrom = -1e9;
      this.lastTo = -1e9;
      this.from = [];
      this.to = [];
      this.value = [];
      this.maxPoint = -1;
      this.setMaxPoint = -1;
      this.nextLayer = null;
    }
    finishChunk(newArrays) {
      this.chunks.push(new Chunk(this.from, this.to, this.value, this.maxPoint));
      this.chunkPos.push(this.chunkStart);
      this.chunkStart = -1;
      this.setMaxPoint = Math.max(this.setMaxPoint, this.maxPoint);
      this.maxPoint = -1;
      if (newArrays) {
        this.from = [];
        this.to = [];
        this.value = [];
      }
    }
    add(from2, to, value) {
      if (!this.addInner(from2, to, value))
        (this.nextLayer || (this.nextLayer = new RangeSetBuilder())).add(from2, to, value);
    }
    addInner(from2, to, value) {
      let diff = from2 - this.lastTo || value.startSide - this.last.endSide;
      if (diff <= 0 && (from2 - this.lastFrom || value.startSide - this.last.startSide) < 0)
        throw new Error("Ranges must be added sorted by `from` position and `startSide`");
      if (diff < 0)
        return false;
      if (this.from.length == 250)
        this.finishChunk(true);
      if (this.chunkStart < 0)
        this.chunkStart = from2;
      this.from.push(from2 - this.chunkStart);
      this.to.push(to - this.chunkStart);
      this.last = value;
      this.lastFrom = from2;
      this.lastTo = to;
      this.value.push(value);
      if (value.point)
        this.maxPoint = Math.max(this.maxPoint, to - from2);
      return true;
    }
    addChunk(from2, chunk) {
      if ((from2 - this.lastTo || chunk.value[0].startSide - this.last.endSide) < 0)
        return false;
      if (this.from.length)
        this.finishChunk(true);
      this.setMaxPoint = Math.max(this.setMaxPoint, chunk.maxPoint);
      this.chunks.push(chunk);
      this.chunkPos.push(from2);
      let last = chunk.value.length - 1;
      this.last = chunk.value[last];
      this.lastFrom = chunk.from[last] + from2;
      this.lastTo = chunk.to[last] + from2;
      return true;
    }
    finish() {
      return this.finishInner(RangeSet.empty);
    }
    finishInner(next) {
      if (this.from.length)
        this.finishChunk(false);
      if (this.chunks.length == 0)
        return next;
      let result = new RangeSet(this.chunkPos, this.chunks, this.nextLayer ? this.nextLayer.finishInner(next) : next, this.setMaxPoint);
      this.from = null;
      return result;
    }
  };
  function findSharedChunks(a, b, textDiff) {
    let inA = /* @__PURE__ */ new Map();
    for (let set of a)
      for (let i = 0; i < set.chunk.length; i++)
        if (set.chunk[i].maxPoint <= 0)
          inA.set(set.chunk[i], set.chunkPos[i]);
    let shared = /* @__PURE__ */ new Set();
    for (let set of b)
      for (let i = 0; i < set.chunk.length; i++) {
        let known = inA.get(set.chunk[i]);
        if (known != null && (textDiff ? textDiff.mapPos(known) : known) == set.chunkPos[i] && !(textDiff === null || textDiff === void 0 ? void 0 : textDiff.touchesRange(known, known + set.chunk[i].length)))
          shared.add(set.chunk[i]);
      }
    return shared;
  }
  var LayerCursor = class {
    constructor(layer, skip, minPoint, rank = 0) {
      this.layer = layer;
      this.skip = skip;
      this.minPoint = minPoint;
      this.rank = rank;
    }
    get startSide() {
      return this.value ? this.value.startSide : 0;
    }
    get endSide() {
      return this.value ? this.value.endSide : 0;
    }
    goto(pos, side = -1e9) {
      this.chunkIndex = this.rangeIndex = 0;
      this.gotoInner(pos, side, false);
      return this;
    }
    gotoInner(pos, side, forward) {
      while (this.chunkIndex < this.layer.chunk.length) {
        let next = this.layer.chunk[this.chunkIndex];
        if (!(this.skip && this.skip.has(next) || this.layer.chunkEnd(this.chunkIndex) < pos || next.maxPoint < this.minPoint))
          break;
        this.chunkIndex++;
        forward = false;
      }
      if (this.chunkIndex < this.layer.chunk.length) {
        let rangeIndex = this.layer.chunk[this.chunkIndex].findIndex(pos - this.layer.chunkPos[this.chunkIndex], side, true);
        if (!forward || this.rangeIndex < rangeIndex)
          this.setRangeIndex(rangeIndex);
      }
      this.next();
    }
    forward(pos, side) {
      if ((this.to - pos || this.endSide - side) < 0)
        this.gotoInner(pos, side, true);
    }
    next() {
      for (; ; ) {
        if (this.chunkIndex == this.layer.chunk.length) {
          this.from = this.to = 1e9;
          this.value = null;
          break;
        } else {
          let chunkPos = this.layer.chunkPos[this.chunkIndex], chunk = this.layer.chunk[this.chunkIndex];
          let from2 = chunkPos + chunk.from[this.rangeIndex];
          this.from = from2;
          this.to = chunkPos + chunk.to[this.rangeIndex];
          this.value = chunk.value[this.rangeIndex];
          this.setRangeIndex(this.rangeIndex + 1);
          if (this.minPoint < 0 || this.value.point && this.to - this.from >= this.minPoint)
            break;
        }
      }
    }
    setRangeIndex(index) {
      if (index == this.layer.chunk[this.chunkIndex].value.length) {
        this.chunkIndex++;
        if (this.skip) {
          while (this.chunkIndex < this.layer.chunk.length && this.skip.has(this.layer.chunk[this.chunkIndex]))
            this.chunkIndex++;
        }
        this.rangeIndex = 0;
      } else {
        this.rangeIndex = index;
      }
    }
    nextChunk() {
      this.chunkIndex++;
      this.rangeIndex = 0;
      this.next();
    }
    compare(other) {
      return this.from - other.from || this.startSide - other.startSide || this.rank - other.rank || this.to - other.to || this.endSide - other.endSide;
    }
  };
  var HeapCursor = class {
    constructor(heap) {
      this.heap = heap;
    }
    static from(sets, skip = null, minPoint = -1) {
      let heap = [];
      for (let i = 0; i < sets.length; i++) {
        for (let cur2 = sets[i]; !cur2.isEmpty; cur2 = cur2.nextLayer) {
          if (cur2.maxPoint >= minPoint)
            heap.push(new LayerCursor(cur2, skip, minPoint, i));
        }
      }
      return heap.length == 1 ? heap[0] : new HeapCursor(heap);
    }
    get startSide() {
      return this.value ? this.value.startSide : 0;
    }
    goto(pos, side = -1e9) {
      for (let cur2 of this.heap)
        cur2.goto(pos, side);
      for (let i = this.heap.length >> 1; i >= 0; i--)
        heapBubble(this.heap, i);
      this.next();
      return this;
    }
    forward(pos, side) {
      for (let cur2 of this.heap)
        cur2.forward(pos, side);
      for (let i = this.heap.length >> 1; i >= 0; i--)
        heapBubble(this.heap, i);
      if ((this.to - pos || this.value.endSide - side) < 0)
        this.next();
    }
    next() {
      if (this.heap.length == 0) {
        this.from = this.to = 1e9;
        this.value = null;
        this.rank = -1;
      } else {
        let top2 = this.heap[0];
        this.from = top2.from;
        this.to = top2.to;
        this.value = top2.value;
        this.rank = top2.rank;
        if (top2.value)
          top2.next();
        heapBubble(this.heap, 0);
      }
    }
  };
  function heapBubble(heap, index) {
    for (let cur2 = heap[index]; ; ) {
      let childIndex = (index << 1) + 1;
      if (childIndex >= heap.length)
        break;
      let child = heap[childIndex];
      if (childIndex + 1 < heap.length && child.compare(heap[childIndex + 1]) >= 0) {
        child = heap[childIndex + 1];
        childIndex++;
      }
      if (cur2.compare(child) < 0)
        break;
      heap[childIndex] = cur2;
      heap[index] = child;
      index = childIndex;
    }
  }
  var SpanCursor = class {
    constructor(sets, skip, minPoint, filterPoint = () => true) {
      this.minPoint = minPoint;
      this.filterPoint = filterPoint;
      this.active = [];
      this.activeTo = [];
      this.activeRank = [];
      this.minActive = -1;
      this.point = null;
      this.pointFrom = 0;
      this.pointRank = 0;
      this.to = -1e9;
      this.endSide = 0;
      this.openStart = -1;
      this.cursor = HeapCursor.from(sets, skip, minPoint);
    }
    goto(pos, side = -1e9) {
      this.cursor.goto(pos, side);
      this.active.length = this.activeTo.length = this.activeRank.length = 0;
      this.minActive = -1;
      this.to = pos;
      this.endSide = side;
      this.openStart = -1;
      this.next();
      return this;
    }
    forward(pos, side) {
      while (this.minActive > -1 && (this.activeTo[this.minActive] - pos || this.active[this.minActive].endSide - side) < 0)
        this.removeActive(this.minActive);
      this.cursor.forward(pos, side);
    }
    removeActive(index) {
      remove(this.active, index);
      remove(this.activeTo, index);
      remove(this.activeRank, index);
      this.minActive = findMinIndex(this.active, this.activeTo);
    }
    addActive(trackOpen) {
      let i = 0, { value, to, rank } = this.cursor;
      while (i < this.activeRank.length && this.activeRank[i] <= rank)
        i++;
      insert(this.active, i, value);
      insert(this.activeTo, i, to);
      insert(this.activeRank, i, rank);
      if (trackOpen)
        insert(trackOpen, i, this.cursor.from);
      this.minActive = findMinIndex(this.active, this.activeTo);
    }
    next() {
      let from2 = this.to, wasPoint = this.point;
      this.point = null;
      let trackOpen = this.openStart < 0 ? [] : null, trackExtra = 0;
      for (; ; ) {
        let a = this.minActive;
        if (a > -1 && (this.activeTo[a] - this.cursor.from || this.active[a].endSide - this.cursor.startSide) < 0) {
          if (this.activeTo[a] > from2) {
            this.to = this.activeTo[a];
            this.endSide = this.active[a].endSide;
            break;
          }
          this.removeActive(a);
          if (trackOpen)
            remove(trackOpen, a);
        } else if (!this.cursor.value) {
          this.to = this.endSide = 1e9;
          break;
        } else if (this.cursor.from > from2) {
          this.to = this.cursor.from;
          this.endSide = this.cursor.startSide;
          break;
        } else {
          let nextVal = this.cursor.value;
          if (!nextVal.point) {
            this.addActive(trackOpen);
            this.cursor.next();
          } else if (wasPoint && this.cursor.to == this.to && this.cursor.from < this.cursor.to) {
            this.cursor.next();
          } else if (!this.filterPoint(this.cursor.from, this.cursor.to, this.cursor.value, this.cursor.rank)) {
            this.cursor.next();
          } else {
            this.point = nextVal;
            this.pointFrom = this.cursor.from;
            this.pointRank = this.cursor.rank;
            this.to = this.cursor.to;
            this.endSide = nextVal.endSide;
            if (this.cursor.from < from2)
              trackExtra = 1;
            this.cursor.next();
            this.forward(this.to, this.endSide);
            break;
          }
        }
      }
      if (trackOpen) {
        let openStart = 0;
        while (openStart < trackOpen.length && trackOpen[openStart] < from2)
          openStart++;
        this.openStart = openStart + trackExtra;
      }
    }
    activeForPoint(to) {
      if (!this.active.length)
        return this.active;
      let active = [];
      for (let i = this.active.length - 1; i >= 0; i--) {
        if (this.activeRank[i] < this.pointRank)
          break;
        if (this.activeTo[i] > to || this.activeTo[i] == to && this.active[i].endSide >= this.point.endSide)
          active.push(this.active[i]);
      }
      return active.reverse();
    }
    openEnd(to) {
      let open = 0;
      for (let i = this.activeTo.length - 1; i >= 0 && this.activeTo[i] > to; i--)
        open++;
      return open;
    }
  };
  function compare(a, startA, b, startB, length, comparator) {
    a.goto(startA);
    b.goto(startB);
    let endB = startB + length;
    let pos = startB, dPos = startB - startA;
    for (; ; ) {
      let diff = a.to + dPos - b.to || a.endSide - b.endSide;
      let end = diff < 0 ? a.to + dPos : b.to, clipEnd = Math.min(end, endB);
      if (a.point || b.point) {
        if (!(a.point && b.point && (a.point == b.point || a.point.eq(b.point)) && sameValues(a.activeForPoint(a.to + dPos), b.activeForPoint(b.to))))
          comparator.comparePoint(pos, clipEnd, a.point, b.point);
      } else {
        if (clipEnd > pos && !sameValues(a.active, b.active))
          comparator.compareRange(pos, clipEnd, a.active, b.active);
      }
      if (end > endB)
        break;
      pos = end;
      if (diff <= 0)
        a.next();
      if (diff >= 0)
        b.next();
    }
  }
  function sameValues(a, b) {
    if (a.length != b.length)
      return false;
    for (let i = 0; i < a.length; i++)
      if (a[i] != b[i] && !a[i].eq(b[i]))
        return false;
    return true;
  }
  function remove(array2, index) {
    for (let i = index, e = array2.length - 1; i < e; i++)
      array2[i] = array2[i + 1];
    array2.pop();
  }
  function insert(array2, index, value) {
    for (let i = array2.length - 1; i >= index; i--)
      array2[i + 1] = array2[i];
    array2[index] = value;
  }
  function findMinIndex(value, array2) {
    let found = -1, foundPos = 1e9;
    for (let i = 0; i < array2.length; i++)
      if ((array2[i] - foundPos || value[i].endSide - value[found].endSide) < 0) {
        found = i;
        foundPos = array2[i];
      }
    return found;
  }

  // node_modules/w3c-keyname/index.es.js
  var base = {
    8: "Backspace",
    9: "Tab",
    10: "Enter",
    12: "NumLock",
    13: "Enter",
    16: "Shift",
    17: "Control",
    18: "Alt",
    20: "CapsLock",
    27: "Escape",
    32: " ",
    33: "PageUp",
    34: "PageDown",
    35: "End",
    36: "Home",
    37: "ArrowLeft",
    38: "ArrowUp",
    39: "ArrowRight",
    40: "ArrowDown",
    44: "PrintScreen",
    45: "Insert",
    46: "Delete",
    59: ";",
    61: "=",
    91: "Meta",
    92: "Meta",
    106: "*",
    107: "+",
    108: ",",
    109: "-",
    110: ".",
    111: "/",
    144: "NumLock",
    145: "ScrollLock",
    160: "Shift",
    161: "Shift",
    162: "Control",
    163: "Control",
    164: "Alt",
    165: "Alt",
    173: "-",
    186: ";",
    187: "=",
    188: ",",
    189: "-",
    190: ".",
    191: "/",
    192: "`",
    219: "[",
    220: "\\",
    221: "]",
    222: "'",
    229: "q"
  };
  var shift = {
    48: ")",
    49: "!",
    50: "@",
    51: "#",
    52: "$",
    53: "%",
    54: "^",
    55: "&",
    56: "*",
    57: "(",
    59: ":",
    61: "+",
    173: "_",
    186: ":",
    187: "+",
    188: "<",
    189: "_",
    190: ">",
    191: "?",
    192: "~",
    219: "{",
    220: "|",
    221: "}",
    222: '"',
    229: "Q"
  };
  var chrome = typeof navigator != "undefined" && /Chrome\/(\d+)/.exec(navigator.userAgent);
  var safari = typeof navigator != "undefined" && /Apple Computer/.test(navigator.vendor);
  var gecko = typeof navigator != "undefined" && /Gecko\/\d+/.test(navigator.userAgent);
  var mac = typeof navigator != "undefined" && /Mac/.test(navigator.platform);
  var ie = typeof navigator != "undefined" && /MSIE \d|Trident\/(?:[7-9]|\d{2,})\..*rv:(\d+)/.exec(navigator.userAgent);
  var brokenModifierNames = chrome && (mac || +chrome[1] < 57) || gecko && mac;
  for (i = 0; i < 10; i++)
    base[48 + i] = base[96 + i] = String(i);
  var i;
  for (i = 1; i <= 24; i++)
    base[i + 111] = "F" + i;
  var i;
  for (i = 65; i <= 90; i++) {
    base[i] = String.fromCharCode(i + 32);
    shift[i] = String.fromCharCode(i);
  }
  var i;
  for (code in base)
    if (!shift.hasOwnProperty(code))
      shift[code] = base[code];
  var code;
  function keyName(event) {
    var ignoreKey = brokenModifierNames && (event.ctrlKey || event.altKey || event.metaKey) || (safari || ie) && event.shiftKey && event.key && event.key.length == 1;
    var name2 = !ignoreKey && event.key || (event.shiftKey ? shift : base)[event.keyCode] || event.key || "Unidentified";
    if (name2 == "Esc")
      name2 = "Escape";
    if (name2 == "Del")
      name2 = "Delete";
    if (name2 == "Left")
      name2 = "ArrowLeft";
    if (name2 == "Up")
      name2 = "ArrowUp";
    if (name2 == "Right")
      name2 = "ArrowRight";
    if (name2 == "Down")
      name2 = "ArrowDown";
    return name2;
  }

  // node_modules/@codemirror/view/dist/index.js
  function getSelection(root) {
    let target;
    if (root.nodeType == 11) {
      target = root.getSelection ? root : root.ownerDocument;
    } else {
      target = root;
    }
    return target.getSelection();
  }
  function contains(dom, node) {
    return node ? dom == node || dom.contains(node.nodeType != 1 ? node.parentNode : node) : false;
  }
  function deepActiveElement() {
    let elt = document.activeElement;
    while (elt && elt.shadowRoot)
      elt = elt.shadowRoot.activeElement;
    return elt;
  }
  function hasSelection(dom, selection) {
    if (!selection.anchorNode)
      return false;
    try {
      return contains(dom, selection.anchorNode);
    } catch (_) {
      return false;
    }
  }
  function clientRectsFor(dom) {
    if (dom.nodeType == 3)
      return textRange(dom, 0, dom.nodeValue.length).getClientRects();
    else if (dom.nodeType == 1)
      return dom.getClientRects();
    else
      return [];
  }
  function isEquivalentPosition(node, off, targetNode, targetOff) {
    return targetNode ? scanFor(node, off, targetNode, targetOff, -1) || scanFor(node, off, targetNode, targetOff, 1) : false;
  }
  function domIndex(node) {
    for (var index = 0; ; index++) {
      node = node.previousSibling;
      if (!node)
        return index;
    }
  }
  function scanFor(node, off, targetNode, targetOff, dir) {
    for (; ; ) {
      if (node == targetNode && off == targetOff)
        return true;
      if (off == (dir < 0 ? 0 : maxOffset(node))) {
        if (node.nodeName == "DIV")
          return false;
        let parent = node.parentNode;
        if (!parent || parent.nodeType != 1)
          return false;
        off = domIndex(node) + (dir < 0 ? 0 : 1);
        node = parent;
      } else if (node.nodeType == 1) {
        node = node.childNodes[off + (dir < 0 ? -1 : 0)];
        if (node.nodeType == 1 && node.contentEditable == "false")
          return false;
        off = dir < 0 ? maxOffset(node) : 0;
      } else {
        return false;
      }
    }
  }
  function maxOffset(node) {
    return node.nodeType == 3 ? node.nodeValue.length : node.childNodes.length;
  }
  var Rect0 = { left: 0, right: 0, top: 0, bottom: 0 };
  function flattenRect(rect, left) {
    let x = left ? rect.left : rect.right;
    return { left: x, right: x, top: rect.top, bottom: rect.bottom };
  }
  function windowRect(win) {
    return {
      left: 0,
      right: win.innerWidth,
      top: 0,
      bottom: win.innerHeight
    };
  }
  function scrollRectIntoView(dom, rect, side, x, y, xMargin, yMargin, ltr) {
    let doc2 = dom.ownerDocument, win = doc2.defaultView;
    for (let cur2 = dom; cur2; ) {
      if (cur2.nodeType == 1) {
        let bounding, top2 = cur2 == doc2.body;
        if (top2) {
          bounding = windowRect(win);
        } else {
          if (cur2.scrollHeight <= cur2.clientHeight && cur2.scrollWidth <= cur2.clientWidth) {
            cur2 = cur2.parentNode;
            continue;
          }
          let rect2 = cur2.getBoundingClientRect();
          bounding = {
            left: rect2.left,
            right: rect2.left + cur2.clientWidth,
            top: rect2.top,
            bottom: rect2.top + cur2.clientHeight
          };
        }
        let moveX = 0, moveY = 0;
        if (y == "nearest") {
          if (rect.top < bounding.top) {
            moveY = -(bounding.top - rect.top + yMargin);
            if (side > 0 && rect.bottom > bounding.bottom + moveY)
              moveY = rect.bottom - bounding.bottom + moveY + yMargin;
          } else if (rect.bottom > bounding.bottom) {
            moveY = rect.bottom - bounding.bottom + yMargin;
            if (side < 0 && rect.top - moveY < bounding.top)
              moveY = -(bounding.top + moveY - rect.top + yMargin);
          }
        } else {
          let rectHeight = rect.bottom - rect.top, boundingHeight = bounding.bottom - bounding.top;
          let targetTop = y == "center" && rectHeight <= boundingHeight ? rect.top + rectHeight / 2 - boundingHeight / 2 : y == "start" || y == "center" && side < 0 ? rect.top - yMargin : rect.bottom - boundingHeight + yMargin;
          moveY = targetTop - bounding.top;
        }
        if (x == "nearest") {
          if (rect.left < bounding.left) {
            moveX = -(bounding.left - rect.left + xMargin);
            if (side > 0 && rect.right > bounding.right + moveX)
              moveX = rect.right - bounding.right + moveX + xMargin;
          } else if (rect.right > bounding.right) {
            moveX = rect.right - bounding.right + xMargin;
            if (side < 0 && rect.left < bounding.left + moveX)
              moveX = -(bounding.left + moveX - rect.left + xMargin);
          }
        } else {
          let targetLeft = x == "center" ? rect.left + (rect.right - rect.left) / 2 - (bounding.right - bounding.left) / 2 : x == "start" == ltr ? rect.left - xMargin : rect.right - (bounding.right - bounding.left) + xMargin;
          moveX = targetLeft - bounding.left;
        }
        if (moveX || moveY) {
          if (top2) {
            win.scrollBy(moveX, moveY);
          } else {
            if (moveY) {
              let start = cur2.scrollTop;
              cur2.scrollTop += moveY;
              moveY = cur2.scrollTop - start;
            }
            if (moveX) {
              let start = cur2.scrollLeft;
              cur2.scrollLeft += moveX;
              moveX = cur2.scrollLeft - start;
            }
            rect = {
              left: rect.left - moveX,
              top: rect.top - moveY,
              right: rect.right - moveX,
              bottom: rect.bottom - moveY
            };
          }
        }
        if (top2)
          break;
        cur2 = cur2.assignedSlot || cur2.parentNode;
        x = y = "nearest";
      } else if (cur2.nodeType == 11) {
        cur2 = cur2.host;
      } else {
        break;
      }
    }
  }
  var DOMSelectionState = class {
    constructor() {
      this.anchorNode = null;
      this.anchorOffset = 0;
      this.focusNode = null;
      this.focusOffset = 0;
    }
    eq(domSel) {
      return this.anchorNode == domSel.anchorNode && this.anchorOffset == domSel.anchorOffset && this.focusNode == domSel.focusNode && this.focusOffset == domSel.focusOffset;
    }
    setRange(range) {
      this.set(range.anchorNode, range.anchorOffset, range.focusNode, range.focusOffset);
    }
    set(anchorNode, anchorOffset, focusNode, focusOffset) {
      this.anchorNode = anchorNode;
      this.anchorOffset = anchorOffset;
      this.focusNode = focusNode;
      this.focusOffset = focusOffset;
    }
  };
  var preventScrollSupported = null;
  function focusPreventScroll(dom) {
    if (dom.setActive)
      return dom.setActive();
    if (preventScrollSupported)
      return dom.focus(preventScrollSupported);
    let stack = [];
    for (let cur2 = dom; cur2; cur2 = cur2.parentNode) {
      stack.push(cur2, cur2.scrollTop, cur2.scrollLeft);
      if (cur2 == cur2.ownerDocument)
        break;
    }
    dom.focus(preventScrollSupported == null ? {
      get preventScroll() {
        preventScrollSupported = { preventScroll: true };
        return true;
      }
    } : void 0);
    if (!preventScrollSupported) {
      preventScrollSupported = false;
      for (let i = 0; i < stack.length; ) {
        let elt = stack[i++], top2 = stack[i++], left = stack[i++];
        if (elt.scrollTop != top2)
          elt.scrollTop = top2;
        if (elt.scrollLeft != left)
          elt.scrollLeft = left;
      }
    }
  }
  var scratchRange;
  function textRange(node, from2, to = from2) {
    let range = scratchRange || (scratchRange = document.createRange());
    range.setEnd(node, to);
    range.setStart(node, from2);
    return range;
  }
  function dispatchKey(elt, name2, code) {
    let options = { key: name2, code: name2, keyCode: code, which: code, cancelable: true };
    let down = new KeyboardEvent("keydown", options);
    down.synthetic = true;
    elt.dispatchEvent(down);
    let up = new KeyboardEvent("keyup", options);
    up.synthetic = true;
    elt.dispatchEvent(up);
    return down.defaultPrevented || up.defaultPrevented;
  }
  function getRoot(node) {
    while (node) {
      if (node && (node.nodeType == 9 || node.nodeType == 11 && node.host))
        return node;
      node = node.assignedSlot || node.parentNode;
    }
    return null;
  }
  function clearAttributes(node) {
    while (node.attributes.length)
      node.removeAttributeNode(node.attributes[0]);
  }
  var DOMPos = class {
    constructor(node, offset, precise = true) {
      this.node = node;
      this.offset = offset;
      this.precise = precise;
    }
    static before(dom, precise) {
      return new DOMPos(dom.parentNode, domIndex(dom), precise);
    }
    static after(dom, precise) {
      return new DOMPos(dom.parentNode, domIndex(dom) + 1, precise);
    }
  };
  var noChildren = [];
  var ContentView = class {
    constructor() {
      this.parent = null;
      this.dom = null;
      this.dirty = 2;
    }
    get editorView() {
      if (!this.parent)
        throw new Error("Accessing view in orphan content view");
      return this.parent.editorView;
    }
    get overrideDOMText() {
      return null;
    }
    get posAtStart() {
      return this.parent ? this.parent.posBefore(this) : 0;
    }
    get posAtEnd() {
      return this.posAtStart + this.length;
    }
    posBefore(view) {
      let pos = this.posAtStart;
      for (let child of this.children) {
        if (child == view)
          return pos;
        pos += child.length + child.breakAfter;
      }
      throw new RangeError("Invalid child in posBefore");
    }
    posAfter(view) {
      return this.posBefore(view) + view.length;
    }
    coordsAt(_pos, _side) {
      return null;
    }
    sync(track) {
      if (this.dirty & 2) {
        let parent = this.dom;
        let prev = null, next;
        for (let child of this.children) {
          if (child.dirty) {
            if (!child.dom && (next = prev ? prev.nextSibling : parent.firstChild)) {
              let contentView = ContentView.get(next);
              if (!contentView || !contentView.parent && contentView.constructor == child.constructor)
                child.reuseDOM(next);
            }
            child.sync(track);
            child.dirty = 0;
          }
          next = prev ? prev.nextSibling : parent.firstChild;
          if (track && !track.written && track.node == parent && next != child.dom)
            track.written = true;
          if (child.dom.parentNode == parent) {
            while (next && next != child.dom)
              next = rm(next);
          } else {
            parent.insertBefore(child.dom, next);
          }
          prev = child.dom;
        }
        next = prev ? prev.nextSibling : parent.firstChild;
        if (next && track && track.node == parent)
          track.written = true;
        while (next)
          next = rm(next);
      } else if (this.dirty & 1) {
        for (let child of this.children)
          if (child.dirty) {
            child.sync(track);
            child.dirty = 0;
          }
      }
    }
    reuseDOM(_dom) {
    }
    localPosFromDOM(node, offset) {
      let after;
      if (node == this.dom) {
        after = this.dom.childNodes[offset];
      } else {
        let bias = maxOffset(node) == 0 ? 0 : offset == 0 ? -1 : 1;
        for (; ; ) {
          let parent = node.parentNode;
          if (parent == this.dom)
            break;
          if (bias == 0 && parent.firstChild != parent.lastChild) {
            if (node == parent.firstChild)
              bias = -1;
            else
              bias = 1;
          }
          node = parent;
        }
        if (bias < 0)
          after = node;
        else
          after = node.nextSibling;
      }
      if (after == this.dom.firstChild)
        return 0;
      while (after && !ContentView.get(after))
        after = after.nextSibling;
      if (!after)
        return this.length;
      for (let i = 0, pos = 0; ; i++) {
        let child = this.children[i];
        if (child.dom == after)
          return pos;
        pos += child.length + child.breakAfter;
      }
    }
    domBoundsAround(from2, to, offset = 0) {
      let fromI = -1, fromStart = -1, toI = -1, toEnd = -1;
      for (let i = 0, pos = offset, prevEnd = offset; i < this.children.length; i++) {
        let child = this.children[i], end = pos + child.length;
        if (pos < from2 && end > to)
          return child.domBoundsAround(from2, to, pos);
        if (end >= from2 && fromI == -1) {
          fromI = i;
          fromStart = pos;
        }
        if (pos > to && child.dom.parentNode == this.dom) {
          toI = i;
          toEnd = prevEnd;
          break;
        }
        prevEnd = end;
        pos = end + child.breakAfter;
      }
      return {
        from: fromStart,
        to: toEnd < 0 ? offset + this.length : toEnd,
        startDOM: (fromI ? this.children[fromI - 1].dom.nextSibling : null) || this.dom.firstChild,
        endDOM: toI < this.children.length && toI >= 0 ? this.children[toI].dom : null
      };
    }
    markDirty(andParent = false) {
      this.dirty |= 2;
      this.markParentsDirty(andParent);
    }
    markParentsDirty(childList) {
      for (let parent = this.parent; parent; parent = parent.parent) {
        if (childList)
          parent.dirty |= 2;
        if (parent.dirty & 1)
          return;
        parent.dirty |= 1;
        childList = false;
      }
    }
    setParent(parent) {
      if (this.parent != parent) {
        this.parent = parent;
        if (this.dirty)
          this.markParentsDirty(true);
      }
    }
    setDOM(dom) {
      if (this.dom)
        this.dom.cmView = null;
      this.dom = dom;
      dom.cmView = this;
    }
    get rootView() {
      for (let v = this; ; ) {
        let parent = v.parent;
        if (!parent)
          return v;
        v = parent;
      }
    }
    replaceChildren(from2, to, children = noChildren) {
      this.markDirty();
      for (let i = from2; i < to; i++) {
        let child = this.children[i];
        if (child.parent == this)
          child.destroy();
      }
      this.children.splice(from2, to - from2, ...children);
      for (let i = 0; i < children.length; i++)
        children[i].setParent(this);
    }
    ignoreMutation(_rec) {
      return false;
    }
    ignoreEvent(_event) {
      return false;
    }
    childCursor(pos = this.length) {
      return new ChildCursor(this.children, pos, this.children.length);
    }
    childPos(pos, bias = 1) {
      return this.childCursor().findPos(pos, bias);
    }
    toString() {
      let name2 = this.constructor.name.replace("View", "");
      return name2 + (this.children.length ? "(" + this.children.join() + ")" : this.length ? "[" + (name2 == "Text" ? this.text : this.length) + "]" : "") + (this.breakAfter ? "#" : "");
    }
    static get(node) {
      return node.cmView;
    }
    get isEditable() {
      return true;
    }
    merge(from2, to, source, hasStart, openStart, openEnd) {
      return false;
    }
    become(other) {
      return false;
    }
    getSide() {
      return 0;
    }
    destroy() {
      this.parent = null;
    }
  };
  ContentView.prototype.breakAfter = 0;
  function rm(dom) {
    let next = dom.nextSibling;
    dom.parentNode.removeChild(dom);
    return next;
  }
  var ChildCursor = class {
    constructor(children, pos, i) {
      this.children = children;
      this.pos = pos;
      this.i = i;
      this.off = 0;
    }
    findPos(pos, bias = 1) {
      for (; ; ) {
        if (pos > this.pos || pos == this.pos && (bias > 0 || this.i == 0 || this.children[this.i - 1].breakAfter)) {
          this.off = pos - this.pos;
          return this;
        }
        let next = this.children[--this.i];
        this.pos -= next.length + next.breakAfter;
      }
    }
  };
  function replaceRange(parent, fromI, fromOff, toI, toOff, insert2, breakAtStart, openStart, openEnd) {
    let { children } = parent;
    let before = children.length ? children[fromI] : null;
    let last = insert2.length ? insert2[insert2.length - 1] : null;
    let breakAtEnd = last ? last.breakAfter : breakAtStart;
    if (fromI == toI && before && !breakAtStart && !breakAtEnd && insert2.length < 2 && before.merge(fromOff, toOff, insert2.length ? last : null, fromOff == 0, openStart, openEnd))
      return;
    if (toI < children.length) {
      let after = children[toI];
      if (after && toOff < after.length) {
        if (fromI == toI) {
          after = after.split(toOff);
          toOff = 0;
        }
        if (!breakAtEnd && last && after.merge(0, toOff, last, true, 0, openEnd)) {
          insert2[insert2.length - 1] = after;
        } else {
          if (toOff)
            after.merge(0, toOff, null, false, 0, openEnd);
          insert2.push(after);
        }
      } else if (after === null || after === void 0 ? void 0 : after.breakAfter) {
        if (last)
          last.breakAfter = 1;
        else
          breakAtStart = 1;
      }
      toI++;
    }
    if (before) {
      before.breakAfter = breakAtStart;
      if (fromOff > 0) {
        if (!breakAtStart && insert2.length && before.merge(fromOff, before.length, insert2[0], false, openStart, 0)) {
          before.breakAfter = insert2.shift().breakAfter;
        } else if (fromOff < before.length || before.children.length && before.children[before.children.length - 1].length == 0) {
          before.merge(fromOff, before.length, null, false, openStart, 0);
        }
        fromI++;
      }
    }
    while (fromI < toI && insert2.length) {
      if (children[toI - 1].become(insert2[insert2.length - 1])) {
        toI--;
        insert2.pop();
        openEnd = insert2.length ? 0 : openStart;
      } else if (children[fromI].become(insert2[0])) {
        fromI++;
        insert2.shift();
        openStart = insert2.length ? 0 : openEnd;
      } else {
        break;
      }
    }
    if (!insert2.length && fromI && toI < children.length && !children[fromI - 1].breakAfter && children[toI].merge(0, 0, children[fromI - 1], false, openStart, openEnd))
      fromI--;
    if (fromI < toI || insert2.length)
      parent.replaceChildren(fromI, toI, insert2);
  }
  function mergeChildrenInto(parent, from2, to, insert2, openStart, openEnd) {
    let cur2 = parent.childCursor();
    let { i: toI, off: toOff } = cur2.findPos(to, 1);
    let { i: fromI, off: fromOff } = cur2.findPos(from2, -1);
    let dLen = from2 - to;
    for (let view of insert2)
      dLen += view.length;
    parent.length += dLen;
    replaceRange(parent, fromI, fromOff, toI, toOff, insert2, 0, openStart, openEnd);
  }
  var nav = typeof navigator != "undefined" ? navigator : { userAgent: "", vendor: "", platform: "" };
  var doc = typeof document != "undefined" ? document : { documentElement: { style: {} } };
  var ie_edge = /* @__PURE__ */ /Edge\/(\d+)/.exec(nav.userAgent);
  var ie_upto10 = /* @__PURE__ */ /MSIE \d/.test(nav.userAgent);
  var ie_11up = /* @__PURE__ */ /Trident\/(?:[7-9]|\d{2,})\..*rv:(\d+)/.exec(nav.userAgent);
  var ie2 = !!(ie_upto10 || ie_11up || ie_edge);
  var gecko2 = !ie2 && /* @__PURE__ */ /gecko\/(\d+)/i.test(nav.userAgent);
  var chrome2 = !ie2 && /* @__PURE__ */ /Chrome\/(\d+)/.exec(nav.userAgent);
  var webkit = "webkitFontSmoothing" in doc.documentElement.style;
  var safari2 = !ie2 && /* @__PURE__ */ /Apple Computer/.test(nav.vendor);
  var ios = safari2 && (/* @__PURE__ */ /Mobile\/\w+/.test(nav.userAgent) || nav.maxTouchPoints > 2);
  var browser = {
    mac: ios || /* @__PURE__ */ /Mac/.test(nav.platform),
    windows: /* @__PURE__ */ /Win/.test(nav.platform),
    linux: /* @__PURE__ */ /Linux|X11/.test(nav.platform),
    ie: ie2,
    ie_version: ie_upto10 ? doc.documentMode || 6 : ie_11up ? +ie_11up[1] : ie_edge ? +ie_edge[1] : 0,
    gecko: gecko2,
    gecko_version: gecko2 ? +(/* @__PURE__ */ /Firefox\/(\d+)/.exec(nav.userAgent) || [0, 0])[1] : 0,
    chrome: !!chrome2,
    chrome_version: chrome2 ? +chrome2[1] : 0,
    ios,
    android: /* @__PURE__ */ /Android\b/.test(nav.userAgent),
    webkit,
    safari: safari2,
    webkit_version: webkit ? +(/* @__PURE__ */ /\bAppleWebKit\/(\d+)/.exec(navigator.userAgent) || [0, 0])[1] : 0,
    tabSize: doc.documentElement.style.tabSize != null ? "tab-size" : "-moz-tab-size"
  };
  var MaxJoinLen = 256;
  var TextView = class extends ContentView {
    constructor(text) {
      super();
      this.text = text;
    }
    get length() {
      return this.text.length;
    }
    createDOM(textDOM) {
      this.setDOM(textDOM || document.createTextNode(this.text));
    }
    sync(track) {
      if (!this.dom)
        this.createDOM();
      if (this.dom.nodeValue != this.text) {
        if (track && track.node == this.dom)
          track.written = true;
        this.dom.nodeValue = this.text;
      }
    }
    reuseDOM(dom) {
      if (dom.nodeType == 3)
        this.createDOM(dom);
    }
    merge(from2, to, source) {
      if (source && (!(source instanceof TextView) || this.length - (to - from2) + source.length > MaxJoinLen))
        return false;
      this.text = this.text.slice(0, from2) + (source ? source.text : "") + this.text.slice(to);
      this.markDirty();
      return true;
    }
    split(from2) {
      let result = new TextView(this.text.slice(from2));
      this.text = this.text.slice(0, from2);
      this.markDirty();
      return result;
    }
    localPosFromDOM(node, offset) {
      return node == this.dom ? offset : offset ? this.text.length : 0;
    }
    domAtPos(pos) {
      return new DOMPos(this.dom, pos);
    }
    domBoundsAround(_from, _to, offset) {
      return { from: offset, to: offset + this.length, startDOM: this.dom, endDOM: this.dom.nextSibling };
    }
    coordsAt(pos, side) {
      return textCoords(this.dom, pos, side);
    }
  };
  var MarkView = class extends ContentView {
    constructor(mark, children = [], length = 0) {
      super();
      this.mark = mark;
      this.children = children;
      this.length = length;
      for (let ch of children)
        ch.setParent(this);
    }
    setAttrs(dom) {
      clearAttributes(dom);
      if (this.mark.class)
        dom.className = this.mark.class;
      if (this.mark.attrs)
        for (let name2 in this.mark.attrs)
          dom.setAttribute(name2, this.mark.attrs[name2]);
      return dom;
    }
    reuseDOM(node) {
      if (node.nodeName == this.mark.tagName.toUpperCase()) {
        this.setDOM(node);
        this.dirty |= 4 | 2;
      }
    }
    sync(track) {
      if (!this.dom)
        this.setDOM(this.setAttrs(document.createElement(this.mark.tagName)));
      else if (this.dirty & 4)
        this.setAttrs(this.dom);
      super.sync(track);
    }
    merge(from2, to, source, _hasStart, openStart, openEnd) {
      if (source && (!(source instanceof MarkView && source.mark.eq(this.mark)) || from2 && openStart <= 0 || to < this.length && openEnd <= 0))
        return false;
      mergeChildrenInto(this, from2, to, source ? source.children : [], openStart - 1, openEnd - 1);
      this.markDirty();
      return true;
    }
    split(from2) {
      let result = [], off = 0, detachFrom = -1, i = 0;
      for (let elt of this.children) {
        let end = off + elt.length;
        if (end > from2)
          result.push(off < from2 ? elt.split(from2 - off) : elt);
        if (detachFrom < 0 && off >= from2)
          detachFrom = i;
        off = end;
        i++;
      }
      let length = this.length - from2;
      this.length = from2;
      if (detachFrom > -1) {
        this.children.length = detachFrom;
        this.markDirty();
      }
      return new MarkView(this.mark, result, length);
    }
    domAtPos(pos) {
      return inlineDOMAtPos(this.dom, this.children, pos);
    }
    coordsAt(pos, side) {
      return coordsInChildren(this, pos, side);
    }
  };
  function textCoords(text, pos, side) {
    let length = text.nodeValue.length;
    if (pos > length)
      pos = length;
    let from2 = pos, to = pos, flatten2 = 0;
    if (pos == 0 && side < 0 || pos == length && side >= 0) {
      if (!(browser.chrome || browser.gecko)) {
        if (pos) {
          from2--;
          flatten2 = 1;
        } else {
          to++;
          flatten2 = -1;
        }
      }
    } else {
      if (side < 0)
        from2--;
      else
        to++;
    }
    let rects = textRange(text, from2, to).getClientRects();
    if (!rects.length)
      return Rect0;
    let rect = rects[(flatten2 ? flatten2 < 0 : side >= 0) ? 0 : rects.length - 1];
    if (browser.safari && !flatten2 && rect.width == 0)
      rect = Array.prototype.find.call(rects, (r) => r.width) || rect;
    return flatten2 ? flattenRect(rect, flatten2 < 0) : rect || null;
  }
  var WidgetView = class extends ContentView {
    constructor(widget, length, side) {
      super();
      this.widget = widget;
      this.length = length;
      this.side = side;
      this.prevWidget = null;
    }
    static create(widget, length, side) {
      return new (widget.customView || WidgetView)(widget, length, side);
    }
    split(from2) {
      let result = WidgetView.create(this.widget, this.length - from2, this.side);
      this.length -= from2;
      return result;
    }
    sync() {
      if (!this.dom || !this.widget.updateDOM(this.dom)) {
        if (this.dom && this.prevWidget)
          this.prevWidget.destroy(this.dom);
        this.prevWidget = null;
        this.setDOM(this.widget.toDOM(this.editorView));
        this.dom.contentEditable = "false";
      }
    }
    getSide() {
      return this.side;
    }
    merge(from2, to, source, hasStart, openStart, openEnd) {
      if (source && (!(source instanceof WidgetView) || !this.widget.compare(source.widget) || from2 > 0 && openStart <= 0 || to < this.length && openEnd <= 0))
        return false;
      this.length = from2 + (source ? source.length : 0) + (this.length - to);
      return true;
    }
    become(other) {
      if (other.length == this.length && other instanceof WidgetView && other.side == this.side) {
        if (this.widget.constructor == other.widget.constructor) {
          if (!this.widget.eq(other.widget))
            this.markDirty(true);
          if (this.dom && !this.prevWidget)
            this.prevWidget = this.widget;
          this.widget = other.widget;
          return true;
        }
      }
      return false;
    }
    ignoreMutation() {
      return true;
    }
    ignoreEvent(event) {
      return this.widget.ignoreEvent(event);
    }
    get overrideDOMText() {
      if (this.length == 0)
        return Text.empty;
      let top2 = this;
      while (top2.parent)
        top2 = top2.parent;
      let view = top2.editorView, text = view && view.state.doc, start = this.posAtStart;
      return text ? text.slice(start, start + this.length) : Text.empty;
    }
    domAtPos(pos) {
      return pos == 0 ? DOMPos.before(this.dom) : DOMPos.after(this.dom, pos == this.length);
    }
    domBoundsAround() {
      return null;
    }
    coordsAt(pos, side) {
      let rects = this.dom.getClientRects(), rect = null;
      if (!rects.length)
        return Rect0;
      for (let i = pos > 0 ? rects.length - 1 : 0; ; i += pos > 0 ? -1 : 1) {
        rect = rects[i];
        if (pos > 0 ? i == 0 : i == rects.length - 1 || rect.top < rect.bottom)
          break;
      }
      return pos == 0 && side > 0 || pos == this.length && side <= 0 ? rect : flattenRect(rect, pos == 0);
    }
    get isEditable() {
      return false;
    }
    destroy() {
      super.destroy();
      if (this.dom)
        this.widget.destroy(this.dom);
    }
  };
  var CompositionView = class extends WidgetView {
    domAtPos(pos) {
      let { topView, text } = this.widget;
      if (!topView)
        return new DOMPos(text, Math.min(pos, text.nodeValue.length));
      return scanCompositionTree(pos, 0, topView, text, (v, p) => v.domAtPos(p), (p) => new DOMPos(text, Math.min(p, text.nodeValue.length)));
    }
    sync() {
      this.setDOM(this.widget.toDOM());
    }
    localPosFromDOM(node, offset) {
      let { topView, text } = this.widget;
      if (!topView)
        return Math.min(offset, this.length);
      return posFromDOMInCompositionTree(node, offset, topView, text);
    }
    ignoreMutation() {
      return false;
    }
    get overrideDOMText() {
      return null;
    }
    coordsAt(pos, side) {
      let { topView, text } = this.widget;
      if (!topView)
        return textCoords(text, pos, side);
      return scanCompositionTree(pos, side, topView, text, (v, pos2, side2) => v.coordsAt(pos2, side2), (pos2, side2) => textCoords(text, pos2, side2));
    }
    destroy() {
      var _a2;
      super.destroy();
      (_a2 = this.widget.topView) === null || _a2 === void 0 ? void 0 : _a2.destroy();
    }
    get isEditable() {
      return true;
    }
  };
  function scanCompositionTree(pos, side, view, text, enterView, fromText) {
    if (view instanceof MarkView) {
      for (let child of view.children) {
        let hasComp = contains(child.dom, text);
        let len = hasComp ? text.nodeValue.length : child.length;
        if (pos < len || pos == len && child.getSide() <= 0)
          return hasComp ? scanCompositionTree(pos, side, child, text, enterView, fromText) : enterView(child, pos, side);
        pos -= len;
      }
      return enterView(view, view.length, -1);
    } else if (view.dom == text) {
      return fromText(pos, side);
    } else {
      return enterView(view, pos, side);
    }
  }
  function posFromDOMInCompositionTree(node, offset, view, text) {
    if (view instanceof MarkView) {
      for (let child of view.children) {
        let pos = 0, hasComp = contains(child.dom, text);
        if (contains(child.dom, node))
          return pos + (hasComp ? posFromDOMInCompositionTree(node, offset, child, text) : child.localPosFromDOM(node, offset));
        pos += hasComp ? text.nodeValue.length : child.length;
      }
    } else if (view.dom == text) {
      return Math.min(offset, text.nodeValue.length);
    }
    return view.localPosFromDOM(node, offset);
  }
  var WidgetBufferView = class extends ContentView {
    constructor(side) {
      super();
      this.side = side;
    }
    get length() {
      return 0;
    }
    merge() {
      return false;
    }
    become(other) {
      return other instanceof WidgetBufferView && other.side == this.side;
    }
    split() {
      return new WidgetBufferView(this.side);
    }
    sync() {
      if (!this.dom) {
        let dom = document.createElement("img");
        dom.className = "cm-widgetBuffer";
        dom.setAttribute("aria-hidden", "true");
        this.setDOM(dom);
      }
    }
    getSide() {
      return this.side;
    }
    domAtPos(pos) {
      return DOMPos.before(this.dom);
    }
    localPosFromDOM() {
      return 0;
    }
    domBoundsAround() {
      return null;
    }
    coordsAt(pos) {
      let imgRect = this.dom.getBoundingClientRect();
      let siblingRect = inlineSiblingRect(this, this.side > 0 ? -1 : 1);
      return siblingRect && siblingRect.top < imgRect.bottom && siblingRect.bottom > imgRect.top ? { left: imgRect.left, right: imgRect.right, top: siblingRect.top, bottom: siblingRect.bottom } : imgRect;
    }
    get overrideDOMText() {
      return Text.empty;
    }
  };
  TextView.prototype.children = WidgetView.prototype.children = WidgetBufferView.prototype.children = noChildren;
  function inlineSiblingRect(view, side) {
    let parent = view.parent, index = parent ? parent.children.indexOf(view) : -1;
    while (parent && index >= 0) {
      if (side < 0 ? index > 0 : index < parent.children.length) {
        let next = parent.children[index + side];
        if (next instanceof TextView) {
          let nextRect = next.coordsAt(side < 0 ? next.length : 0, side);
          if (nextRect)
            return nextRect;
        }
        index += side;
      } else if (parent instanceof MarkView && parent.parent) {
        index = parent.parent.children.indexOf(parent) + (side < 0 ? 0 : 1);
        parent = parent.parent;
      } else {
        let last = parent.dom.lastChild;
        if (last && last.nodeName == "BR")
          return last.getClientRects()[0];
        break;
      }
    }
    return void 0;
  }
  function inlineDOMAtPos(dom, children, pos) {
    let i = 0;
    for (let off = 0; i < children.length; i++) {
      let child = children[i], end = off + child.length;
      if (end == off && child.getSide() <= 0)
        continue;
      if (pos > off && pos < end && child.dom.parentNode == dom)
        return child.domAtPos(pos - off);
      if (pos <= off)
        break;
      off = end;
    }
    for (; i > 0; i--) {
      let before = children[i - 1].dom;
      if (before.parentNode == dom)
        return DOMPos.after(before);
    }
    return new DOMPos(dom, 0);
  }
  function joinInlineInto(parent, view, open) {
    let last, { children } = parent;
    if (open > 0 && view instanceof MarkView && children.length && (last = children[children.length - 1]) instanceof MarkView && last.mark.eq(view.mark)) {
      joinInlineInto(last, view.children[0], open - 1);
    } else {
      children.push(view);
      view.setParent(parent);
    }
    parent.length += view.length;
  }
  function coordsInChildren(view, pos, side) {
    for (let off = 0, i = 0; i < view.children.length; i++) {
      let child = view.children[i], end = off + child.length, next;
      if ((side <= 0 || end == view.length || child.getSide() > 0 ? end >= pos : end > pos) && (pos < end || i + 1 == view.children.length || (next = view.children[i + 1]).length || next.getSide() > 0)) {
        let flatten2 = 0;
        if (end == off) {
          if (child.getSide() <= 0)
            continue;
          flatten2 = side = -child.getSide();
        }
        let rect = child.coordsAt(Math.max(0, pos - off), side);
        return flatten2 && rect ? flattenRect(rect, side < 0) : rect;
      }
      off = end;
    }
    let last = view.dom.lastChild;
    if (!last)
      return view.dom.getBoundingClientRect();
    let rects = clientRectsFor(last);
    return rects[rects.length - 1] || null;
  }
  function combineAttrs(source, target) {
    for (let name2 in source) {
      if (name2 == "class" && target.class)
        target.class += " " + source.class;
      else if (name2 == "style" && target.style)
        target.style += ";" + source.style;
      else
        target[name2] = source[name2];
    }
    return target;
  }
  function attrsEq(a, b) {
    if (a == b)
      return true;
    if (!a || !b)
      return false;
    let keysA = Object.keys(a), keysB = Object.keys(b);
    if (keysA.length != keysB.length)
      return false;
    for (let key of keysA) {
      if (keysB.indexOf(key) == -1 || a[key] !== b[key])
        return false;
    }
    return true;
  }
  function updateAttrs(dom, prev, attrs) {
    if (prev) {
      for (let name2 in prev)
        if (!(attrs && name2 in attrs))
          dom.removeAttribute(name2);
    }
    if (attrs) {
      for (let name2 in attrs)
        if (!(prev && prev[name2] == attrs[name2]))
          dom.setAttribute(name2, attrs[name2]);
    }
  }
  var WidgetType = class {
    eq(_widget) {
      return false;
    }
    updateDOM(_dom) {
      return false;
    }
    compare(other) {
      return this == other || this.constructor == other.constructor && this.eq(other);
    }
    get estimatedHeight() {
      return -1;
    }
    ignoreEvent(_event) {
      return true;
    }
    get customView() {
      return null;
    }
    destroy(_dom) {
    }
  };
  var BlockType = /* @__PURE__ */ function(BlockType2) {
    BlockType2[BlockType2["Text"] = 0] = "Text";
    BlockType2[BlockType2["WidgetBefore"] = 1] = "WidgetBefore";
    BlockType2[BlockType2["WidgetAfter"] = 2] = "WidgetAfter";
    BlockType2[BlockType2["WidgetRange"] = 3] = "WidgetRange";
    return BlockType2;
  }(BlockType || (BlockType = {}));
  var Decoration = class extends RangeValue {
    constructor(startSide, endSide, widget, spec) {
      super();
      this.startSide = startSide;
      this.endSide = endSide;
      this.widget = widget;
      this.spec = spec;
    }
    get heightRelevant() {
      return false;
    }
    static mark(spec) {
      return new MarkDecoration(spec);
    }
    static widget(spec) {
      let side = spec.side || 0, block = !!spec.block;
      side += block ? side > 0 ? 3e8 : -4e8 : side > 0 ? 1e8 : -1e8;
      return new PointDecoration(spec, side, side, block, spec.widget || null, false);
    }
    static replace(spec) {
      let block = !!spec.block, startSide, endSide;
      if (spec.isBlockGap) {
        startSide = -5e8;
        endSide = 4e8;
      } else {
        let { start, end } = getInclusive(spec, block);
        startSide = (start ? block ? -3e8 : -1 : 5e8) - 1;
        endSide = (end ? block ? 2e8 : 1 : -6e8) + 1;
      }
      return new PointDecoration(spec, startSide, endSide, block, spec.widget || null, true);
    }
    static line(spec) {
      return new LineDecoration(spec);
    }
    static set(of, sort = false) {
      return RangeSet.of(of, sort);
    }
    hasHeight() {
      return this.widget ? this.widget.estimatedHeight > -1 : false;
    }
  };
  Decoration.none = RangeSet.empty;
  var MarkDecoration = class extends Decoration {
    constructor(spec) {
      let { start, end } = getInclusive(spec);
      super(start ? -1 : 5e8, end ? 1 : -6e8, null, spec);
      this.tagName = spec.tagName || "span";
      this.class = spec.class || "";
      this.attrs = spec.attributes || null;
    }
    eq(other) {
      return this == other || other instanceof MarkDecoration && this.tagName == other.tagName && this.class == other.class && attrsEq(this.attrs, other.attrs);
    }
    range(from2, to = from2) {
      if (from2 >= to)
        throw new RangeError("Mark decorations may not be empty");
      return super.range(from2, to);
    }
  };
  MarkDecoration.prototype.point = false;
  var LineDecoration = class extends Decoration {
    constructor(spec) {
      super(-2e8, -2e8, null, spec);
    }
    eq(other) {
      return other instanceof LineDecoration && attrsEq(this.spec.attributes, other.spec.attributes);
    }
    range(from2, to = from2) {
      if (to != from2)
        throw new RangeError("Line decoration ranges must be zero-length");
      return super.range(from2, to);
    }
  };
  LineDecoration.prototype.mapMode = MapMode.TrackBefore;
  LineDecoration.prototype.point = true;
  var PointDecoration = class extends Decoration {
    constructor(spec, startSide, endSide, block, widget, isReplace) {
      super(startSide, endSide, widget, spec);
      this.block = block;
      this.isReplace = isReplace;
      this.mapMode = !block ? MapMode.TrackDel : startSide <= 0 ? MapMode.TrackBefore : MapMode.TrackAfter;
    }
    get type() {
      return this.startSide < this.endSide ? BlockType.WidgetRange : this.startSide <= 0 ? BlockType.WidgetBefore : BlockType.WidgetAfter;
    }
    get heightRelevant() {
      return this.block || !!this.widget && this.widget.estimatedHeight >= 5;
    }
    eq(other) {
      return other instanceof PointDecoration && widgetsEq(this.widget, other.widget) && this.block == other.block && this.startSide == other.startSide && this.endSide == other.endSide;
    }
    range(from2, to = from2) {
      if (this.isReplace && (from2 > to || from2 == to && this.startSide > 0 && this.endSide <= 0))
        throw new RangeError("Invalid range for replacement decoration");
      if (!this.isReplace && to != from2)
        throw new RangeError("Widget decorations can only have zero-length ranges");
      return super.range(from2, to);
    }
  };
  PointDecoration.prototype.point = true;
  function getInclusive(spec, block = false) {
    let { inclusiveStart: start, inclusiveEnd: end } = spec;
    if (start == null)
      start = spec.inclusive;
    if (end == null)
      end = spec.inclusive;
    return { start: start !== null && start !== void 0 ? start : block, end: end !== null && end !== void 0 ? end : block };
  }
  function widgetsEq(a, b) {
    return a == b || !!(a && b && a.compare(b));
  }
  function addRange(from2, to, ranges, margin = 0) {
    let last = ranges.length - 1;
    if (last >= 0 && ranges[last] + margin >= from2)
      ranges[last] = Math.max(ranges[last], to);
    else
      ranges.push(from2, to);
  }
  var LineView = class extends ContentView {
    constructor() {
      super(...arguments);
      this.children = [];
      this.length = 0;
      this.prevAttrs = void 0;
      this.attrs = null;
      this.breakAfter = 0;
    }
    merge(from2, to, source, hasStart, openStart, openEnd) {
      if (source) {
        if (!(source instanceof LineView))
          return false;
        if (!this.dom)
          source.transferDOM(this);
      }
      if (hasStart)
        this.setDeco(source ? source.attrs : null);
      mergeChildrenInto(this, from2, to, source ? source.children : [], openStart, openEnd);
      return true;
    }
    split(at) {
      let end = new LineView();
      end.breakAfter = this.breakAfter;
      if (this.length == 0)
        return end;
      let { i, off } = this.childPos(at);
      if (off) {
        end.append(this.children[i].split(off), 0);
        this.children[i].merge(off, this.children[i].length, null, false, 0, 0);
        i++;
      }
      for (let j = i; j < this.children.length; j++)
        end.append(this.children[j], 0);
      while (i > 0 && this.children[i - 1].length == 0)
        this.children[--i].destroy();
      this.children.length = i;
      this.markDirty();
      this.length = at;
      return end;
    }
    transferDOM(other) {
      if (!this.dom)
        return;
      other.setDOM(this.dom);
      other.prevAttrs = this.prevAttrs === void 0 ? this.attrs : this.prevAttrs;
      this.prevAttrs = void 0;
      this.dom = null;
    }
    setDeco(attrs) {
      if (!attrsEq(this.attrs, attrs)) {
        if (this.dom) {
          this.prevAttrs = this.attrs;
          this.markDirty();
        }
        this.attrs = attrs;
      }
    }
    append(child, openStart) {
      joinInlineInto(this, child, openStart);
    }
    addLineDeco(deco) {
      let attrs = deco.spec.attributes, cls = deco.spec.class;
      if (attrs)
        this.attrs = combineAttrs(attrs, this.attrs || {});
      if (cls)
        this.attrs = combineAttrs({ class: cls }, this.attrs || {});
    }
    domAtPos(pos) {
      return inlineDOMAtPos(this.dom, this.children, pos);
    }
    reuseDOM(node) {
      if (node.nodeName == "DIV") {
        this.setDOM(node);
        this.dirty |= 4 | 2;
      }
    }
    sync(track) {
      var _a2;
      if (!this.dom) {
        this.setDOM(document.createElement("div"));
        this.dom.className = "cm-line";
        this.prevAttrs = this.attrs ? null : void 0;
      } else if (this.dirty & 4) {
        clearAttributes(this.dom);
        this.dom.className = "cm-line";
        this.prevAttrs = this.attrs ? null : void 0;
      }
      if (this.prevAttrs !== void 0) {
        updateAttrs(this.dom, this.prevAttrs, this.attrs);
        this.dom.classList.add("cm-line");
        this.prevAttrs = void 0;
      }
      super.sync(track);
      let last = this.dom.lastChild;
      while (last && ContentView.get(last) instanceof MarkView)
        last = last.lastChild;
      if (!last || !this.length || last.nodeName != "BR" && ((_a2 = ContentView.get(last)) === null || _a2 === void 0 ? void 0 : _a2.isEditable) == false && (!browser.ios || !this.children.some((ch) => ch instanceof TextView))) {
        let hack = document.createElement("BR");
        hack.cmIgnore = true;
        this.dom.appendChild(hack);
      }
    }
    measureTextSize() {
      if (this.children.length == 0 || this.length > 20)
        return null;
      let totalWidth = 0;
      for (let child of this.children) {
        if (!(child instanceof TextView))
          return null;
        let rects = clientRectsFor(child.dom);
        if (rects.length != 1)
          return null;
        totalWidth += rects[0].width;
      }
      return {
        lineHeight: this.dom.getBoundingClientRect().height,
        charWidth: totalWidth / this.length
      };
    }
    coordsAt(pos, side) {
      return coordsInChildren(this, pos, side);
    }
    become(_other) {
      return false;
    }
    get type() {
      return BlockType.Text;
    }
    static find(docView, pos) {
      for (let i = 0, off = 0; i < docView.children.length; i++) {
        let block = docView.children[i], end = off + block.length;
        if (end >= pos) {
          if (block instanceof LineView)
            return block;
          if (end > pos)
            break;
        }
        off = end + block.breakAfter;
      }
      return null;
    }
  };
  var BlockWidgetView = class extends ContentView {
    constructor(widget, length, type) {
      super();
      this.widget = widget;
      this.length = length;
      this.type = type;
      this.breakAfter = 0;
      this.prevWidget = null;
    }
    merge(from2, to, source, _takeDeco, openStart, openEnd) {
      if (source && (!(source instanceof BlockWidgetView) || !this.widget.compare(source.widget) || from2 > 0 && openStart <= 0 || to < this.length && openEnd <= 0))
        return false;
      this.length = from2 + (source ? source.length : 0) + (this.length - to);
      return true;
    }
    domAtPos(pos) {
      return pos == 0 ? DOMPos.before(this.dom) : DOMPos.after(this.dom, pos == this.length);
    }
    split(at) {
      let len = this.length - at;
      this.length = at;
      let end = new BlockWidgetView(this.widget, len, this.type);
      end.breakAfter = this.breakAfter;
      return end;
    }
    get children() {
      return noChildren;
    }
    sync() {
      if (!this.dom || !this.widget.updateDOM(this.dom)) {
        if (this.dom && this.prevWidget)
          this.prevWidget.destroy(this.dom);
        this.prevWidget = null;
        this.setDOM(this.widget.toDOM(this.editorView));
        this.dom.contentEditable = "false";
      }
    }
    get overrideDOMText() {
      return this.parent ? this.parent.view.state.doc.slice(this.posAtStart, this.posAtEnd) : Text.empty;
    }
    domBoundsAround() {
      return null;
    }
    become(other) {
      if (other instanceof BlockWidgetView && other.type == this.type && other.widget.constructor == this.widget.constructor) {
        if (!other.widget.eq(this.widget))
          this.markDirty(true);
        if (this.dom && !this.prevWidget)
          this.prevWidget = this.widget;
        this.widget = other.widget;
        this.length = other.length;
        this.breakAfter = other.breakAfter;
        return true;
      }
      return false;
    }
    ignoreMutation() {
      return true;
    }
    ignoreEvent(event) {
      return this.widget.ignoreEvent(event);
    }
    destroy() {
      super.destroy();
      if (this.dom)
        this.widget.destroy(this.dom);
    }
  };
  var ContentBuilder = class {
    constructor(doc2, pos, end, disallowBlockEffectsBelow) {
      this.doc = doc2;
      this.pos = pos;
      this.end = end;
      this.disallowBlockEffectsBelow = disallowBlockEffectsBelow;
      this.content = [];
      this.curLine = null;
      this.breakAtStart = 0;
      this.pendingBuffer = 0;
      this.atCursorPos = true;
      this.openStart = -1;
      this.openEnd = -1;
      this.text = "";
      this.textOff = 0;
      this.cursor = doc2.iter();
      this.skip = pos;
    }
    posCovered() {
      if (this.content.length == 0)
        return !this.breakAtStart && this.doc.lineAt(this.pos).from != this.pos;
      let last = this.content[this.content.length - 1];
      return !last.breakAfter && !(last instanceof BlockWidgetView && last.type == BlockType.WidgetBefore);
    }
    getLine() {
      if (!this.curLine) {
        this.content.push(this.curLine = new LineView());
        this.atCursorPos = true;
      }
      return this.curLine;
    }
    flushBuffer(active) {
      if (this.pendingBuffer) {
        this.curLine.append(wrapMarks(new WidgetBufferView(-1), active), active.length);
        this.pendingBuffer = 0;
      }
    }
    addBlockWidget(view) {
      this.flushBuffer([]);
      this.curLine = null;
      this.content.push(view);
    }
    finish(openEnd) {
      if (!openEnd)
        this.flushBuffer([]);
      else
        this.pendingBuffer = 0;
      if (!this.posCovered())
        this.getLine();
    }
    buildText(length, active, openStart) {
      while (length > 0) {
        if (this.textOff == this.text.length) {
          let { value, lineBreak, done } = this.cursor.next(this.skip);
          this.skip = 0;
          if (done)
            throw new Error("Ran out of text content when drawing inline views");
          if (lineBreak) {
            if (!this.posCovered())
              this.getLine();
            if (this.content.length)
              this.content[this.content.length - 1].breakAfter = 1;
            else
              this.breakAtStart = 1;
            this.flushBuffer([]);
            this.curLine = null;
            length--;
            continue;
          } else {
            this.text = value;
            this.textOff = 0;
          }
        }
        let take = Math.min(this.text.length - this.textOff, length, 512);
        this.flushBuffer(active.slice(0, openStart));
        this.getLine().append(wrapMarks(new TextView(this.text.slice(this.textOff, this.textOff + take)), active), openStart);
        this.atCursorPos = true;
        this.textOff += take;
        length -= take;
        openStart = 0;
      }
    }
    span(from2, to, active, openStart) {
      this.buildText(to - from2, active, openStart);
      this.pos = to;
      if (this.openStart < 0)
        this.openStart = openStart;
    }
    point(from2, to, deco, active, openStart) {
      let len = to - from2;
      if (deco instanceof PointDecoration) {
        if (deco.block) {
          let { type } = deco;
          if (type == BlockType.WidgetAfter && !this.posCovered())
            this.getLine();
          this.addBlockWidget(new BlockWidgetView(deco.widget || new NullWidget("div"), len, type));
        } else {
          let view = WidgetView.create(deco.widget || new NullWidget("span"), len, deco.startSide);
          let cursorBefore = this.atCursorPos && !view.isEditable && openStart <= active.length && (from2 < to || deco.startSide > 0);
          let cursorAfter = !view.isEditable && (from2 < to || deco.startSide <= 0);
          let line = this.getLine();
          if (this.pendingBuffer == 2 && !cursorBefore)
            this.pendingBuffer = 0;
          this.flushBuffer(active);
          if (cursorBefore) {
            line.append(wrapMarks(new WidgetBufferView(1), active), openStart);
            openStart = active.length + Math.max(0, openStart - active.length);
          }
          line.append(wrapMarks(view, active), openStart);
          this.atCursorPos = cursorAfter;
          this.pendingBuffer = !cursorAfter ? 0 : from2 < to ? 1 : 2;
        }
      } else if (this.doc.lineAt(this.pos).from == this.pos) {
        this.getLine().addLineDeco(deco);
      }
      if (len) {
        if (this.textOff + len <= this.text.length) {
          this.textOff += len;
        } else {
          this.skip += len - (this.text.length - this.textOff);
          this.text = "";
          this.textOff = 0;
        }
        this.pos = to;
      }
      if (this.openStart < 0)
        this.openStart = openStart;
    }
    filterPoint(from2, to, value, index) {
      if (index < this.disallowBlockEffectsBelow && value instanceof PointDecoration) {
        if (value.block)
          throw new RangeError("Block decorations may not be specified via plugins");
        if (to > this.doc.lineAt(this.pos).to)
          throw new RangeError("Decorations that replace line breaks may not be specified via plugins");
      }
      return true;
    }
    static build(text, from2, to, decorations2, pluginDecorationLength) {
      let builder = new ContentBuilder(text, from2, to, pluginDecorationLength);
      builder.openEnd = RangeSet.spans(decorations2, from2, to, builder);
      if (builder.openStart < 0)
        builder.openStart = builder.openEnd;
      builder.finish(builder.openEnd);
      return builder;
    }
  };
  function wrapMarks(view, active) {
    for (let mark of active)
      view = new MarkView(mark, [view], view.length);
    return view;
  }
  var NullWidget = class extends WidgetType {
    constructor(tag) {
      super();
      this.tag = tag;
    }
    eq(other) {
      return other.tag == this.tag;
    }
    toDOM() {
      return document.createElement(this.tag);
    }
    updateDOM(elt) {
      return elt.nodeName.toLowerCase() == this.tag;
    }
  };
  var none2 = [];
  var clickAddsSelectionRange = /* @__PURE__ */ Facet.define();
  var dragMovesSelection$1 = /* @__PURE__ */ Facet.define();
  var mouseSelectionStyle = /* @__PURE__ */ Facet.define();
  var exceptionSink = /* @__PURE__ */ Facet.define();
  var updateListener = /* @__PURE__ */ Facet.define();
  var inputHandler = /* @__PURE__ */ Facet.define();
  var scrollTo = /* @__PURE__ */ StateEffect.define({
    map: (range, changes) => range.map(changes)
  });
  var centerOn = /* @__PURE__ */ StateEffect.define({
    map: (range, changes) => range.map(changes)
  });
  var ScrollTarget = class {
    constructor(range, y = "nearest", x = "nearest", yMargin = 5, xMargin = 5) {
      this.range = range;
      this.y = y;
      this.x = x;
      this.yMargin = yMargin;
      this.xMargin = xMargin;
    }
    map(changes) {
      return changes.empty ? this : new ScrollTarget(this.range.map(changes), this.y, this.x, this.yMargin, this.xMargin);
    }
  };
  var scrollIntoView = /* @__PURE__ */ StateEffect.define({ map: (t2, ch) => t2.map(ch) });
  function logException(state, exception, context) {
    let handler = state.facet(exceptionSink);
    if (handler.length)
      handler[0](exception);
    else if (window.onerror)
      window.onerror(String(exception), context, void 0, void 0, exception);
    else if (context)
      console.error(context + ":", exception);
    else
      console.error(exception);
  }
  var editable = /* @__PURE__ */ Facet.define({ combine: (values2) => values2.length ? values2[0] : true });
  var PluginFieldProvider = class {
    constructor(field, get) {
      this.field = field;
      this.get = get;
    }
  };
  var PluginField = class {
    from(get) {
      return new PluginFieldProvider(this, get);
    }
    static define() {
      return new PluginField();
    }
  };
  PluginField.decorations = /* @__PURE__ */ PluginField.define();
  PluginField.atomicRanges = /* @__PURE__ */ PluginField.define();
  PluginField.scrollMargins = /* @__PURE__ */ PluginField.define();
  var nextPluginID = 0;
  var viewPlugin = /* @__PURE__ */ Facet.define();
  var ViewPlugin = class {
    constructor(id2, create, fields) {
      this.id = id2;
      this.create = create;
      this.fields = fields;
      this.extension = viewPlugin.of(this);
    }
    static define(create, spec) {
      let { eventHandlers, provide, decorations: decorations2 } = spec || {};
      let fields = [];
      if (provide)
        for (let provider of Array.isArray(provide) ? provide : [provide])
          fields.push(provider);
      if (eventHandlers)
        fields.push(domEventHandlers.from((value) => ({ plugin: value, handlers: eventHandlers })));
      if (decorations2)
        fields.push(PluginField.decorations.from(decorations2));
      return new ViewPlugin(nextPluginID++, create, fields);
    }
    static fromClass(cls, spec) {
      return ViewPlugin.define((view) => new cls(view), spec);
    }
  };
  var domEventHandlers = /* @__PURE__ */ PluginField.define();
  var PluginInstance = class {
    constructor(spec) {
      this.spec = spec;
      this.mustUpdate = null;
      this.value = null;
    }
    takeField(type, target) {
      if (this.spec) {
        for (let { field, get } of this.spec.fields)
          if (field == type)
            target.push(get(this.value));
      }
    }
    update(view) {
      if (!this.value) {
        if (this.spec) {
          try {
            this.value = this.spec.create(view);
          } catch (e) {
            logException(view.state, e, "CodeMirror plugin crashed");
            this.deactivate();
          }
        }
      } else if (this.mustUpdate) {
        let update = this.mustUpdate;
        this.mustUpdate = null;
        if (this.value.update) {
          try {
            this.value.update(update);
          } catch (e) {
            logException(update.state, e, "CodeMirror plugin crashed");
            if (this.value.destroy)
              try {
                this.value.destroy();
              } catch (_) {
              }
            this.deactivate();
          }
        }
      }
      return this;
    }
    destroy(view) {
      var _a2;
      if ((_a2 = this.value) === null || _a2 === void 0 ? void 0 : _a2.destroy) {
        try {
          this.value.destroy();
        } catch (e) {
          logException(view.state, e, "CodeMirror plugin crashed");
        }
      }
    }
    deactivate() {
      this.spec = this.value = null;
    }
  };
  var editorAttributes = /* @__PURE__ */ Facet.define();
  var contentAttributes = /* @__PURE__ */ Facet.define();
  var decorations = /* @__PURE__ */ Facet.define();
  var styleModule = /* @__PURE__ */ Facet.define();
  var ChangedRange = class {
    constructor(fromA, toA, fromB, toB) {
      this.fromA = fromA;
      this.toA = toA;
      this.fromB = fromB;
      this.toB = toB;
    }
    join(other) {
      return new ChangedRange(Math.min(this.fromA, other.fromA), Math.max(this.toA, other.toA), Math.min(this.fromB, other.fromB), Math.max(this.toB, other.toB));
    }
    addToSet(set) {
      let i = set.length, me = this;
      for (; i > 0; i--) {
        let range = set[i - 1];
        if (range.fromA > me.toA)
          continue;
        if (range.toA < me.fromA)
          break;
        me = me.join(range);
        set.splice(i - 1, 1);
      }
      set.splice(i, 0, me);
      return set;
    }
    static extendWithRanges(diff, ranges) {
      if (ranges.length == 0)
        return diff;
      let result = [];
      for (let dI = 0, rI = 0, posA = 0, posB = 0; ; dI++) {
        let next = dI == diff.length ? null : diff[dI], off = posA - posB;
        let end = next ? next.fromB : 1e9;
        while (rI < ranges.length && ranges[rI] < end) {
          let from2 = ranges[rI], to = ranges[rI + 1];
          let fromB = Math.max(posB, from2), toB = Math.min(end, to);
          if (fromB <= toB)
            new ChangedRange(fromB + off, toB + off, fromB, toB).addToSet(result);
          if (to > end)
            break;
          else
            rI += 2;
        }
        if (!next)
          return result;
        new ChangedRange(next.fromA, next.toA, next.fromB, next.toB).addToSet(result);
        posA = next.toA;
        posB = next.toB;
      }
    }
  };
  var ViewUpdate = class {
    constructor(view, state, transactions = none2) {
      this.view = view;
      this.state = state;
      this.transactions = transactions;
      this.flags = 0;
      this.startState = view.state;
      this.changes = ChangeSet.empty(this.startState.doc.length);
      for (let tr of transactions)
        this.changes = this.changes.compose(tr.changes);
      let changedRanges = [];
      this.changes.iterChangedRanges((fromA, toA, fromB, toB) => changedRanges.push(new ChangedRange(fromA, toA, fromB, toB)));
      this.changedRanges = changedRanges;
      let focus = view.hasFocus;
      if (focus != view.inputState.notifiedFocused) {
        view.inputState.notifiedFocused = focus;
        this.flags |= 1;
      }
    }
    get viewportChanged() {
      return (this.flags & 4) > 0;
    }
    get heightChanged() {
      return (this.flags & 2) > 0;
    }
    get geometryChanged() {
      return this.docChanged || (this.flags & (8 | 2)) > 0;
    }
    get focusChanged() {
      return (this.flags & 1) > 0;
    }
    get docChanged() {
      return !this.changes.empty;
    }
    get selectionSet() {
      return this.transactions.some((tr) => tr.selection);
    }
    get empty() {
      return this.flags == 0 && this.transactions.length == 0;
    }
  };
  var Direction = /* @__PURE__ */ function(Direction2) {
    Direction2[Direction2["LTR"] = 0] = "LTR";
    Direction2[Direction2["RTL"] = 1] = "RTL";
    return Direction2;
  }(Direction || (Direction = {}));
  var LTR = Direction.LTR;
  var RTL = Direction.RTL;
  function dec(str) {
    let result = [];
    for (let i = 0; i < str.length; i++)
      result.push(1 << +str[i]);
    return result;
  }
  var LowTypes = /* @__PURE__ */ dec("88888888888888888888888888888888888666888888787833333333337888888000000000000000000000000008888880000000000000000000000000088888888888888888888888888888888888887866668888088888663380888308888800000000000000000000000800000000000000000000000000000008");
  var ArabicTypes = /* @__PURE__ */ dec("4444448826627288999999999992222222222222222222222222222222222222222222222229999999999999999999994444444444644222822222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222999999949999999229989999223333333333");
  var Brackets = /* @__PURE__ */ Object.create(null);
  var BracketStack = [];
  for (let p of ["()", "[]", "{}"]) {
    let l = /* @__PURE__ */ p.charCodeAt(0), r = /* @__PURE__ */ p.charCodeAt(1);
    Brackets[l] = r;
    Brackets[r] = -l;
  }
  function charType(ch) {
    return ch <= 247 ? LowTypes[ch] : 1424 <= ch && ch <= 1524 ? 2 : 1536 <= ch && ch <= 1785 ? ArabicTypes[ch - 1536] : 1774 <= ch && ch <= 2220 ? 4 : 8192 <= ch && ch <= 8203 ? 256 : ch == 8204 ? 256 : 1;
  }
  var BidiRE = /[\u0590-\u05f4\u0600-\u06ff\u0700-\u08ac]/;
  var BidiSpan = class {
    constructor(from2, to, level) {
      this.from = from2;
      this.to = to;
      this.level = level;
    }
    get dir() {
      return this.level % 2 ? RTL : LTR;
    }
    side(end, dir) {
      return this.dir == dir == end ? this.to : this.from;
    }
    static find(order, index, level, assoc) {
      let maybe = -1;
      for (let i = 0; i < order.length; i++) {
        let span2 = order[i];
        if (span2.from <= index && span2.to >= index) {
          if (span2.level == level)
            return i;
          if (maybe < 0 || (assoc != 0 ? assoc < 0 ? span2.from < index : span2.to > index : order[maybe].level > span2.level))
            maybe = i;
        }
      }
      if (maybe < 0)
        throw new RangeError("Index out of range");
      return maybe;
    }
  };
  var types = [];
  function computeOrder(line, direction) {
    let len = line.length, outerType = direction == LTR ? 1 : 2, oppositeType = direction == LTR ? 2 : 1;
    if (!line || outerType == 1 && !BidiRE.test(line))
      return trivialOrder(len);
    for (let i = 0, prev = outerType, prevStrong = outerType; i < len; i++) {
      let type = charType(line.charCodeAt(i));
      if (type == 512)
        type = prev;
      else if (type == 8 && prevStrong == 4)
        type = 16;
      types[i] = type == 4 ? 2 : type;
      if (type & 7)
        prevStrong = type;
      prev = type;
    }
    for (let i = 0, prev = outerType, prevStrong = outerType; i < len; i++) {
      let type = types[i];
      if (type == 128) {
        if (i < len - 1 && prev == types[i + 1] && prev & 24)
          type = types[i] = prev;
        else
          types[i] = 256;
      } else if (type == 64) {
        let end = i + 1;
        while (end < len && types[end] == 64)
          end++;
        let replace = i && prev == 8 || end < len && types[end] == 8 ? prevStrong == 1 ? 1 : 8 : 256;
        for (let j = i; j < end; j++)
          types[j] = replace;
        i = end - 1;
      } else if (type == 8 && prevStrong == 1) {
        types[i] = 1;
      }
      prev = type;
      if (type & 7)
        prevStrong = type;
    }
    for (let i = 0, sI = 0, context = 0, ch, br, type; i < len; i++) {
      if (br = Brackets[ch = line.charCodeAt(i)]) {
        if (br < 0) {
          for (let sJ = sI - 3; sJ >= 0; sJ -= 3) {
            if (BracketStack[sJ + 1] == -br) {
              let flags = BracketStack[sJ + 2];
              let type2 = flags & 2 ? outerType : !(flags & 4) ? 0 : flags & 1 ? oppositeType : outerType;
              if (type2)
                types[i] = types[BracketStack[sJ]] = type2;
              sI = sJ;
              break;
            }
          }
        } else if (BracketStack.length == 189) {
          break;
        } else {
          BracketStack[sI++] = i;
          BracketStack[sI++] = ch;
          BracketStack[sI++] = context;
        }
      } else if ((type = types[i]) == 2 || type == 1) {
        let embed = type == outerType;
        context = embed ? 0 : 1;
        for (let sJ = sI - 3; sJ >= 0; sJ -= 3) {
          let cur2 = BracketStack[sJ + 2];
          if (cur2 & 2)
            break;
          if (embed) {
            BracketStack[sJ + 2] |= 2;
          } else {
            if (cur2 & 4)
              break;
            BracketStack[sJ + 2] |= 4;
          }
        }
      }
    }
    for (let i = 0; i < len; i++) {
      if (types[i] == 256) {
        let end = i + 1;
        while (end < len && types[end] == 256)
          end++;
        let beforeL = (i ? types[i - 1] : outerType) == 1;
        let afterL = (end < len ? types[end] : outerType) == 1;
        let replace = beforeL == afterL ? beforeL ? 1 : 2 : outerType;
        for (let j = i; j < end; j++)
          types[j] = replace;
        i = end - 1;
      }
    }
    let order = [];
    if (outerType == 1) {
      for (let i = 0; i < len; ) {
        let start = i, rtl = types[i++] != 1;
        while (i < len && rtl == (types[i] != 1))
          i++;
        if (rtl) {
          for (let j = i; j > start; ) {
            let end = j, l = types[--j] != 2;
            while (j > start && l == (types[j - 1] != 2))
              j--;
            order.push(new BidiSpan(j, end, l ? 2 : 1));
          }
        } else {
          order.push(new BidiSpan(start, i, 0));
        }
      }
    } else {
      for (let i = 0; i < len; ) {
        let start = i, rtl = types[i++] == 2;
        while (i < len && rtl == (types[i] == 2))
          i++;
        order.push(new BidiSpan(start, i, rtl ? 1 : 2));
      }
    }
    return order;
  }
  function trivialOrder(length) {
    return [new BidiSpan(0, length, 0)];
  }
  var movedOver = "";
  function moveVisually(line, order, dir, start, forward) {
    var _a2;
    let startIndex = start.head - line.from, spanI = -1;
    if (startIndex == 0) {
      if (!forward || !line.length)
        return null;
      if (order[0].level != dir) {
        startIndex = order[0].side(false, dir);
        spanI = 0;
      }
    } else if (startIndex == line.length) {
      if (forward)
        return null;
      let last = order[order.length - 1];
      if (last.level != dir) {
        startIndex = last.side(true, dir);
        spanI = order.length - 1;
      }
    }
    if (spanI < 0)
      spanI = BidiSpan.find(order, startIndex, (_a2 = start.bidiLevel) !== null && _a2 !== void 0 ? _a2 : -1, start.assoc);
    let span2 = order[spanI];
    if (startIndex == span2.side(forward, dir)) {
      span2 = order[spanI += forward ? 1 : -1];
      startIndex = span2.side(!forward, dir);
    }
    let indexForward = forward == (span2.dir == dir);
    let nextIndex = findClusterBreak(line.text, startIndex, indexForward);
    movedOver = line.text.slice(Math.min(startIndex, nextIndex), Math.max(startIndex, nextIndex));
    if (nextIndex != span2.side(forward, dir))
      return EditorSelection.cursor(nextIndex + line.from, indexForward ? -1 : 1, span2.level);
    let nextSpan = spanI == (forward ? order.length - 1 : 0) ? null : order[spanI + (forward ? 1 : -1)];
    if (!nextSpan && span2.level != dir)
      return EditorSelection.cursor(forward ? line.to : line.from, forward ? -1 : 1, dir);
    if (nextSpan && nextSpan.level < span2.level)
      return EditorSelection.cursor(nextSpan.side(!forward, dir) + line.from, forward ? 1 : -1, nextSpan.level);
    return EditorSelection.cursor(nextIndex + line.from, forward ? -1 : 1, span2.level);
  }
  var LineBreakPlaceholder = "\uFFFF";
  var DOMReader = class {
    constructor(points, state) {
      this.points = points;
      this.text = "";
      this.lineSeparator = state.facet(EditorState.lineSeparator);
    }
    append(text) {
      this.text += text;
    }
    lineBreak() {
      this.text += LineBreakPlaceholder;
    }
    readRange(start, end) {
      if (!start)
        return this;
      let parent = start.parentNode;
      for (let cur2 = start; ; ) {
        this.findPointBefore(parent, cur2);
        this.readNode(cur2);
        let next = cur2.nextSibling;
        if (next == end)
          break;
        let view = ContentView.get(cur2), nextView = ContentView.get(next);
        if (view && nextView ? view.breakAfter : (view ? view.breakAfter : isBlockElement(cur2)) || isBlockElement(next) && (cur2.nodeName != "BR" || cur2.cmIgnore))
          this.lineBreak();
        cur2 = next;
      }
      this.findPointBefore(parent, end);
      return this;
    }
    readTextNode(node) {
      let text = node.nodeValue;
      for (let point of this.points)
        if (point.node == node)
          point.pos = this.text.length + Math.min(point.offset, text.length);
      for (let off = 0, re = this.lineSeparator ? null : /\r\n?|\n/g; ; ) {
        let nextBreak = -1, breakSize = 1, m;
        if (this.lineSeparator) {
          nextBreak = text.indexOf(this.lineSeparator, off);
          breakSize = this.lineSeparator.length;
        } else if (m = re.exec(text)) {
          nextBreak = m.index;
          breakSize = m[0].length;
        }
        this.append(text.slice(off, nextBreak < 0 ? text.length : nextBreak));
        if (nextBreak < 0)
          break;
        this.lineBreak();
        if (breakSize > 1) {
          for (let point of this.points)
            if (point.node == node && point.pos > this.text.length)
              point.pos -= breakSize - 1;
        }
        off = nextBreak + breakSize;
      }
    }
    readNode(node) {
      if (node.cmIgnore)
        return;
      let view = ContentView.get(node);
      let fromView = view && view.overrideDOMText;
      if (fromView != null) {
        this.findPointInside(node, fromView.length);
        for (let i = fromView.iter(); !i.next().done; ) {
          if (i.lineBreak)
            this.lineBreak();
          else
            this.append(i.value);
        }
      } else if (node.nodeType == 3) {
        this.readTextNode(node);
      } else if (node.nodeName == "BR") {
        if (node.nextSibling)
          this.lineBreak();
      } else if (node.nodeType == 1) {
        this.readRange(node.firstChild, null);
      }
    }
    findPointBefore(node, next) {
      for (let point of this.points)
        if (point.node == node && node.childNodes[point.offset] == next)
          point.pos = this.text.length;
    }
    findPointInside(node, maxLen) {
      for (let point of this.points)
        if (node.nodeType == 3 ? point.node == node : node.contains(point.node))
          point.pos = this.text.length + Math.min(maxLen, point.offset);
    }
  };
  function isBlockElement(node) {
    return node.nodeType == 1 && /^(DIV|P|LI|UL|OL|BLOCKQUOTE|DD|DT|H\d|SECTION|PRE)$/.test(node.nodeName);
  }
  var DOMPoint = class {
    constructor(node, offset) {
      this.node = node;
      this.offset = offset;
      this.pos = -1;
    }
  };
  var DocView = class extends ContentView {
    constructor(view) {
      super();
      this.view = view;
      this.compositionDeco = Decoration.none;
      this.decorations = [];
      this.pluginDecorationLength = 0;
      this.minWidth = 0;
      this.minWidthFrom = 0;
      this.minWidthTo = 0;
      this.impreciseAnchor = null;
      this.impreciseHead = null;
      this.forceSelection = false;
      this.lastUpdate = Date.now();
      this.setDOM(view.contentDOM);
      this.children = [new LineView()];
      this.children[0].setParent(this);
      this.updateDeco();
      this.updateInner([new ChangedRange(0, 0, 0, view.state.doc.length)], 0);
    }
    get root() {
      return this.view.root;
    }
    get editorView() {
      return this.view;
    }
    get length() {
      return this.view.state.doc.length;
    }
    update(update) {
      let changedRanges = update.changedRanges;
      if (this.minWidth > 0 && changedRanges.length) {
        if (!changedRanges.every(({ fromA, toA }) => toA < this.minWidthFrom || fromA > this.minWidthTo)) {
          this.minWidth = this.minWidthFrom = this.minWidthTo = 0;
        } else {
          this.minWidthFrom = update.changes.mapPos(this.minWidthFrom, 1);
          this.minWidthTo = update.changes.mapPos(this.minWidthTo, 1);
        }
      }
      if (this.view.inputState.composing < 0)
        this.compositionDeco = Decoration.none;
      else if (update.transactions.length || this.dirty)
        this.compositionDeco = computeCompositionDeco(this.view, update.changes);
      if ((browser.ie || browser.chrome) && !this.compositionDeco.size && update && update.state.doc.lines != update.startState.doc.lines)
        this.forceSelection = true;
      let prevDeco = this.decorations, deco = this.updateDeco();
      let decoDiff = findChangedDeco(prevDeco, deco, update.changes);
      changedRanges = ChangedRange.extendWithRanges(changedRanges, decoDiff);
      if (this.dirty == 0 && changedRanges.length == 0) {
        return false;
      } else {
        this.updateInner(changedRanges, update.startState.doc.length);
        if (update.transactions.length)
          this.lastUpdate = Date.now();
        return true;
      }
    }
    updateInner(changes, oldLength) {
      this.view.viewState.mustMeasureContent = true;
      this.updateChildren(changes, oldLength);
      let { observer } = this.view;
      observer.ignore(() => {
        this.dom.style.height = this.view.viewState.contentHeight + "px";
        this.dom.style.minWidth = this.minWidth ? this.minWidth + "px" : "";
        let track = browser.chrome || browser.ios ? { node: observer.selectionRange.focusNode, written: false } : void 0;
        this.sync(track);
        this.dirty = 0;
        if (track && (track.written || observer.selectionRange.focusNode != track.node))
          this.forceSelection = true;
        this.dom.style.height = "";
      });
      let gaps = [];
      if (this.view.viewport.from || this.view.viewport.to < this.view.state.doc.length) {
        for (let child of this.children)
          if (child instanceof BlockWidgetView && child.widget instanceof BlockGapWidget)
            gaps.push(child.dom);
      }
      observer.updateGaps(gaps);
    }
    updateChildren(changes, oldLength) {
      let cursor = this.childCursor(oldLength);
      for (let i = changes.length - 1; ; i--) {
        let next = i >= 0 ? changes[i] : null;
        if (!next)
          break;
        let { fromA, toA, fromB, toB } = next;
        let { content: content2, breakAtStart, openStart, openEnd } = ContentBuilder.build(this.view.state.doc, fromB, toB, this.decorations, this.pluginDecorationLength);
        let { i: toI, off: toOff } = cursor.findPos(toA, 1);
        let { i: fromI, off: fromOff } = cursor.findPos(fromA, -1);
        replaceRange(this, fromI, fromOff, toI, toOff, content2, breakAtStart, openStart, openEnd);
      }
    }
    updateSelection(mustRead = false, fromPointer = false) {
      if (mustRead)
        this.view.observer.readSelectionRange();
      if (!(fromPointer || this.mayControlSelection()) || browser.ios && this.view.inputState.rapidCompositionStart)
        return;
      let force = this.forceSelection;
      this.forceSelection = false;
      let main = this.view.state.selection.main;
      let anchor = this.domAtPos(main.anchor);
      let head = main.empty ? anchor : this.domAtPos(main.head);
      if (browser.gecko && main.empty && betweenUneditable(anchor)) {
        let dummy = document.createTextNode("");
        this.view.observer.ignore(() => anchor.node.insertBefore(dummy, anchor.node.childNodes[anchor.offset] || null));
        anchor = head = new DOMPos(dummy, 0);
        force = true;
      }
      let domSel = this.view.observer.selectionRange;
      if (force || !domSel.focusNode || !isEquivalentPosition(anchor.node, anchor.offset, domSel.anchorNode, domSel.anchorOffset) || !isEquivalentPosition(head.node, head.offset, domSel.focusNode, domSel.focusOffset)) {
        this.view.observer.ignore(() => {
          if (browser.android && browser.chrome && this.dom.contains(domSel.focusNode) && inUneditable(domSel.focusNode, this.dom)) {
            this.dom.blur();
            this.dom.focus({ preventScroll: true });
          }
          let rawSel = getSelection(this.root);
          if (main.empty) {
            if (browser.gecko) {
              let nextTo = nextToUneditable(anchor.node, anchor.offset);
              if (nextTo && nextTo != (1 | 2)) {
                let text = nearbyTextNode(anchor.node, anchor.offset, nextTo == 1 ? 1 : -1);
                if (text)
                  anchor = new DOMPos(text, nextTo == 1 ? 0 : text.nodeValue.length);
              }
            }
            rawSel.collapse(anchor.node, anchor.offset);
            if (main.bidiLevel != null && domSel.cursorBidiLevel != null)
              domSel.cursorBidiLevel = main.bidiLevel;
          } else if (rawSel.extend) {
            rawSel.collapse(anchor.node, anchor.offset);
            rawSel.extend(head.node, head.offset);
          } else {
            let range = document.createRange();
            if (main.anchor > main.head)
              [anchor, head] = [head, anchor];
            range.setEnd(head.node, head.offset);
            range.setStart(anchor.node, anchor.offset);
            rawSel.removeAllRanges();
            rawSel.addRange(range);
          }
        });
        this.view.observer.setSelectionRange(anchor, head);
      }
      this.impreciseAnchor = anchor.precise ? null : new DOMPos(domSel.anchorNode, domSel.anchorOffset);
      this.impreciseHead = head.precise ? null : new DOMPos(domSel.focusNode, domSel.focusOffset);
    }
    enforceCursorAssoc() {
      if (this.compositionDeco.size)
        return;
      let cursor = this.view.state.selection.main;
      let sel = getSelection(this.root);
      if (!cursor.empty || !cursor.assoc || !sel.modify)
        return;
      let line = LineView.find(this, cursor.head);
      if (!line)
        return;
      let lineStart = line.posAtStart;
      if (cursor.head == lineStart || cursor.head == lineStart + line.length)
        return;
      let before = this.coordsAt(cursor.head, -1), after = this.coordsAt(cursor.head, 1);
      if (!before || !after || before.bottom > after.top)
        return;
      let dom = this.domAtPos(cursor.head + cursor.assoc);
      sel.collapse(dom.node, dom.offset);
      sel.modify("move", cursor.assoc < 0 ? "forward" : "backward", "lineboundary");
    }
    mayControlSelection() {
      return this.view.state.facet(editable) ? this.root.activeElement == this.dom : hasSelection(this.dom, this.view.observer.selectionRange);
    }
    nearest(dom) {
      for (let cur2 = dom; cur2; ) {
        let domView = ContentView.get(cur2);
        if (domView && domView.rootView == this)
          return domView;
        cur2 = cur2.parentNode;
      }
      return null;
    }
    posFromDOM(node, offset) {
      let view = this.nearest(node);
      if (!view)
        throw new RangeError("Trying to find position for a DOM position outside of the document");
      return view.localPosFromDOM(node, offset) + view.posAtStart;
    }
    domAtPos(pos) {
      let { i, off } = this.childCursor().findPos(pos, -1);
      for (; i < this.children.length - 1; ) {
        let child = this.children[i];
        if (off < child.length || child instanceof LineView)
          break;
        i++;
        off = 0;
      }
      return this.children[i].domAtPos(off);
    }
    coordsAt(pos, side) {
      for (let off = this.length, i = this.children.length - 1; ; i--) {
        let child = this.children[i], start = off - child.breakAfter - child.length;
        if (pos > start || pos == start && child.type != BlockType.WidgetBefore && child.type != BlockType.WidgetAfter && (!i || side == 2 || this.children[i - 1].breakAfter || this.children[i - 1].type == BlockType.WidgetBefore && side > -2))
          return child.coordsAt(pos - start, side);
        off = start;
      }
    }
    measureVisibleLineHeights() {
      let result = [], { from: from2, to } = this.view.viewState.viewport;
      let contentWidth = this.view.contentDOM.clientWidth;
      let isWider = contentWidth > Math.max(this.view.scrollDOM.clientWidth, this.minWidth) + 1;
      let widest = -1;
      for (let pos = 0, i = 0; i < this.children.length; i++) {
        let child = this.children[i], end = pos + child.length;
        if (end > to)
          break;
        if (pos >= from2) {
          let childRect = child.dom.getBoundingClientRect();
          result.push(childRect.height);
          if (isWider) {
            let last = child.dom.lastChild;
            let rects = last ? clientRectsFor(last) : [];
            if (rects.length) {
              let rect = rects[rects.length - 1];
              let width = this.view.textDirection == Direction.LTR ? rect.right - childRect.left : childRect.right - rect.left;
              if (width > widest) {
                widest = width;
                this.minWidth = contentWidth;
                this.minWidthFrom = pos;
                this.minWidthTo = end;
              }
            }
          }
        }
        pos = end + child.breakAfter;
      }
      return result;
    }
    measureTextSize() {
      for (let child of this.children) {
        if (child instanceof LineView) {
          let measure = child.measureTextSize();
          if (measure)
            return measure;
        }
      }
      let dummy = document.createElement("div"), lineHeight, charWidth;
      dummy.className = "cm-line";
      dummy.textContent = "abc def ghi jkl mno pqr stu";
      this.view.observer.ignore(() => {
        this.dom.appendChild(dummy);
        let rect = clientRectsFor(dummy.firstChild)[0];
        lineHeight = dummy.getBoundingClientRect().height;
        charWidth = rect ? rect.width / 27 : 7;
        dummy.remove();
      });
      return { lineHeight, charWidth };
    }
    childCursor(pos = this.length) {
      let i = this.children.length;
      if (i)
        pos -= this.children[--i].length;
      return new ChildCursor(this.children, pos, i);
    }
    computeBlockGapDeco() {
      let deco = [], vs = this.view.viewState;
      for (let pos = 0, i = 0; ; i++) {
        let next = i == vs.viewports.length ? null : vs.viewports[i];
        let end = next ? next.from - 1 : this.length;
        if (end > pos) {
          let height = vs.lineBlockAt(end).bottom - vs.lineBlockAt(pos).top;
          deco.push(Decoration.replace({
            widget: new BlockGapWidget(height),
            block: true,
            inclusive: true,
            isBlockGap: true
          }).range(pos, end));
        }
        if (!next)
          break;
        pos = next.to + 1;
      }
      return Decoration.set(deco);
    }
    updateDeco() {
      let pluginDecorations = this.view.pluginField(PluginField.decorations);
      this.pluginDecorationLength = pluginDecorations.length;
      return this.decorations = [
        ...pluginDecorations,
        ...this.view.state.facet(decorations),
        this.compositionDeco,
        this.computeBlockGapDeco(),
        this.view.viewState.lineGapDeco
      ];
    }
    scrollIntoView(target) {
      let { range } = target;
      let rect = this.coordsAt(range.head, range.empty ? range.assoc : range.head > range.anchor ? -1 : 1), other;
      if (!rect)
        return;
      if (!range.empty && (other = this.coordsAt(range.anchor, range.anchor > range.head ? -1 : 1)))
        rect = {
          left: Math.min(rect.left, other.left),
          top: Math.min(rect.top, other.top),
          right: Math.max(rect.right, other.right),
          bottom: Math.max(rect.bottom, other.bottom)
        };
      let mLeft = 0, mRight = 0, mTop = 0, mBottom = 0;
      for (let margins of this.view.pluginField(PluginField.scrollMargins))
        if (margins) {
          let { left, right, top: top2, bottom } = margins;
          if (left != null)
            mLeft = Math.max(mLeft, left);
          if (right != null)
            mRight = Math.max(mRight, right);
          if (top2 != null)
            mTop = Math.max(mTop, top2);
          if (bottom != null)
            mBottom = Math.max(mBottom, bottom);
        }
      let targetRect = {
        left: rect.left - mLeft,
        top: rect.top - mTop,
        right: rect.right + mRight,
        bottom: rect.bottom + mBottom
      };
      scrollRectIntoView(this.view.scrollDOM, targetRect, range.head < range.anchor ? -1 : 1, target.x, target.y, target.xMargin, target.yMargin, this.view.textDirection == Direction.LTR);
    }
  };
  function betweenUneditable(pos) {
    return pos.node.nodeType == 1 && pos.node.firstChild && (pos.offset == 0 || pos.node.childNodes[pos.offset - 1].contentEditable == "false") && (pos.offset == pos.node.childNodes.length || pos.node.childNodes[pos.offset].contentEditable == "false");
  }
  var BlockGapWidget = class extends WidgetType {
    constructor(height) {
      super();
      this.height = height;
    }
    toDOM() {
      let elt = document.createElement("div");
      this.updateDOM(elt);
      return elt;
    }
    eq(other) {
      return other.height == this.height;
    }
    updateDOM(elt) {
      elt.style.height = this.height + "px";
      return true;
    }
    get estimatedHeight() {
      return this.height;
    }
  };
  function compositionSurroundingNode(view) {
    let sel = view.observer.selectionRange;
    let textNode = sel.focusNode && nearbyTextNode(sel.focusNode, sel.focusOffset, 0);
    if (!textNode)
      return null;
    let cView = view.docView.nearest(textNode);
    if (!cView)
      return null;
    if (cView instanceof LineView) {
      let topNode = textNode;
      while (topNode.parentNode != cView.dom)
        topNode = topNode.parentNode;
      let prev = topNode.previousSibling;
      while (prev && !ContentView.get(prev))
        prev = prev.previousSibling;
      let pos = prev ? ContentView.get(prev).posAtEnd : cView.posAtStart;
      return { from: pos, to: pos, node: topNode, text: textNode };
    } else {
      for (; ; ) {
        let { parent } = cView;
        if (!parent)
          return null;
        if (parent instanceof LineView)
          break;
        cView = parent;
      }
      let from2 = cView.posAtStart;
      return { from: from2, to: from2 + cView.length, node: cView.dom, text: textNode };
    }
  }
  function computeCompositionDeco(view, changes) {
    let surrounding = compositionSurroundingNode(view);
    if (!surrounding)
      return Decoration.none;
    let { from: from2, to, node, text: textNode } = surrounding;
    let newFrom = changes.mapPos(from2, 1), newTo = Math.max(newFrom, changes.mapPos(to, -1));
    let { state } = view, text = node.nodeType == 3 ? node.nodeValue : new DOMReader([], state).readRange(node.firstChild, null).text;
    if (newTo - newFrom < text.length) {
      if (state.doc.sliceString(newFrom, Math.min(state.doc.length, newFrom + text.length), LineBreakPlaceholder) == text)
        newTo = newFrom + text.length;
      else if (state.doc.sliceString(Math.max(0, newTo - text.length), newTo, LineBreakPlaceholder) == text)
        newFrom = newTo - text.length;
      else
        return Decoration.none;
    } else if (state.doc.sliceString(newFrom, newTo, LineBreakPlaceholder) != text) {
      return Decoration.none;
    }
    let topView = ContentView.get(node);
    if (topView instanceof CompositionView)
      topView = topView.widget.topView;
    else if (topView)
      topView.parent = null;
    return Decoration.set(Decoration.replace({ widget: new CompositionWidget(node, textNode, topView) }).range(newFrom, newTo));
  }
  var CompositionWidget = class extends WidgetType {
    constructor(top2, text, topView) {
      super();
      this.top = top2;
      this.text = text;
      this.topView = topView;
    }
    eq(other) {
      return this.top == other.top && this.text == other.text;
    }
    toDOM() {
      return this.top;
    }
    ignoreEvent() {
      return false;
    }
    get customView() {
      return CompositionView;
    }
  };
  function nearbyTextNode(node, offset, side) {
    for (; ; ) {
      if (node.nodeType == 3)
        return node;
      if (node.nodeType == 1 && offset > 0 && side <= 0) {
        node = node.childNodes[offset - 1];
        offset = maxOffset(node);
      } else if (node.nodeType == 1 && offset < node.childNodes.length && side >= 0) {
        node = node.childNodes[offset];
        offset = 0;
      } else {
        return null;
      }
    }
  }
  function nextToUneditable(node, offset) {
    if (node.nodeType != 1)
      return 0;
    return (offset && node.childNodes[offset - 1].contentEditable == "false" ? 1 : 0) | (offset < node.childNodes.length && node.childNodes[offset].contentEditable == "false" ? 2 : 0);
  }
  var DecorationComparator$1 = class {
    constructor() {
      this.changes = [];
    }
    compareRange(from2, to) {
      addRange(from2, to, this.changes);
    }
    comparePoint(from2, to) {
      addRange(from2, to, this.changes);
    }
  };
  function findChangedDeco(a, b, diff) {
    let comp = new DecorationComparator$1();
    RangeSet.compare(a, b, diff, comp);
    return comp.changes;
  }
  function inUneditable(node, inside2) {
    for (let cur2 = node; cur2 && cur2 != inside2; cur2 = cur2.assignedSlot || cur2.parentNode) {
      if (cur2.nodeType == 1 && cur2.contentEditable == "false") {
        return true;
      }
    }
    return false;
  }
  function groupAt(state, pos, bias = 1) {
    let categorize = state.charCategorizer(pos);
    let line = state.doc.lineAt(pos), linePos = pos - line.from;
    if (line.length == 0)
      return EditorSelection.cursor(pos);
    if (linePos == 0)
      bias = 1;
    else if (linePos == line.length)
      bias = -1;
    let from2 = linePos, to = linePos;
    if (bias < 0)
      from2 = findClusterBreak(line.text, linePos, false);
    else
      to = findClusterBreak(line.text, linePos);
    let cat = categorize(line.text.slice(from2, to));
    while (from2 > 0) {
      let prev = findClusterBreak(line.text, from2, false);
      if (categorize(line.text.slice(prev, from2)) != cat)
        break;
      from2 = prev;
    }
    while (to < line.length) {
      let next = findClusterBreak(line.text, to);
      if (categorize(line.text.slice(to, next)) != cat)
        break;
      to = next;
    }
    return EditorSelection.range(from2 + line.from, to + line.from);
  }
  function getdx(x, rect) {
    return rect.left > x ? rect.left - x : Math.max(0, x - rect.right);
  }
  function getdy(y, rect) {
    return rect.top > y ? rect.top - y : Math.max(0, y - rect.bottom);
  }
  function yOverlap(a, b) {
    return a.top < b.bottom - 1 && a.bottom > b.top + 1;
  }
  function upTop(rect, top2) {
    return top2 < rect.top ? { top: top2, left: rect.left, right: rect.right, bottom: rect.bottom } : rect;
  }
  function upBot(rect, bottom) {
    return bottom > rect.bottom ? { top: rect.top, left: rect.left, right: rect.right, bottom } : rect;
  }
  function domPosAtCoords(parent, x, y) {
    let closest, closestRect, closestX, closestY;
    let above, below, aboveRect, belowRect;
    for (let child = parent.firstChild; child; child = child.nextSibling) {
      let rects = clientRectsFor(child);
      for (let i = 0; i < rects.length; i++) {
        let rect = rects[i];
        if (closestRect && yOverlap(closestRect, rect))
          rect = upTop(upBot(rect, closestRect.bottom), closestRect.top);
        let dx = getdx(x, rect), dy = getdy(y, rect);
        if (dx == 0 && dy == 0)
          return child.nodeType == 3 ? domPosInText(child, x, y) : domPosAtCoords(child, x, y);
        if (!closest || closestY > dy || closestY == dy && closestX > dx) {
          closest = child;
          closestRect = rect;
          closestX = dx;
          closestY = dy;
        }
        if (dx == 0) {
          if (y > rect.bottom && (!aboveRect || aboveRect.bottom < rect.bottom)) {
            above = child;
            aboveRect = rect;
          } else if (y < rect.top && (!belowRect || belowRect.top > rect.top)) {
            below = child;
            belowRect = rect;
          }
        } else if (aboveRect && yOverlap(aboveRect, rect)) {
          aboveRect = upBot(aboveRect, rect.bottom);
        } else if (belowRect && yOverlap(belowRect, rect)) {
          belowRect = upTop(belowRect, rect.top);
        }
      }
    }
    if (aboveRect && aboveRect.bottom >= y) {
      closest = above;
      closestRect = aboveRect;
    } else if (belowRect && belowRect.top <= y) {
      closest = below;
      closestRect = belowRect;
    }
    if (!closest)
      return { node: parent, offset: 0 };
    let clipX = Math.max(closestRect.left, Math.min(closestRect.right, x));
    if (closest.nodeType == 3)
      return domPosInText(closest, clipX, y);
    if (!closestX && closest.contentEditable == "true")
      return domPosAtCoords(closest, clipX, y);
    let offset = Array.prototype.indexOf.call(parent.childNodes, closest) + (x >= (closestRect.left + closestRect.right) / 2 ? 1 : 0);
    return { node: parent, offset };
  }
  function domPosInText(node, x, y) {
    let len = node.nodeValue.length;
    let closestOffset = -1, closestDY = 1e9, generalSide = 0;
    for (let i = 0; i < len; i++) {
      let rects = textRange(node, i, i + 1).getClientRects();
      for (let j = 0; j < rects.length; j++) {
        let rect = rects[j];
        if (rect.top == rect.bottom)
          continue;
        if (!generalSide)
          generalSide = x - rect.left;
        let dy = (rect.top > y ? rect.top - y : y - rect.bottom) - 1;
        if (rect.left - 1 <= x && rect.right + 1 >= x && dy < closestDY) {
          let right = x >= (rect.left + rect.right) / 2, after = right;
          if (browser.chrome || browser.gecko) {
            let rectBefore = textRange(node, i).getBoundingClientRect();
            if (rectBefore.left == rect.right)
              after = !right;
          }
          if (dy <= 0)
            return { node, offset: i + (after ? 1 : 0) };
          closestOffset = i + (after ? 1 : 0);
          closestDY = dy;
        }
      }
    }
    return { node, offset: closestOffset > -1 ? closestOffset : generalSide > 0 ? node.nodeValue.length : 0 };
  }
  function posAtCoords(view, { x, y }, precise, bias = -1) {
    var _a2;
    let content2 = view.contentDOM.getBoundingClientRect(), docTop = content2.top + view.viewState.paddingTop;
    let block, { docHeight } = view.viewState;
    let yOffset = y - docTop;
    if (yOffset < 0)
      return 0;
    if (yOffset > docHeight)
      return view.state.doc.length;
    for (let halfLine = view.defaultLineHeight / 2, bounced = false; ; ) {
      block = view.elementAtHeight(yOffset);
      if (block.type == BlockType.Text)
        break;
      for (; ; ) {
        yOffset = bias > 0 ? block.bottom + halfLine : block.top - halfLine;
        if (yOffset >= 0 && yOffset <= docHeight)
          break;
        if (bounced)
          return precise ? null : 0;
        bounced = true;
        bias = -bias;
      }
    }
    y = docTop + yOffset;
    let lineStart = block.from;
    if (lineStart < view.viewport.from)
      return view.viewport.from == 0 ? 0 : precise ? null : posAtCoordsImprecise(view, content2, block, x, y);
    if (lineStart > view.viewport.to)
      return view.viewport.to == view.state.doc.length ? view.state.doc.length : precise ? null : posAtCoordsImprecise(view, content2, block, x, y);
    let doc2 = view.dom.ownerDocument;
    let root = view.root.elementFromPoint ? view.root : doc2;
    let element = root.elementFromPoint(x, y);
    if (element && !view.contentDOM.contains(element))
      element = null;
    if (!element) {
      x = Math.max(content2.left + 1, Math.min(content2.right - 1, x));
      element = root.elementFromPoint(x, y);
      if (element && !view.contentDOM.contains(element))
        element = null;
    }
    let node, offset = -1;
    if (element && ((_a2 = view.docView.nearest(element)) === null || _a2 === void 0 ? void 0 : _a2.isEditable) != false) {
      if (doc2.caretPositionFromPoint) {
        let pos = doc2.caretPositionFromPoint(x, y);
        if (pos)
          ({ offsetNode: node, offset } = pos);
      } else if (doc2.caretRangeFromPoint) {
        let range = doc2.caretRangeFromPoint(x, y);
        if (range) {
          ({ startContainer: node, startOffset: offset } = range);
          if (browser.safari && isSuspiciousCaretResult(node, offset, x))
            node = void 0;
        }
      }
    }
    if (!node || !view.docView.dom.contains(node)) {
      let line = LineView.find(view.docView, lineStart);
      if (!line)
        return yOffset > block.top + block.height / 2 ? block.to : block.from;
      ({ node, offset } = domPosAtCoords(line.dom, x, y));
    }
    return view.docView.posFromDOM(node, offset);
  }
  function posAtCoordsImprecise(view, contentRect, block, x, y) {
    let into = Math.round((x - contentRect.left) * view.defaultCharacterWidth);
    if (view.lineWrapping && block.height > view.defaultLineHeight * 1.5) {
      let line = Math.floor((y - block.top) / view.defaultLineHeight);
      into += line * view.viewState.heightOracle.lineLength;
    }
    let content2 = view.state.sliceDoc(block.from, block.to);
    return block.from + findColumn(content2, into, view.state.tabSize);
  }
  function isSuspiciousCaretResult(node, offset, x) {
    let len;
    if (node.nodeType != 3 || offset != (len = node.nodeValue.length))
      return false;
    for (let next = node.nextSibling; next; next = next.nextSibling)
      if (next.nodeType != 1 || next.nodeName != "BR")
        return false;
    return textRange(node, len - 1, len).getBoundingClientRect().left > x;
  }
  function moveToLineBoundary(view, start, forward, includeWrap) {
    let line = view.state.doc.lineAt(start.head);
    let coords = !includeWrap || !view.lineWrapping ? null : view.coordsAtPos(start.assoc < 0 && start.head > line.from ? start.head - 1 : start.head);
    if (coords) {
      let editorRect = view.dom.getBoundingClientRect();
      let pos = view.posAtCoords({
        x: forward == (view.textDirection == Direction.LTR) ? editorRect.right - 1 : editorRect.left + 1,
        y: (coords.top + coords.bottom) / 2
      });
      if (pos != null)
        return EditorSelection.cursor(pos, forward ? -1 : 1);
    }
    let lineView = LineView.find(view.docView, start.head);
    let end = lineView ? forward ? lineView.posAtEnd : lineView.posAtStart : forward ? line.to : line.from;
    return EditorSelection.cursor(end, forward ? -1 : 1);
  }
  function moveByChar(view, start, forward, by) {
    let line = view.state.doc.lineAt(start.head), spans = view.bidiSpans(line);
    for (let cur2 = start, check = null; ; ) {
      let next = moveVisually(line, spans, view.textDirection, cur2, forward), char = movedOver;
      if (!next) {
        if (line.number == (forward ? view.state.doc.lines : 1))
          return cur2;
        char = "\n";
        line = view.state.doc.line(line.number + (forward ? 1 : -1));
        spans = view.bidiSpans(line);
        next = EditorSelection.cursor(forward ? line.from : line.to);
      }
      if (!check) {
        if (!by)
          return next;
        check = by(char);
      } else if (!check(char)) {
        return cur2;
      }
      cur2 = next;
    }
  }
  function byGroup(view, pos, start) {
    let categorize = view.state.charCategorizer(pos);
    let cat = categorize(start);
    return (next) => {
      let nextCat = categorize(next);
      if (cat == CharCategory.Space)
        cat = nextCat;
      return cat == nextCat;
    };
  }
  function moveVertically(view, start, forward, distance) {
    let startPos = start.head, dir = forward ? 1 : -1;
    if (startPos == (forward ? view.state.doc.length : 0))
      return EditorSelection.cursor(startPos, start.assoc);
    let goal = start.goalColumn, startY;
    let rect = view.contentDOM.getBoundingClientRect();
    let startCoords = view.coordsAtPos(startPos), docTop = view.documentTop;
    if (startCoords) {
      if (goal == null)
        goal = startCoords.left - rect.left;
      startY = dir < 0 ? startCoords.top : startCoords.bottom;
    } else {
      let line = view.viewState.lineBlockAt(startPos - docTop);
      if (goal == null)
        goal = Math.min(rect.right - rect.left, view.defaultCharacterWidth * (startPos - line.from));
      startY = (dir < 0 ? line.top : line.bottom) + docTop;
    }
    let resolvedGoal = rect.left + goal;
    let dist = distance !== null && distance !== void 0 ? distance : view.defaultLineHeight >> 1;
    for (let extra = 0; ; extra += 10) {
      let curY = startY + (dist + extra) * dir;
      let pos = posAtCoords(view, { x: resolvedGoal, y: curY }, false, dir);
      if (curY < rect.top || curY > rect.bottom || (dir < 0 ? pos < startPos : pos > startPos))
        return EditorSelection.cursor(pos, start.assoc, void 0, goal);
    }
  }
  function skipAtoms(view, oldPos, pos) {
    let atoms = view.pluginField(PluginField.atomicRanges);
    for (; ; ) {
      let moved = false;
      for (let set of atoms) {
        set.between(pos.from - 1, pos.from + 1, (from2, to, value) => {
          if (pos.from > from2 && pos.from < to) {
            pos = oldPos.from > pos.from ? EditorSelection.cursor(from2, 1) : EditorSelection.cursor(to, -1);
            moved = true;
          }
        });
      }
      if (!moved)
        return pos;
    }
  }
  var InputState = class {
    constructor(view) {
      this.lastKeyCode = 0;
      this.lastKeyTime = 0;
      this.pendingIOSKey = void 0;
      this.lastSelectionOrigin = null;
      this.lastSelectionTime = 0;
      this.lastEscPress = 0;
      this.lastContextMenu = 0;
      this.scrollHandlers = [];
      this.registeredEvents = [];
      this.customHandlers = [];
      this.composing = -1;
      this.compositionFirstChange = null;
      this.compositionEndedAt = 0;
      this.rapidCompositionStart = false;
      this.mouseSelection = null;
      for (let type in handlers) {
        let handler = handlers[type];
        view.contentDOM.addEventListener(type, (event) => {
          if (!eventBelongsToEditor(view, event) || this.ignoreDuringComposition(event))
            return;
          if (type == "keydown" && this.keydown(view, event))
            return;
          if (this.mustFlushObserver(event))
            view.observer.forceFlush();
          if (this.runCustomHandlers(type, view, event))
            event.preventDefault();
          else
            handler(view, event);
        });
        this.registeredEvents.push(type);
      }
      this.notifiedFocused = view.hasFocus;
      this.ensureHandlers(view);
      if (browser.safari)
        view.contentDOM.addEventListener("input", () => null);
    }
    setSelectionOrigin(origin) {
      this.lastSelectionOrigin = origin;
      this.lastSelectionTime = Date.now();
    }
    ensureHandlers(view) {
      let handlers2 = this.customHandlers = view.pluginField(domEventHandlers);
      for (let set of handlers2) {
        for (let type in set.handlers)
          if (this.registeredEvents.indexOf(type) < 0 && type != "scroll") {
            this.registeredEvents.push(type);
            view.contentDOM.addEventListener(type, (event) => {
              if (!eventBelongsToEditor(view, event))
                return;
              if (this.runCustomHandlers(type, view, event))
                event.preventDefault();
            });
          }
      }
    }
    runCustomHandlers(type, view, event) {
      for (let set of this.customHandlers) {
        let handler = set.handlers[type];
        if (handler) {
          try {
            if (handler.call(set.plugin, event, view) || event.defaultPrevented)
              return true;
          } catch (e) {
            logException(view.state, e);
          }
        }
      }
      return false;
    }
    runScrollHandlers(view, event) {
      for (let set of this.customHandlers) {
        let handler = set.handlers.scroll;
        if (handler) {
          try {
            handler.call(set.plugin, event, view);
          } catch (e) {
            logException(view.state, e);
          }
        }
      }
    }
    keydown(view, event) {
      this.lastKeyCode = event.keyCode;
      this.lastKeyTime = Date.now();
      if (event.keyCode == 9 && Date.now() < this.lastEscPress + 2e3)
        return true;
      if (browser.android && browser.chrome && !event.synthetic && (event.keyCode == 13 || event.keyCode == 8)) {
        view.observer.delayAndroidKey(event.key, event.keyCode);
        return true;
      }
      let pending;
      if (browser.ios && (pending = PendingKeys.find((key) => key.keyCode == event.keyCode)) && !(event.ctrlKey || event.altKey || event.metaKey) && !event.synthetic) {
        this.pendingIOSKey = pending;
        setTimeout(() => this.flushIOSKey(view), 250);
        return true;
      }
      return false;
    }
    flushIOSKey(view) {
      let key = this.pendingIOSKey;
      if (!key)
        return false;
      this.pendingIOSKey = void 0;
      return dispatchKey(view.contentDOM, key.key, key.keyCode);
    }
    ignoreDuringComposition(event) {
      if (!/^key/.test(event.type))
        return false;
      if (this.composing > 0)
        return true;
      if (browser.safari && Date.now() - this.compositionEndedAt < 500) {
        this.compositionEndedAt = 0;
        return true;
      }
      return false;
    }
    mustFlushObserver(event) {
      return event.type == "keydown" && event.keyCode != 229 || event.type == "compositionend" && !browser.ios;
    }
    startMouseSelection(mouseSelection) {
      if (this.mouseSelection)
        this.mouseSelection.destroy();
      this.mouseSelection = mouseSelection;
    }
    update(update) {
      if (this.mouseSelection)
        this.mouseSelection.update(update);
      if (update.transactions.length)
        this.lastKeyCode = this.lastSelectionTime = 0;
    }
    destroy() {
      if (this.mouseSelection)
        this.mouseSelection.destroy();
    }
  };
  var PendingKeys = [
    { key: "Backspace", keyCode: 8, inputType: "deleteContentBackward" },
    { key: "Enter", keyCode: 13, inputType: "insertParagraph" },
    { key: "Delete", keyCode: 46, inputType: "deleteContentForward" }
  ];
  var modifierCodes = [16, 17, 18, 20, 91, 92, 224, 225];
  var MouseSelection = class {
    constructor(view, startEvent, style, mustSelect) {
      this.view = view;
      this.style = style;
      this.mustSelect = mustSelect;
      this.lastEvent = startEvent;
      let doc2 = view.contentDOM.ownerDocument;
      doc2.addEventListener("mousemove", this.move = this.move.bind(this));
      doc2.addEventListener("mouseup", this.up = this.up.bind(this));
      this.extend = startEvent.shiftKey;
      this.multiple = view.state.facet(EditorState.allowMultipleSelections) && addsSelectionRange(view, startEvent);
      this.dragMove = dragMovesSelection(view, startEvent);
      this.dragging = isInPrimarySelection(view, startEvent) && getClickType(startEvent) == 1 ? null : false;
      if (this.dragging === false) {
        startEvent.preventDefault();
        this.select(startEvent);
      }
    }
    move(event) {
      if (event.buttons == 0)
        return this.destroy();
      if (this.dragging !== false)
        return;
      this.select(this.lastEvent = event);
    }
    up(event) {
      if (this.dragging == null)
        this.select(this.lastEvent);
      if (!this.dragging)
        event.preventDefault();
      this.destroy();
    }
    destroy() {
      let doc2 = this.view.contentDOM.ownerDocument;
      doc2.removeEventListener("mousemove", this.move);
      doc2.removeEventListener("mouseup", this.up);
      this.view.inputState.mouseSelection = null;
    }
    select(event) {
      let selection = this.style.get(event, this.extend, this.multiple);
      if (this.mustSelect || !selection.eq(this.view.state.selection) || selection.main.assoc != this.view.state.selection.main.assoc)
        this.view.dispatch({
          selection,
          userEvent: "select.pointer",
          scrollIntoView: true
        });
      this.mustSelect = false;
    }
    update(update) {
      if (update.docChanged && this.dragging)
        this.dragging = this.dragging.map(update.changes);
      if (this.style.update(update))
        setTimeout(() => this.select(this.lastEvent), 20);
    }
  };
  function addsSelectionRange(view, event) {
    let facet = view.state.facet(clickAddsSelectionRange);
    return facet.length ? facet[0](event) : browser.mac ? event.metaKey : event.ctrlKey;
  }
  function dragMovesSelection(view, event) {
    let facet = view.state.facet(dragMovesSelection$1);
    return facet.length ? facet[0](event) : browser.mac ? !event.altKey : !event.ctrlKey;
  }
  function isInPrimarySelection(view, event) {
    let { main } = view.state.selection;
    if (main.empty)
      return false;
    let sel = getSelection(view.root);
    if (sel.rangeCount == 0)
      return true;
    let rects = sel.getRangeAt(0).getClientRects();
    for (let i = 0; i < rects.length; i++) {
      let rect = rects[i];
      if (rect.left <= event.clientX && rect.right >= event.clientX && rect.top <= event.clientY && rect.bottom >= event.clientY)
        return true;
    }
    return false;
  }
  function eventBelongsToEditor(view, event) {
    if (!event.bubbles)
      return true;
    if (event.defaultPrevented)
      return false;
    for (let node = event.target, cView; node != view.contentDOM; node = node.parentNode)
      if (!node || node.nodeType == 11 || (cView = ContentView.get(node)) && cView.ignoreEvent(event))
        return false;
    return true;
  }
  var handlers = /* @__PURE__ */ Object.create(null);
  var brokenClipboardAPI = browser.ie && browser.ie_version < 15 || browser.ios && browser.webkit_version < 604;
  function capturePaste(view) {
    let parent = view.dom.parentNode;
    if (!parent)
      return;
    let target = parent.appendChild(document.createElement("textarea"));
    target.style.cssText = "position: fixed; left: -10000px; top: 10px";
    target.focus();
    setTimeout(() => {
      view.focus();
      target.remove();
      doPaste(view, target.value);
    }, 50);
  }
  function doPaste(view, input) {
    let { state } = view, changes, i = 1, text = state.toText(input);
    let byLine = text.lines == state.selection.ranges.length;
    let linewise = lastLinewiseCopy != null && state.selection.ranges.every((r) => r.empty) && lastLinewiseCopy == text.toString();
    if (linewise) {
      let lastLine = -1;
      changes = state.changeByRange((range) => {
        let line = state.doc.lineAt(range.from);
        if (line.from == lastLine)
          return { range };
        lastLine = line.from;
        let insert2 = state.toText((byLine ? text.line(i++).text : input) + state.lineBreak);
        return {
          changes: { from: line.from, insert: insert2 },
          range: EditorSelection.cursor(range.from + insert2.length)
        };
      });
    } else if (byLine) {
      changes = state.changeByRange((range) => {
        let line = text.line(i++);
        return {
          changes: { from: range.from, to: range.to, insert: line.text },
          range: EditorSelection.cursor(range.from + line.length)
        };
      });
    } else {
      changes = state.replaceSelection(text);
    }
    view.dispatch(changes, {
      userEvent: "input.paste",
      scrollIntoView: true
    });
  }
  handlers.keydown = (view, event) => {
    view.inputState.setSelectionOrigin("select");
    if (event.keyCode == 27)
      view.inputState.lastEscPress = Date.now();
    else if (modifierCodes.indexOf(event.keyCode) < 0)
      view.inputState.lastEscPress = 0;
  };
  var lastTouch = 0;
  handlers.touchstart = (view, e) => {
    lastTouch = Date.now();
    view.inputState.setSelectionOrigin("select.pointer");
  };
  handlers.touchmove = (view) => {
    view.inputState.setSelectionOrigin("select.pointer");
  };
  handlers.mousedown = (view, event) => {
    view.observer.flush();
    if (lastTouch > Date.now() - 2e3 && getClickType(event) == 1)
      return;
    let style = null;
    for (let makeStyle of view.state.facet(mouseSelectionStyle)) {
      style = makeStyle(view, event);
      if (style)
        break;
    }
    if (!style && event.button == 0)
      style = basicMouseSelection(view, event);
    if (style) {
      let mustFocus = view.root.activeElement != view.contentDOM;
      if (mustFocus)
        view.observer.ignore(() => focusPreventScroll(view.contentDOM));
      view.inputState.startMouseSelection(new MouseSelection(view, event, style, mustFocus));
    }
  };
  function rangeForClick(view, pos, bias, type) {
    if (type == 1) {
      return EditorSelection.cursor(pos, bias);
    } else if (type == 2) {
      return groupAt(view.state, pos, bias);
    } else {
      let visual = LineView.find(view.docView, pos), line = view.state.doc.lineAt(visual ? visual.posAtEnd : pos);
      let from2 = visual ? visual.posAtStart : line.from, to = visual ? visual.posAtEnd : line.to;
      if (to < view.state.doc.length && to == line.to)
        to++;
      return EditorSelection.range(from2, to);
    }
  }
  var insideY = (y, rect) => y >= rect.top && y <= rect.bottom;
  var inside = (x, y, rect) => insideY(y, rect) && x >= rect.left && x <= rect.right;
  function findPositionSide(view, pos, x, y) {
    let line = LineView.find(view.docView, pos);
    if (!line)
      return 1;
    let off = pos - line.posAtStart;
    if (off == 0)
      return 1;
    if (off == line.length)
      return -1;
    let before = line.coordsAt(off, -1);
    if (before && inside(x, y, before))
      return -1;
    let after = line.coordsAt(off, 1);
    if (after && inside(x, y, after))
      return 1;
    return before && insideY(y, before) ? -1 : 1;
  }
  function queryPos(view, event) {
    let pos = view.posAtCoords({ x: event.clientX, y: event.clientY }, false);
    return { pos, bias: findPositionSide(view, pos, event.clientX, event.clientY) };
  }
  var BadMouseDetail = browser.ie && browser.ie_version <= 11;
  var lastMouseDown = null;
  var lastMouseDownCount = 0;
  var lastMouseDownTime = 0;
  function getClickType(event) {
    if (!BadMouseDetail)
      return event.detail;
    let last = lastMouseDown, lastTime = lastMouseDownTime;
    lastMouseDown = event;
    lastMouseDownTime = Date.now();
    return lastMouseDownCount = !last || lastTime > Date.now() - 400 && Math.abs(last.clientX - event.clientX) < 2 && Math.abs(last.clientY - event.clientY) < 2 ? (lastMouseDownCount + 1) % 3 : 1;
  }
  function basicMouseSelection(view, event) {
    let start = queryPos(view, event), type = getClickType(event);
    let startSel = view.state.selection;
    let last = start, lastEvent = event;
    return {
      update(update) {
        if (update.docChanged) {
          if (start)
            start.pos = update.changes.mapPos(start.pos);
          startSel = startSel.map(update.changes);
          lastEvent = null;
        }
      },
      get(event2, extend2, multiple) {
        let cur2;
        if (lastEvent && event2.clientX == lastEvent.clientX && event2.clientY == lastEvent.clientY)
          cur2 = last;
        else {
          cur2 = last = queryPos(view, event2);
          lastEvent = event2;
        }
        if (!cur2 || !start)
          return startSel;
        let range = rangeForClick(view, cur2.pos, cur2.bias, type);
        if (start.pos != cur2.pos && !extend2) {
          let startRange = rangeForClick(view, start.pos, start.bias, type);
          let from2 = Math.min(startRange.from, range.from), to = Math.max(startRange.to, range.to);
          range = from2 < range.from ? EditorSelection.range(from2, to) : EditorSelection.range(to, from2);
        }
        if (extend2)
          return startSel.replaceRange(startSel.main.extend(range.from, range.to));
        else if (multiple)
          return startSel.addRange(range);
        else
          return EditorSelection.create([range]);
      }
    };
  }
  handlers.dragstart = (view, event) => {
    let { selection: { main } } = view.state;
    let { mouseSelection } = view.inputState;
    if (mouseSelection)
      mouseSelection.dragging = main;
    if (event.dataTransfer) {
      event.dataTransfer.setData("Text", view.state.sliceDoc(main.from, main.to));
      event.dataTransfer.effectAllowed = "copyMove";
    }
  };
  function dropText(view, event, text, direct) {
    if (!text)
      return;
    let dropPos = view.posAtCoords({ x: event.clientX, y: event.clientY }, false);
    event.preventDefault();
    let { mouseSelection } = view.inputState;
    let del = direct && mouseSelection && mouseSelection.dragging && mouseSelection.dragMove ? { from: mouseSelection.dragging.from, to: mouseSelection.dragging.to } : null;
    let ins = { from: dropPos, insert: text };
    let changes = view.state.changes(del ? [del, ins] : ins);
    view.focus();
    view.dispatch({
      changes,
      selection: { anchor: changes.mapPos(dropPos, -1), head: changes.mapPos(dropPos, 1) },
      userEvent: del ? "move.drop" : "input.drop"
    });
  }
  handlers.drop = (view, event) => {
    if (!event.dataTransfer)
      return;
    if (view.state.readOnly)
      return event.preventDefault();
    let files = event.dataTransfer.files;
    if (files && files.length) {
      event.preventDefault();
      let text = Array(files.length), read = 0;
      let finishFile = () => {
        if (++read == files.length)
          dropText(view, event, text.filter((s) => s != null).join(view.state.lineBreak), false);
      };
      for (let i = 0; i < files.length; i++) {
        let reader = new FileReader();
        reader.onerror = finishFile;
        reader.onload = () => {
          if (!/[\x00-\x08\x0e-\x1f]{2}/.test(reader.result))
            text[i] = reader.result;
          finishFile();
        };
        reader.readAsText(files[i]);
      }
    } else {
      dropText(view, event, event.dataTransfer.getData("Text"), true);
    }
  };
  handlers.paste = (view, event) => {
    if (view.state.readOnly)
      return event.preventDefault();
    view.observer.flush();
    let data = brokenClipboardAPI ? null : event.clipboardData;
    if (data) {
      doPaste(view, data.getData("text/plain"));
      event.preventDefault();
    } else {
      capturePaste(view);
    }
  };
  function captureCopy(view, text) {
    let parent = view.dom.parentNode;
    if (!parent)
      return;
    let target = parent.appendChild(document.createElement("textarea"));
    target.style.cssText = "position: fixed; left: -10000px; top: 10px";
    target.value = text;
    target.focus();
    target.selectionEnd = text.length;
    target.selectionStart = 0;
    setTimeout(() => {
      target.remove();
      view.focus();
    }, 50);
  }
  function copiedRange(state) {
    let content2 = [], ranges = [], linewise = false;
    for (let range of state.selection.ranges)
      if (!range.empty) {
        content2.push(state.sliceDoc(range.from, range.to));
        ranges.push(range);
      }
    if (!content2.length) {
      let upto = -1;
      for (let { from: from2 } of state.selection.ranges) {
        let line = state.doc.lineAt(from2);
        if (line.number > upto) {
          content2.push(line.text);
          ranges.push({ from: line.from, to: Math.min(state.doc.length, line.to + 1) });
        }
        upto = line.number;
      }
      linewise = true;
    }
    return { text: content2.join(state.lineBreak), ranges, linewise };
  }
  var lastLinewiseCopy = null;
  handlers.copy = handlers.cut = (view, event) => {
    let { text, ranges, linewise } = copiedRange(view.state);
    if (!text && !linewise)
      return;
    lastLinewiseCopy = linewise ? text : null;
    let data = brokenClipboardAPI ? null : event.clipboardData;
    if (data) {
      event.preventDefault();
      data.clearData();
      data.setData("text/plain", text);
    } else {
      captureCopy(view, text);
    }
    if (event.type == "cut" && !view.state.readOnly)
      view.dispatch({
        changes: ranges,
        scrollIntoView: true,
        userEvent: "delete.cut"
      });
  };
  handlers.focus = handlers.blur = (view) => {
    setTimeout(() => {
      if (view.hasFocus != view.inputState.notifiedFocused)
        view.update([]);
    }, 10);
  };
  function forceClearComposition(view, rapid) {
    if (view.docView.compositionDeco.size) {
      view.inputState.rapidCompositionStart = rapid;
      try {
        view.update([]);
      } finally {
        view.inputState.rapidCompositionStart = false;
      }
    }
  }
  handlers.compositionstart = handlers.compositionupdate = (view) => {
    if (view.inputState.compositionFirstChange == null)
      view.inputState.compositionFirstChange = true;
    if (view.inputState.composing < 0) {
      view.inputState.composing = 0;
      if (view.docView.compositionDeco.size) {
        view.observer.flush();
        forceClearComposition(view, true);
      }
    }
  };
  handlers.compositionend = (view) => {
    view.inputState.composing = -1;
    view.inputState.compositionEndedAt = Date.now();
    view.inputState.compositionFirstChange = null;
    setTimeout(() => {
      if (view.inputState.composing < 0)
        forceClearComposition(view, false);
    }, 50);
  };
  handlers.contextmenu = (view) => {
    view.inputState.lastContextMenu = Date.now();
  };
  handlers.beforeinput = (view, event) => {
    var _a2;
    let pending;
    if (browser.chrome && browser.android && (pending = PendingKeys.find((key) => key.inputType == event.inputType))) {
      view.observer.delayAndroidKey(pending.key, pending.keyCode);
      if (pending.key == "Backspace" || pending.key == "Delete") {
        let startViewHeight = ((_a2 = window.visualViewport) === null || _a2 === void 0 ? void 0 : _a2.height) || 0;
        setTimeout(() => {
          var _a3;
          if ((((_a3 = window.visualViewport) === null || _a3 === void 0 ? void 0 : _a3.height) || 0) > startViewHeight + 10 && view.hasFocus) {
            view.contentDOM.blur();
            view.focus();
          }
        }, 100);
      }
    }
  };
  var wrappingWhiteSpace = ["pre-wrap", "normal", "pre-line", "break-spaces"];
  var HeightOracle = class {
    constructor() {
      this.doc = Text.empty;
      this.lineWrapping = false;
      this.direction = Direction.LTR;
      this.heightSamples = {};
      this.lineHeight = 14;
      this.charWidth = 7;
      this.lineLength = 30;
      this.heightChanged = false;
    }
    heightForGap(from2, to) {
      let lines = this.doc.lineAt(to).number - this.doc.lineAt(from2).number + 1;
      if (this.lineWrapping)
        lines += Math.ceil((to - from2 - lines * this.lineLength * 0.5) / this.lineLength);
      return this.lineHeight * lines;
    }
    heightForLine(length) {
      if (!this.lineWrapping)
        return this.lineHeight;
      let lines = 1 + Math.max(0, Math.ceil((length - this.lineLength) / (this.lineLength - 5)));
      return lines * this.lineHeight;
    }
    setDoc(doc2) {
      this.doc = doc2;
      return this;
    }
    mustRefreshForStyle(whiteSpace, direction) {
      return wrappingWhiteSpace.indexOf(whiteSpace) > -1 != this.lineWrapping || this.direction != direction;
    }
    mustRefreshForHeights(lineHeights) {
      let newHeight = false;
      for (let i = 0; i < lineHeights.length; i++) {
        let h = lineHeights[i];
        if (h < 0) {
          i++;
        } else if (!this.heightSamples[Math.floor(h * 10)]) {
          newHeight = true;
          this.heightSamples[Math.floor(h * 10)] = true;
        }
      }
      return newHeight;
    }
    refresh(whiteSpace, direction, lineHeight, charWidth, lineLength, knownHeights) {
      let lineWrapping = wrappingWhiteSpace.indexOf(whiteSpace) > -1;
      let changed = Math.round(lineHeight) != Math.round(this.lineHeight) || this.lineWrapping != lineWrapping || this.direction != direction;
      this.lineWrapping = lineWrapping;
      this.direction = direction;
      this.lineHeight = lineHeight;
      this.charWidth = charWidth;
      this.lineLength = lineLength;
      if (changed) {
        this.heightSamples = {};
        for (let i = 0; i < knownHeights.length; i++) {
          let h = knownHeights[i];
          if (h < 0)
            i++;
          else
            this.heightSamples[Math.floor(h * 10)] = true;
        }
      }
      return changed;
    }
  };
  var MeasuredHeights = class {
    constructor(from2, heights) {
      this.from = from2;
      this.heights = heights;
      this.index = 0;
    }
    get more() {
      return this.index < this.heights.length;
    }
  };
  var BlockInfo = class {
    constructor(from2, length, top2, height, type) {
      this.from = from2;
      this.length = length;
      this.top = top2;
      this.height = height;
      this.type = type;
    }
    get to() {
      return this.from + this.length;
    }
    get bottom() {
      return this.top + this.height;
    }
    join(other) {
      let detail = (Array.isArray(this.type) ? this.type : [this]).concat(Array.isArray(other.type) ? other.type : [other]);
      return new BlockInfo(this.from, this.length + other.length, this.top, this.height + other.height, detail);
    }
    moveY(offset) {
      return !offset ? this : new BlockInfo(this.from, this.length, this.top + offset, this.height, Array.isArray(this.type) ? this.type.map((b) => b.moveY(offset)) : this.type);
    }
  };
  var QueryType = /* @__PURE__ */ function(QueryType3) {
    QueryType3[QueryType3["ByPos"] = 0] = "ByPos";
    QueryType3[QueryType3["ByHeight"] = 1] = "ByHeight";
    QueryType3[QueryType3["ByPosNoHeight"] = 2] = "ByPosNoHeight";
    return QueryType3;
  }(QueryType || (QueryType = {}));
  var Epsilon = 1e-3;
  var HeightMap = class {
    constructor(length, height, flags = 2) {
      this.length = length;
      this.height = height;
      this.flags = flags;
    }
    get outdated() {
      return (this.flags & 2) > 0;
    }
    set outdated(value) {
      this.flags = (value ? 2 : 0) | this.flags & ~2;
    }
    setHeight(oracle, height) {
      if (this.height != height) {
        if (Math.abs(this.height - height) > Epsilon)
          oracle.heightChanged = true;
        this.height = height;
      }
    }
    replace(_from, _to, nodes) {
      return HeightMap.of(nodes);
    }
    decomposeLeft(_to, result) {
      result.push(this);
    }
    decomposeRight(_from, result) {
      result.push(this);
    }
    applyChanges(decorations2, oldDoc, oracle, changes) {
      let me = this;
      for (let i = changes.length - 1; i >= 0; i--) {
        let { fromA, toA, fromB, toB } = changes[i];
        let start = me.lineAt(fromA, QueryType.ByPosNoHeight, oldDoc, 0, 0);
        let end = start.to >= toA ? start : me.lineAt(toA, QueryType.ByPosNoHeight, oldDoc, 0, 0);
        toB += end.to - toA;
        toA = end.to;
        while (i > 0 && start.from <= changes[i - 1].toA) {
          fromA = changes[i - 1].fromA;
          fromB = changes[i - 1].fromB;
          i--;
          if (fromA < start.from)
            start = me.lineAt(fromA, QueryType.ByPosNoHeight, oldDoc, 0, 0);
        }
        fromB += start.from - fromA;
        fromA = start.from;
        let nodes = NodeBuilder.build(oracle, decorations2, fromB, toB);
        me = me.replace(fromA, toA, nodes);
      }
      return me.updateHeight(oracle, 0);
    }
    static empty() {
      return new HeightMapText(0, 0);
    }
    static of(nodes) {
      if (nodes.length == 1)
        return nodes[0];
      let i = 0, j = nodes.length, before = 0, after = 0;
      for (; ; ) {
        if (i == j) {
          if (before > after * 2) {
            let split = nodes[i - 1];
            if (split.break)
              nodes.splice(--i, 1, split.left, null, split.right);
            else
              nodes.splice(--i, 1, split.left, split.right);
            j += 1 + split.break;
            before -= split.size;
          } else if (after > before * 2) {
            let split = nodes[j];
            if (split.break)
              nodes.splice(j, 1, split.left, null, split.right);
            else
              nodes.splice(j, 1, split.left, split.right);
            j += 2 + split.break;
            after -= split.size;
          } else {
            break;
          }
        } else if (before < after) {
          let next = nodes[i++];
          if (next)
            before += next.size;
        } else {
          let next = nodes[--j];
          if (next)
            after += next.size;
        }
      }
      let brk = 0;
      if (nodes[i - 1] == null) {
        brk = 1;
        i--;
      } else if (nodes[i] == null) {
        brk = 1;
        j++;
      }
      return new HeightMapBranch(HeightMap.of(nodes.slice(0, i)), brk, HeightMap.of(nodes.slice(j)));
    }
  };
  HeightMap.prototype.size = 1;
  var HeightMapBlock = class extends HeightMap {
    constructor(length, height, type) {
      super(length, height);
      this.type = type;
    }
    blockAt(_height, _doc, top2, offset) {
      return new BlockInfo(offset, this.length, top2, this.height, this.type);
    }
    lineAt(_value, _type, doc2, top2, offset) {
      return this.blockAt(0, doc2, top2, offset);
    }
    forEachLine(_from, _to, doc2, top2, offset, f) {
      f(this.blockAt(0, doc2, top2, offset));
    }
    updateHeight(oracle, offset = 0, _force = false, measured) {
      if (measured && measured.from <= offset && measured.more)
        this.setHeight(oracle, measured.heights[measured.index++]);
      this.outdated = false;
      return this;
    }
    toString() {
      return `block(${this.length})`;
    }
  };
  var HeightMapText = class extends HeightMapBlock {
    constructor(length, height) {
      super(length, height, BlockType.Text);
      this.collapsed = 0;
      this.widgetHeight = 0;
    }
    replace(_from, _to, nodes) {
      let node = nodes[0];
      if (nodes.length == 1 && (node instanceof HeightMapText || node instanceof HeightMapGap && node.flags & 4) && Math.abs(this.length - node.length) < 10) {
        if (node instanceof HeightMapGap)
          node = new HeightMapText(node.length, this.height);
        else
          node.height = this.height;
        if (!this.outdated)
          node.outdated = false;
        return node;
      } else {
        return HeightMap.of(nodes);
      }
    }
    updateHeight(oracle, offset = 0, force = false, measured) {
      if (measured && measured.from <= offset && measured.more)
        this.setHeight(oracle, measured.heights[measured.index++]);
      else if (force || this.outdated)
        this.setHeight(oracle, Math.max(this.widgetHeight, oracle.heightForLine(this.length - this.collapsed)));
      this.outdated = false;
      return this;
    }
    toString() {
      return `line(${this.length}${this.collapsed ? -this.collapsed : ""}${this.widgetHeight ? ":" + this.widgetHeight : ""})`;
    }
  };
  var HeightMapGap = class extends HeightMap {
    constructor(length) {
      super(length, 0);
    }
    lines(doc2, offset) {
      let firstLine = doc2.lineAt(offset).number, lastLine = doc2.lineAt(offset + this.length).number;
      return { firstLine, lastLine, lineHeight: this.height / (lastLine - firstLine + 1) };
    }
    blockAt(height, doc2, top2, offset) {
      let { firstLine, lastLine, lineHeight } = this.lines(doc2, offset);
      let line = Math.max(0, Math.min(lastLine - firstLine, Math.floor((height - top2) / lineHeight)));
      let { from: from2, length } = doc2.line(firstLine + line);
      return new BlockInfo(from2, length, top2 + lineHeight * line, lineHeight, BlockType.Text);
    }
    lineAt(value, type, doc2, top2, offset) {
      if (type == QueryType.ByHeight)
        return this.blockAt(value, doc2, top2, offset);
      if (type == QueryType.ByPosNoHeight) {
        let { from: from3, to } = doc2.lineAt(value);
        return new BlockInfo(from3, to - from3, 0, 0, BlockType.Text);
      }
      let { firstLine, lineHeight } = this.lines(doc2, offset);
      let { from: from2, length, number: number2 } = doc2.lineAt(value);
      return new BlockInfo(from2, length, top2 + lineHeight * (number2 - firstLine), lineHeight, BlockType.Text);
    }
    forEachLine(from2, to, doc2, top2, offset, f) {
      let { firstLine, lineHeight } = this.lines(doc2, offset);
      for (let pos = Math.max(from2, offset), end = Math.min(offset + this.length, to); pos <= end; ) {
        let line = doc2.lineAt(pos);
        if (pos == from2)
          top2 += lineHeight * (line.number - firstLine);
        f(new BlockInfo(line.from, line.length, top2, lineHeight, BlockType.Text));
        top2 += lineHeight;
        pos = line.to + 1;
      }
    }
    replace(from2, to, nodes) {
      let after = this.length - to;
      if (after > 0) {
        let last = nodes[nodes.length - 1];
        if (last instanceof HeightMapGap)
          nodes[nodes.length - 1] = new HeightMapGap(last.length + after);
        else
          nodes.push(null, new HeightMapGap(after - 1));
      }
      if (from2 > 0) {
        let first = nodes[0];
        if (first instanceof HeightMapGap)
          nodes[0] = new HeightMapGap(from2 + first.length);
        else
          nodes.unshift(new HeightMapGap(from2 - 1), null);
      }
      return HeightMap.of(nodes);
    }
    decomposeLeft(to, result) {
      result.push(new HeightMapGap(to - 1), null);
    }
    decomposeRight(from2, result) {
      result.push(null, new HeightMapGap(this.length - from2 - 1));
    }
    updateHeight(oracle, offset = 0, force = false, measured) {
      let end = offset + this.length;
      if (measured && measured.from <= offset + this.length && measured.more) {
        let nodes = [], pos = Math.max(offset, measured.from), singleHeight = -1;
        let wasChanged = oracle.heightChanged;
        if (measured.from > offset)
          nodes.push(new HeightMapGap(measured.from - offset - 1).updateHeight(oracle, offset));
        while (pos <= end && measured.more) {
          let len = oracle.doc.lineAt(pos).length;
          if (nodes.length)
            nodes.push(null);
          let height = measured.heights[measured.index++];
          if (singleHeight == -1)
            singleHeight = height;
          else if (Math.abs(height - singleHeight) >= Epsilon)
            singleHeight = -2;
          let line = new HeightMapText(len, height);
          line.outdated = false;
          nodes.push(line);
          pos += len + 1;
        }
        if (pos <= end)
          nodes.push(null, new HeightMapGap(end - pos).updateHeight(oracle, pos));
        let result = HeightMap.of(nodes);
        oracle.heightChanged = wasChanged || singleHeight < 0 || Math.abs(result.height - this.height) >= Epsilon || Math.abs(singleHeight - this.lines(oracle.doc, offset).lineHeight) >= Epsilon;
        return result;
      } else if (force || this.outdated) {
        this.setHeight(oracle, oracle.heightForGap(offset, offset + this.length));
        this.outdated = false;
      }
      return this;
    }
    toString() {
      return `gap(${this.length})`;
    }
  };
  var HeightMapBranch = class extends HeightMap {
    constructor(left, brk, right) {
      super(left.length + brk + right.length, left.height + right.height, brk | (left.outdated || right.outdated ? 2 : 0));
      this.left = left;
      this.right = right;
      this.size = left.size + right.size;
    }
    get break() {
      return this.flags & 1;
    }
    blockAt(height, doc2, top2, offset) {
      let mid = top2 + this.left.height;
      return height < mid ? this.left.blockAt(height, doc2, top2, offset) : this.right.blockAt(height, doc2, mid, offset + this.left.length + this.break);
    }
    lineAt(value, type, doc2, top2, offset) {
      let rightTop = top2 + this.left.height, rightOffset = offset + this.left.length + this.break;
      let left = type == QueryType.ByHeight ? value < rightTop : value < rightOffset;
      let base2 = left ? this.left.lineAt(value, type, doc2, top2, offset) : this.right.lineAt(value, type, doc2, rightTop, rightOffset);
      if (this.break || (left ? base2.to < rightOffset : base2.from > rightOffset))
        return base2;
      let subQuery = type == QueryType.ByPosNoHeight ? QueryType.ByPosNoHeight : QueryType.ByPos;
      if (left)
        return base2.join(this.right.lineAt(rightOffset, subQuery, doc2, rightTop, rightOffset));
      else
        return this.left.lineAt(rightOffset, subQuery, doc2, top2, offset).join(base2);
    }
    forEachLine(from2, to, doc2, top2, offset, f) {
      let rightTop = top2 + this.left.height, rightOffset = offset + this.left.length + this.break;
      if (this.break) {
        if (from2 < rightOffset)
          this.left.forEachLine(from2, to, doc2, top2, offset, f);
        if (to >= rightOffset)
          this.right.forEachLine(from2, to, doc2, rightTop, rightOffset, f);
      } else {
        let mid = this.lineAt(rightOffset, QueryType.ByPos, doc2, top2, offset);
        if (from2 < mid.from)
          this.left.forEachLine(from2, mid.from - 1, doc2, top2, offset, f);
        if (mid.to >= from2 && mid.from <= to)
          f(mid);
        if (to > mid.to)
          this.right.forEachLine(mid.to + 1, to, doc2, rightTop, rightOffset, f);
      }
    }
    replace(from2, to, nodes) {
      let rightStart = this.left.length + this.break;
      if (to < rightStart)
        return this.balanced(this.left.replace(from2, to, nodes), this.right);
      if (from2 > this.left.length)
        return this.balanced(this.left, this.right.replace(from2 - rightStart, to - rightStart, nodes));
      let result = [];
      if (from2 > 0)
        this.decomposeLeft(from2, result);
      let left = result.length;
      for (let node of nodes)
        result.push(node);
      if (from2 > 0)
        mergeGaps(result, left - 1);
      if (to < this.length) {
        let right = result.length;
        this.decomposeRight(to, result);
        mergeGaps(result, right);
      }
      return HeightMap.of(result);
    }
    decomposeLeft(to, result) {
      let left = this.left.length;
      if (to <= left)
        return this.left.decomposeLeft(to, result);
      result.push(this.left);
      if (this.break) {
        left++;
        if (to >= left)
          result.push(null);
      }
      if (to > left)
        this.right.decomposeLeft(to - left, result);
    }
    decomposeRight(from2, result) {
      let left = this.left.length, right = left + this.break;
      if (from2 >= right)
        return this.right.decomposeRight(from2 - right, result);
      if (from2 < left)
        this.left.decomposeRight(from2, result);
      if (this.break && from2 < right)
        result.push(null);
      result.push(this.right);
    }
    balanced(left, right) {
      if (left.size > 2 * right.size || right.size > 2 * left.size)
        return HeightMap.of(this.break ? [left, null, right] : [left, right]);
      this.left = left;
      this.right = right;
      this.height = left.height + right.height;
      this.outdated = left.outdated || right.outdated;
      this.size = left.size + right.size;
      this.length = left.length + this.break + right.length;
      return this;
    }
    updateHeight(oracle, offset = 0, force = false, measured) {
      let { left, right } = this, rightStart = offset + left.length + this.break, rebalance = null;
      if (measured && measured.from <= offset + left.length && measured.more)
        rebalance = left = left.updateHeight(oracle, offset, force, measured);
      else
        left.updateHeight(oracle, offset, force);
      if (measured && measured.from <= rightStart + right.length && measured.more)
        rebalance = right = right.updateHeight(oracle, rightStart, force, measured);
      else
        right.updateHeight(oracle, rightStart, force);
      if (rebalance)
        return this.balanced(left, right);
      this.height = this.left.height + this.right.height;
      this.outdated = false;
      return this;
    }
    toString() {
      return this.left + (this.break ? " " : "-") + this.right;
    }
  };
  function mergeGaps(nodes, around) {
    let before, after;
    if (nodes[around] == null && (before = nodes[around - 1]) instanceof HeightMapGap && (after = nodes[around + 1]) instanceof HeightMapGap)
      nodes.splice(around - 1, 3, new HeightMapGap(before.length + 1 + after.length));
  }
  var relevantWidgetHeight = 5;
  var NodeBuilder = class {
    constructor(pos, oracle) {
      this.pos = pos;
      this.oracle = oracle;
      this.nodes = [];
      this.lineStart = -1;
      this.lineEnd = -1;
      this.covering = null;
      this.writtenTo = pos;
    }
    get isCovered() {
      return this.covering && this.nodes[this.nodes.length - 1] == this.covering;
    }
    span(_from, to) {
      if (this.lineStart > -1) {
        let end = Math.min(to, this.lineEnd), last = this.nodes[this.nodes.length - 1];
        if (last instanceof HeightMapText)
          last.length += end - this.pos;
        else if (end > this.pos || !this.isCovered)
          this.nodes.push(new HeightMapText(end - this.pos, -1));
        this.writtenTo = end;
        if (to > end) {
          this.nodes.push(null);
          this.writtenTo++;
          this.lineStart = -1;
        }
      }
      this.pos = to;
    }
    point(from2, to, deco) {
      if (from2 < to || deco.heightRelevant) {
        let height = deco.widget ? deco.widget.estimatedHeight : 0;
        if (height < 0)
          height = this.oracle.lineHeight;
        let len = to - from2;
        if (deco.block) {
          this.addBlock(new HeightMapBlock(len, height, deco.type));
        } else if (len || height >= relevantWidgetHeight) {
          this.addLineDeco(height, len);
        }
      } else if (to > from2) {
        this.span(from2, to);
      }
      if (this.lineEnd > -1 && this.lineEnd < this.pos)
        this.lineEnd = this.oracle.doc.lineAt(this.pos).to;
    }
    enterLine() {
      if (this.lineStart > -1)
        return;
      let { from: from2, to } = this.oracle.doc.lineAt(this.pos);
      this.lineStart = from2;
      this.lineEnd = to;
      if (this.writtenTo < from2) {
        if (this.writtenTo < from2 - 1 || this.nodes[this.nodes.length - 1] == null)
          this.nodes.push(this.blankContent(this.writtenTo, from2 - 1));
        this.nodes.push(null);
      }
      if (this.pos > from2)
        this.nodes.push(new HeightMapText(this.pos - from2, -1));
      this.writtenTo = this.pos;
    }
    blankContent(from2, to) {
      let gap = new HeightMapGap(to - from2);
      if (this.oracle.doc.lineAt(from2).to == to)
        gap.flags |= 4;
      return gap;
    }
    ensureLine() {
      this.enterLine();
      let last = this.nodes.length ? this.nodes[this.nodes.length - 1] : null;
      if (last instanceof HeightMapText)
        return last;
      let line = new HeightMapText(0, -1);
      this.nodes.push(line);
      return line;
    }
    addBlock(block) {
      this.enterLine();
      if (block.type == BlockType.WidgetAfter && !this.isCovered)
        this.ensureLine();
      this.nodes.push(block);
      this.writtenTo = this.pos = this.pos + block.length;
      if (block.type != BlockType.WidgetBefore)
        this.covering = block;
    }
    addLineDeco(height, length) {
      let line = this.ensureLine();
      line.length += length;
      line.collapsed += length;
      line.widgetHeight = Math.max(line.widgetHeight, height);
      this.writtenTo = this.pos = this.pos + length;
    }
    finish(from2) {
      let last = this.nodes.length == 0 ? null : this.nodes[this.nodes.length - 1];
      if (this.lineStart > -1 && !(last instanceof HeightMapText) && !this.isCovered)
        this.nodes.push(new HeightMapText(0, -1));
      else if (this.writtenTo < this.pos || last == null)
        this.nodes.push(this.blankContent(this.writtenTo, this.pos));
      let pos = from2;
      for (let node of this.nodes) {
        if (node instanceof HeightMapText)
          node.updateHeight(this.oracle, pos);
        pos += node ? node.length : 1;
      }
      return this.nodes;
    }
    static build(oracle, decorations2, from2, to) {
      let builder = new NodeBuilder(from2, oracle);
      RangeSet.spans(decorations2, from2, to, builder, 0);
      return builder.finish(from2);
    }
  };
  function heightRelevantDecoChanges(a, b, diff) {
    let comp = new DecorationComparator();
    RangeSet.compare(a, b, diff, comp, 0);
    return comp.changes;
  }
  var DecorationComparator = class {
    constructor() {
      this.changes = [];
    }
    compareRange() {
    }
    comparePoint(from2, to, a, b) {
      if (from2 < to || a && a.heightRelevant || b && b.heightRelevant)
        addRange(from2, to, this.changes, 5);
    }
  };
  function visiblePixelRange(dom, paddingTop) {
    let rect = dom.getBoundingClientRect();
    let left = Math.max(0, rect.left), right = Math.min(innerWidth, rect.right);
    let top2 = Math.max(0, rect.top), bottom = Math.min(innerHeight, rect.bottom);
    let body = dom.ownerDocument.body;
    for (let parent = dom.parentNode; parent && parent != body; ) {
      if (parent.nodeType == 1) {
        let elt = parent;
        let style = window.getComputedStyle(elt);
        if ((elt.scrollHeight > elt.clientHeight || elt.scrollWidth > elt.clientWidth) && style.overflow != "visible") {
          let parentRect = elt.getBoundingClientRect();
          left = Math.max(left, parentRect.left);
          right = Math.min(right, parentRect.right);
          top2 = Math.max(top2, parentRect.top);
          bottom = Math.min(bottom, parentRect.bottom);
        }
        parent = style.position == "absolute" || style.position == "fixed" ? elt.offsetParent : elt.parentNode;
      } else if (parent.nodeType == 11) {
        parent = parent.host;
      } else {
        break;
      }
    }
    return {
      left: left - rect.left,
      right: Math.max(left, right) - rect.left,
      top: top2 - (rect.top + paddingTop),
      bottom: Math.max(top2, bottom) - (rect.top + paddingTop)
    };
  }
  function fullPixelRange(dom, paddingTop) {
    let rect = dom.getBoundingClientRect();
    return {
      left: 0,
      right: rect.right - rect.left,
      top: paddingTop,
      bottom: rect.bottom - (rect.top + paddingTop)
    };
  }
  var LineGap = class {
    constructor(from2, to, size) {
      this.from = from2;
      this.to = to;
      this.size = size;
    }
    static same(a, b) {
      if (a.length != b.length)
        return false;
      for (let i = 0; i < a.length; i++) {
        let gA = a[i], gB = b[i];
        if (gA.from != gB.from || gA.to != gB.to || gA.size != gB.size)
          return false;
      }
      return true;
    }
    draw(wrapping) {
      return Decoration.replace({ widget: new LineGapWidget(this.size, wrapping) }).range(this.from, this.to);
    }
  };
  var LineGapWidget = class extends WidgetType {
    constructor(size, vertical) {
      super();
      this.size = size;
      this.vertical = vertical;
    }
    eq(other) {
      return other.size == this.size && other.vertical == this.vertical;
    }
    toDOM() {
      let elt = document.createElement("div");
      if (this.vertical) {
        elt.style.height = this.size + "px";
      } else {
        elt.style.width = this.size + "px";
        elt.style.height = "2px";
        elt.style.display = "inline-block";
      }
      return elt;
    }
    get estimatedHeight() {
      return this.vertical ? this.size : -1;
    }
  };
  var ViewState = class {
    constructor(state) {
      this.state = state;
      this.pixelViewport = { left: 0, right: window.innerWidth, top: 0, bottom: 0 };
      this.inView = true;
      this.paddingTop = 0;
      this.paddingBottom = 0;
      this.contentDOMWidth = 0;
      this.contentDOMHeight = 0;
      this.editorHeight = 0;
      this.editorWidth = 0;
      this.heightOracle = new HeightOracle();
      this.scaler = IdScaler;
      this.scrollTarget = null;
      this.printing = false;
      this.mustMeasureContent = true;
      this.visibleRanges = [];
      this.mustEnforceCursorAssoc = false;
      this.heightMap = HeightMap.empty().applyChanges(state.facet(decorations), Text.empty, this.heightOracle.setDoc(state.doc), [new ChangedRange(0, 0, 0, state.doc.length)]);
      this.viewport = this.getViewport(0, null);
      this.updateViewportLines();
      this.updateForViewport();
      this.lineGaps = this.ensureLineGaps([]);
      this.lineGapDeco = Decoration.set(this.lineGaps.map((gap) => gap.draw(false)));
      this.computeVisibleRanges();
    }
    updateForViewport() {
      let viewports = [this.viewport], { main } = this.state.selection;
      for (let i = 0; i <= 1; i++) {
        let pos = i ? main.head : main.anchor;
        if (!viewports.some(({ from: from2, to }) => pos >= from2 && pos <= to)) {
          let { from: from2, to } = this.lineBlockAt(pos);
          viewports.push(new Viewport(from2, to));
        }
      }
      this.viewports = viewports.sort((a, b) => a.from - b.from);
      this.scaler = this.heightMap.height <= 7e6 ? IdScaler : new BigScaler(this.heightOracle.doc, this.heightMap, this.viewports);
    }
    updateViewportLines() {
      this.viewportLines = [];
      this.heightMap.forEachLine(this.viewport.from, this.viewport.to, this.state.doc, 0, 0, (block) => {
        this.viewportLines.push(this.scaler.scale == 1 ? block : scaleBlock(block, this.scaler));
      });
    }
    update(update, scrollTarget = null) {
      let prev = this.state;
      this.state = update.state;
      let newDeco = this.state.facet(decorations);
      let contentChanges = update.changedRanges;
      let heightChanges = ChangedRange.extendWithRanges(contentChanges, heightRelevantDecoChanges(update.startState.facet(decorations), newDeco, update ? update.changes : ChangeSet.empty(this.state.doc.length)));
      let prevHeight = this.heightMap.height;
      this.heightMap = this.heightMap.applyChanges(newDeco, prev.doc, this.heightOracle.setDoc(this.state.doc), heightChanges);
      if (this.heightMap.height != prevHeight)
        update.flags |= 2;
      let viewport = heightChanges.length ? this.mapViewport(this.viewport, update.changes) : this.viewport;
      if (scrollTarget && (scrollTarget.range.head < viewport.from || scrollTarget.range.head > viewport.to) || !this.viewportIsAppropriate(viewport))
        viewport = this.getViewport(0, scrollTarget);
      let updateLines = !update.changes.empty || update.flags & 2 || viewport.from != this.viewport.from || viewport.to != this.viewport.to;
      this.viewport = viewport;
      this.updateForViewport();
      if (updateLines)
        this.updateViewportLines();
      if (this.lineGaps.length || this.viewport.to - this.viewport.from > 4e3)
        this.updateLineGaps(this.ensureLineGaps(this.mapLineGaps(this.lineGaps, update.changes)));
      update.flags |= this.computeVisibleRanges();
      if (scrollTarget)
        this.scrollTarget = scrollTarget;
      if (!this.mustEnforceCursorAssoc && update.selectionSet && update.view.lineWrapping && update.state.selection.main.empty && update.state.selection.main.assoc)
        this.mustEnforceCursorAssoc = true;
    }
    measure(view) {
      let dom = view.contentDOM, style = window.getComputedStyle(dom);
      let oracle = this.heightOracle;
      let whiteSpace = style.whiteSpace, direction = style.direction == "rtl" ? Direction.RTL : Direction.LTR;
      let refresh = this.heightOracle.mustRefreshForStyle(whiteSpace, direction);
      let measureContent = refresh || this.mustMeasureContent || this.contentDOMHeight != dom.clientHeight;
      let result = 0, bias = 0;
      if (this.editorWidth != view.scrollDOM.clientWidth) {
        if (oracle.lineWrapping)
          measureContent = true;
        this.editorWidth = view.scrollDOM.clientWidth;
        result |= 8;
      }
      if (measureContent) {
        this.mustMeasureContent = false;
        this.contentDOMHeight = dom.clientHeight;
        let paddingTop = parseInt(style.paddingTop) || 0, paddingBottom = parseInt(style.paddingBottom) || 0;
        if (this.paddingTop != paddingTop || this.paddingBottom != paddingBottom) {
          result |= 8;
          this.paddingTop = paddingTop;
          this.paddingBottom = paddingBottom;
        }
      }
      let pixelViewport = (this.printing ? fullPixelRange : visiblePixelRange)(dom, this.paddingTop);
      let dTop = pixelViewport.top - this.pixelViewport.top, dBottom = pixelViewport.bottom - this.pixelViewport.bottom;
      this.pixelViewport = pixelViewport;
      let inView = this.pixelViewport.bottom > this.pixelViewport.top && this.pixelViewport.right > this.pixelViewport.left;
      if (inView != this.inView) {
        this.inView = inView;
        if (inView)
          measureContent = true;
      }
      if (!this.inView)
        return 0;
      let contentWidth = dom.clientWidth;
      if (this.contentDOMWidth != contentWidth || this.editorHeight != view.scrollDOM.clientHeight) {
        this.contentDOMWidth = contentWidth;
        this.editorHeight = view.scrollDOM.clientHeight;
        result |= 8;
      }
      if (measureContent) {
        let lineHeights = view.docView.measureVisibleLineHeights();
        if (oracle.mustRefreshForHeights(lineHeights))
          refresh = true;
        if (refresh || oracle.lineWrapping && Math.abs(contentWidth - this.contentDOMWidth) > oracle.charWidth) {
          let { lineHeight, charWidth } = view.docView.measureTextSize();
          refresh = oracle.refresh(whiteSpace, direction, lineHeight, charWidth, contentWidth / charWidth, lineHeights);
          if (refresh) {
            view.docView.minWidth = 0;
            result |= 8;
          }
        }
        if (dTop > 0 && dBottom > 0)
          bias = Math.max(dTop, dBottom);
        else if (dTop < 0 && dBottom < 0)
          bias = Math.min(dTop, dBottom);
        oracle.heightChanged = false;
        this.heightMap = this.heightMap.updateHeight(oracle, 0, refresh, new MeasuredHeights(this.viewport.from, lineHeights));
        if (oracle.heightChanged)
          result |= 2;
      }
      let viewportChange = !this.viewportIsAppropriate(this.viewport, bias) || this.scrollTarget && (this.scrollTarget.range.head < this.viewport.from || this.scrollTarget.range.head > this.viewport.to);
      if (viewportChange)
        this.viewport = this.getViewport(bias, this.scrollTarget);
      this.updateForViewport();
      if (result & 2 || viewportChange)
        this.updateViewportLines();
      if (this.lineGaps.length || this.viewport.to - this.viewport.from > 4e3)
        this.updateLineGaps(this.ensureLineGaps(refresh ? [] : this.lineGaps));
      result |= this.computeVisibleRanges();
      if (this.mustEnforceCursorAssoc) {
        this.mustEnforceCursorAssoc = false;
        view.docView.enforceCursorAssoc();
      }
      return result;
    }
    get visibleTop() {
      return this.scaler.fromDOM(this.pixelViewport.top);
    }
    get visibleBottom() {
      return this.scaler.fromDOM(this.pixelViewport.bottom);
    }
    getViewport(bias, scrollTarget) {
      let marginTop = 0.5 - Math.max(-0.5, Math.min(0.5, bias / 1e3 / 2));
      let map = this.heightMap, doc2 = this.state.doc, { visibleTop, visibleBottom } = this;
      let viewport = new Viewport(map.lineAt(visibleTop - marginTop * 1e3, QueryType.ByHeight, doc2, 0, 0).from, map.lineAt(visibleBottom + (1 - marginTop) * 1e3, QueryType.ByHeight, doc2, 0, 0).to);
      if (scrollTarget) {
        let { head } = scrollTarget.range;
        if (head < viewport.from || head > viewport.to) {
          let viewHeight = Math.min(this.editorHeight, this.pixelViewport.bottom - this.pixelViewport.top);
          let block = map.lineAt(head, QueryType.ByPos, doc2, 0, 0), topPos;
          if (scrollTarget.y == "center")
            topPos = (block.top + block.bottom) / 2 - viewHeight / 2;
          else if (scrollTarget.y == "start" || scrollTarget.y == "nearest" && head < viewport.from)
            topPos = block.top;
          else
            topPos = block.bottom - viewHeight;
          viewport = new Viewport(map.lineAt(topPos - 1e3 / 2, QueryType.ByHeight, doc2, 0, 0).from, map.lineAt(topPos + viewHeight + 1e3 / 2, QueryType.ByHeight, doc2, 0, 0).to);
        }
      }
      return viewport;
    }
    mapViewport(viewport, changes) {
      let from2 = changes.mapPos(viewport.from, -1), to = changes.mapPos(viewport.to, 1);
      return new Viewport(this.heightMap.lineAt(from2, QueryType.ByPos, this.state.doc, 0, 0).from, this.heightMap.lineAt(to, QueryType.ByPos, this.state.doc, 0, 0).to);
    }
    viewportIsAppropriate({ from: from2, to }, bias = 0) {
      if (!this.inView)
        return true;
      let { top: top2 } = this.heightMap.lineAt(from2, QueryType.ByPos, this.state.doc, 0, 0);
      let { bottom } = this.heightMap.lineAt(to, QueryType.ByPos, this.state.doc, 0, 0);
      let { visibleTop, visibleBottom } = this;
      return (from2 == 0 || top2 <= visibleTop - Math.max(10, Math.min(-bias, 250))) && (to == this.state.doc.length || bottom >= visibleBottom + Math.max(10, Math.min(bias, 250))) && (top2 > visibleTop - 2 * 1e3 && bottom < visibleBottom + 2 * 1e3);
    }
    mapLineGaps(gaps, changes) {
      if (!gaps.length || changes.empty)
        return gaps;
      let mapped = [];
      for (let gap of gaps)
        if (!changes.touchesRange(gap.from, gap.to))
          mapped.push(new LineGap(changes.mapPos(gap.from), changes.mapPos(gap.to), gap.size));
      return mapped;
    }
    ensureLineGaps(current) {
      let gaps = [];
      if (this.heightOracle.direction != Direction.LTR)
        return gaps;
      for (let line of this.viewportLines) {
        if (line.length < 4e3)
          continue;
        let structure = lineStructure(line.from, line.to, this.state);
        if (structure.total < 4e3)
          continue;
        let viewFrom, viewTo;
        if (this.heightOracle.lineWrapping) {
          let marginHeight = 2e3 / this.heightOracle.lineLength * this.heightOracle.lineHeight;
          viewFrom = findPosition(structure, (this.visibleTop - line.top - marginHeight) / line.height);
          viewTo = findPosition(structure, (this.visibleBottom - line.top + marginHeight) / line.height);
        } else {
          let totalWidth = structure.total * this.heightOracle.charWidth;
          let marginWidth = 2e3 * this.heightOracle.charWidth;
          viewFrom = findPosition(structure, (this.pixelViewport.left - marginWidth) / totalWidth);
          viewTo = findPosition(structure, (this.pixelViewport.right + marginWidth) / totalWidth);
        }
        let outside = [];
        if (viewFrom > line.from)
          outside.push({ from: line.from, to: viewFrom });
        if (viewTo < line.to)
          outside.push({ from: viewTo, to: line.to });
        let sel = this.state.selection.main;
        if (sel.from >= line.from && sel.from <= line.to)
          cutRange(outside, sel.from - 10, sel.from + 10);
        if (!sel.empty && sel.to >= line.from && sel.to <= line.to)
          cutRange(outside, sel.to - 10, sel.to + 10);
        for (let { from: from2, to } of outside)
          if (to - from2 > 1e3) {
            gaps.push(find(current, (gap) => gap.from >= line.from && gap.to <= line.to && Math.abs(gap.from - from2) < 1e3 && Math.abs(gap.to - to) < 1e3) || new LineGap(from2, to, this.gapSize(line, from2, to, structure)));
          }
      }
      return gaps;
    }
    gapSize(line, from2, to, structure) {
      let fraction = findFraction(structure, to) - findFraction(structure, from2);
      if (this.heightOracle.lineWrapping) {
        return line.height * fraction;
      } else {
        return structure.total * this.heightOracle.charWidth * fraction;
      }
    }
    updateLineGaps(gaps) {
      if (!LineGap.same(gaps, this.lineGaps)) {
        this.lineGaps = gaps;
        this.lineGapDeco = Decoration.set(gaps.map((gap) => gap.draw(this.heightOracle.lineWrapping)));
      }
    }
    computeVisibleRanges() {
      let deco = this.state.facet(decorations);
      if (this.lineGaps.length)
        deco = deco.concat(this.lineGapDeco);
      let ranges = [];
      RangeSet.spans(deco, this.viewport.from, this.viewport.to, {
        span(from2, to) {
          ranges.push({ from: from2, to });
        },
        point() {
        }
      }, 20);
      let changed = ranges.length != this.visibleRanges.length || this.visibleRanges.some((r, i) => r.from != ranges[i].from || r.to != ranges[i].to);
      this.visibleRanges = ranges;
      return changed ? 4 : 0;
    }
    lineBlockAt(pos) {
      return pos >= this.viewport.from && pos <= this.viewport.to && this.viewportLines.find((b) => b.from <= pos && b.to >= pos) || scaleBlock(this.heightMap.lineAt(pos, QueryType.ByPos, this.state.doc, 0, 0), this.scaler);
    }
    lineBlockAtHeight(height) {
      return scaleBlock(this.heightMap.lineAt(this.scaler.fromDOM(height), QueryType.ByHeight, this.state.doc, 0, 0), this.scaler);
    }
    elementAtHeight(height) {
      return scaleBlock(this.heightMap.blockAt(this.scaler.fromDOM(height), this.state.doc, 0, 0), this.scaler);
    }
    get docHeight() {
      return this.scaler.toDOM(this.heightMap.height);
    }
    get contentHeight() {
      return this.docHeight + this.paddingTop + this.paddingBottom;
    }
  };
  var Viewport = class {
    constructor(from2, to) {
      this.from = from2;
      this.to = to;
    }
  };
  function lineStructure(from2, to, state) {
    let ranges = [], pos = from2, total = 0;
    RangeSet.spans(state.facet(decorations), from2, to, {
      span() {
      },
      point(from3, to2) {
        if (from3 > pos) {
          ranges.push({ from: pos, to: from3 });
          total += from3 - pos;
        }
        pos = to2;
      }
    }, 20);
    if (pos < to) {
      ranges.push({ from: pos, to });
      total += to - pos;
    }
    return { total, ranges };
  }
  function findPosition({ total, ranges }, ratio) {
    if (ratio <= 0)
      return ranges[0].from;
    if (ratio >= 1)
      return ranges[ranges.length - 1].to;
    let dist = Math.floor(total * ratio);
    for (let i = 0; ; i++) {
      let { from: from2, to } = ranges[i], size = to - from2;
      if (dist <= size)
        return from2 + dist;
      dist -= size;
    }
  }
  function findFraction(structure, pos) {
    let counted = 0;
    for (let { from: from2, to } of structure.ranges) {
      if (pos <= to) {
        counted += pos - from2;
        break;
      }
      counted += to - from2;
    }
    return counted / structure.total;
  }
  function cutRange(ranges, from2, to) {
    for (let i = 0; i < ranges.length; i++) {
      let r = ranges[i];
      if (r.from < to && r.to > from2) {
        let pieces = [];
        if (r.from < from2)
          pieces.push({ from: r.from, to: from2 });
        if (r.to > to)
          pieces.push({ from: to, to: r.to });
        ranges.splice(i, 1, ...pieces);
        i += pieces.length - 1;
      }
    }
  }
  function find(array2, f) {
    for (let val of array2)
      if (f(val))
        return val;
    return void 0;
  }
  var IdScaler = {
    toDOM(n) {
      return n;
    },
    fromDOM(n) {
      return n;
    },
    scale: 1
  };
  var BigScaler = class {
    constructor(doc2, heightMap, viewports) {
      let vpHeight = 0, base2 = 0, domBase = 0;
      this.viewports = viewports.map(({ from: from2, to }) => {
        let top2 = heightMap.lineAt(from2, QueryType.ByPos, doc2, 0, 0).top;
        let bottom = heightMap.lineAt(to, QueryType.ByPos, doc2, 0, 0).bottom;
        vpHeight += bottom - top2;
        return { from: from2, to, top: top2, bottom, domTop: 0, domBottom: 0 };
      });
      this.scale = (7e6 - vpHeight) / (heightMap.height - vpHeight);
      for (let obj of this.viewports) {
        obj.domTop = domBase + (obj.top - base2) * this.scale;
        domBase = obj.domBottom = obj.domTop + (obj.bottom - obj.top);
        base2 = obj.bottom;
      }
    }
    toDOM(n) {
      for (let i = 0, base2 = 0, domBase = 0; ; i++) {
        let vp = i < this.viewports.length ? this.viewports[i] : null;
        if (!vp || n < vp.top)
          return domBase + (n - base2) * this.scale;
        if (n <= vp.bottom)
          return vp.domTop + (n - vp.top);
        base2 = vp.bottom;
        domBase = vp.domBottom;
      }
    }
    fromDOM(n) {
      for (let i = 0, base2 = 0, domBase = 0; ; i++) {
        let vp = i < this.viewports.length ? this.viewports[i] : null;
        if (!vp || n < vp.domTop)
          return base2 + (n - domBase) / this.scale;
        if (n <= vp.domBottom)
          return vp.top + (n - vp.domTop);
        base2 = vp.bottom;
        domBase = vp.domBottom;
      }
    }
  };
  function scaleBlock(block, scaler) {
    if (scaler.scale == 1)
      return block;
    let bTop = scaler.toDOM(block.top), bBottom = scaler.toDOM(block.bottom);
    return new BlockInfo(block.from, block.length, bTop, bBottom - bTop, Array.isArray(block.type) ? block.type.map((b) => scaleBlock(b, scaler)) : block.type);
  }
  var theme = /* @__PURE__ */ Facet.define({ combine: (strs) => strs.join(" ") });
  var darkTheme = /* @__PURE__ */ Facet.define({ combine: (values2) => values2.indexOf(true) > -1 });
  var baseThemeID = /* @__PURE__ */ StyleModule.newName();
  var baseLightID = /* @__PURE__ */ StyleModule.newName();
  var baseDarkID = /* @__PURE__ */ StyleModule.newName();
  var lightDarkIDs = { "&light": "." + baseLightID, "&dark": "." + baseDarkID };
  function buildTheme(main, spec, scopes) {
    return new StyleModule(spec, {
      finish(sel) {
        return /&/.test(sel) ? sel.replace(/&\w*/, (m) => {
          if (m == "&")
            return main;
          if (!scopes || !scopes[m])
            throw new RangeError(`Unsupported selector: ${m}`);
          return scopes[m];
        }) : main + " " + sel;
      }
    });
  }
  var baseTheme = /* @__PURE__ */ buildTheme("." + baseThemeID, {
    "&.cm-editor": {
      position: "relative !important",
      boxSizing: "border-box",
      "&.cm-focused": {
        outline: "1px dotted #212121"
      },
      display: "flex !important",
      flexDirection: "column"
    },
    ".cm-scroller": {
      display: "flex !important",
      alignItems: "flex-start !important",
      fontFamily: "monospace",
      lineHeight: 1.4,
      height: "100%",
      overflowX: "auto",
      position: "relative",
      zIndex: 0
    },
    ".cm-content": {
      margin: 0,
      flexGrow: 2,
      minHeight: "100%",
      display: "block",
      whiteSpace: "pre",
      wordWrap: "normal",
      boxSizing: "border-box",
      padding: "4px 0",
      outline: "none",
      "&[contenteditable=true]": {
        WebkitUserModify: "read-write-plaintext-only"
      }
    },
    ".cm-lineWrapping": {
      whiteSpace_fallback: "pre-wrap",
      whiteSpace: "break-spaces",
      wordBreak: "break-word",
      overflowWrap: "anywhere"
    },
    "&light .cm-content": { caretColor: "black" },
    "&dark .cm-content": { caretColor: "white" },
    ".cm-line": {
      display: "block",
      padding: "0 2px 0 4px"
    },
    ".cm-selectionLayer": {
      zIndex: -1,
      contain: "size style"
    },
    ".cm-selectionBackground": {
      position: "absolute"
    },
    "&light .cm-selectionBackground": {
      background: "#d9d9d9"
    },
    "&dark .cm-selectionBackground": {
      background: "#222"
    },
    "&light.cm-focused .cm-selectionBackground": {
      background: "#d7d4f0"
    },
    "&dark.cm-focused .cm-selectionBackground": {
      background: "#233"
    },
    ".cm-cursorLayer": {
      zIndex: 100,
      contain: "size style",
      pointerEvents: "none"
    },
    "&.cm-focused .cm-cursorLayer": {
      animation: "steps(1) cm-blink 1.2s infinite"
    },
    "@keyframes cm-blink": { "0%": {}, "50%": { visibility: "hidden" }, "100%": {} },
    "@keyframes cm-blink2": { "0%": {}, "50%": { visibility: "hidden" }, "100%": {} },
    ".cm-cursor, .cm-dropCursor": {
      position: "absolute",
      borderLeft: "1.2px solid black",
      marginLeft: "-0.6px",
      pointerEvents: "none"
    },
    ".cm-cursor": {
      display: "none"
    },
    "&dark .cm-cursor": {
      borderLeftColor: "#444"
    },
    "&.cm-focused .cm-cursor": {
      display: "block"
    },
    "&light .cm-activeLine": { backgroundColor: "#f3f9ff" },
    "&dark .cm-activeLine": { backgroundColor: "#223039" },
    "&light .cm-specialChar": { color: "red" },
    "&dark .cm-specialChar": { color: "#f78" },
    ".cm-tab": {
      display: "inline-block",
      overflow: "hidden",
      verticalAlign: "bottom"
    },
    ".cm-widgetBuffer": {
      verticalAlign: "text-top",
      height: "1em",
      display: "inline"
    },
    ".cm-placeholder": {
      color: "#888",
      display: "inline-block",
      verticalAlign: "top"
    },
    ".cm-button": {
      verticalAlign: "middle",
      color: "inherit",
      fontSize: "70%",
      padding: ".2em 1em",
      borderRadius: "1px"
    },
    "&light .cm-button": {
      backgroundImage: "linear-gradient(#eff1f5, #d9d9df)",
      border: "1px solid #888",
      "&:active": {
        backgroundImage: "linear-gradient(#b4b4b4, #d0d3d6)"
      }
    },
    "&dark .cm-button": {
      backgroundImage: "linear-gradient(#393939, #111)",
      border: "1px solid #888",
      "&:active": {
        backgroundImage: "linear-gradient(#111, #333)"
      }
    },
    ".cm-textfield": {
      verticalAlign: "middle",
      color: "inherit",
      fontSize: "70%",
      border: "1px solid silver",
      padding: ".2em .5em"
    },
    "&light .cm-textfield": {
      backgroundColor: "white"
    },
    "&dark .cm-textfield": {
      border: "1px solid #555",
      backgroundColor: "inherit"
    }
  }, lightDarkIDs);
  var observeOptions = {
    childList: true,
    characterData: true,
    subtree: true,
    attributes: true,
    characterDataOldValue: true
  };
  var useCharData = browser.ie && browser.ie_version <= 11;
  var DOMObserver = class {
    constructor(view, onChange, onScrollChanged) {
      this.view = view;
      this.onChange = onChange;
      this.onScrollChanged = onScrollChanged;
      this.active = false;
      this.selectionRange = new DOMSelectionState();
      this.selectionChanged = false;
      this.delayedFlush = -1;
      this.resizeTimeout = -1;
      this.queue = [];
      this.delayedAndroidKey = null;
      this.scrollTargets = [];
      this.intersection = null;
      this.resize = null;
      this.intersecting = false;
      this.gapIntersection = null;
      this.gaps = [];
      this.parentCheck = -1;
      this.dom = view.contentDOM;
      this.observer = new MutationObserver((mutations) => {
        for (let mut of mutations)
          this.queue.push(mut);
        if ((browser.ie && browser.ie_version <= 11 || browser.ios && view.composing) && mutations.some((m) => m.type == "childList" && m.removedNodes.length || m.type == "characterData" && m.oldValue.length > m.target.nodeValue.length))
          this.flushSoon();
        else
          this.flush();
      });
      if (useCharData)
        this.onCharData = (event) => {
          this.queue.push({
            target: event.target,
            type: "characterData",
            oldValue: event.prevValue
          });
          this.flushSoon();
        };
      this.onSelectionChange = this.onSelectionChange.bind(this);
      window.addEventListener("resize", this.onResize = this.onResize.bind(this));
      if (typeof ResizeObserver == "function") {
        this.resize = new ResizeObserver(() => {
          if (this.view.docView.lastUpdate < Date.now() - 75)
            this.onResize();
        });
        this.resize.observe(view.scrollDOM);
      }
      window.addEventListener("beforeprint", this.onPrint = this.onPrint.bind(this));
      this.start();
      window.addEventListener("scroll", this.onScroll = this.onScroll.bind(this));
      if (typeof IntersectionObserver == "function") {
        this.intersection = new IntersectionObserver((entries) => {
          if (this.parentCheck < 0)
            this.parentCheck = setTimeout(this.listenForScroll.bind(this), 1e3);
          if (entries.length > 0 && entries[entries.length - 1].intersectionRatio > 0 != this.intersecting) {
            this.intersecting = !this.intersecting;
            if (this.intersecting != this.view.inView)
              this.onScrollChanged(document.createEvent("Event"));
          }
        }, {});
        this.intersection.observe(this.dom);
        this.gapIntersection = new IntersectionObserver((entries) => {
          if (entries.length > 0 && entries[entries.length - 1].intersectionRatio > 0)
            this.onScrollChanged(document.createEvent("Event"));
        }, {});
      }
      this.listenForScroll();
      this.readSelectionRange();
      this.dom.ownerDocument.addEventListener("selectionchange", this.onSelectionChange);
    }
    onScroll(e) {
      if (this.intersecting)
        this.flush(false);
      this.onScrollChanged(e);
    }
    onResize() {
      if (this.resizeTimeout < 0)
        this.resizeTimeout = setTimeout(() => {
          this.resizeTimeout = -1;
          this.view.requestMeasure();
        }, 50);
    }
    onPrint() {
      this.view.viewState.printing = true;
      this.view.measure();
      setTimeout(() => {
        this.view.viewState.printing = false;
        this.view.requestMeasure();
      }, 500);
    }
    updateGaps(gaps) {
      if (this.gapIntersection && (gaps.length != this.gaps.length || this.gaps.some((g, i) => g != gaps[i]))) {
        this.gapIntersection.disconnect();
        for (let gap of gaps)
          this.gapIntersection.observe(gap);
        this.gaps = gaps;
      }
    }
    onSelectionChange(event) {
      if (!this.readSelectionRange() || this.delayedAndroidKey)
        return;
      let { view } = this, sel = this.selectionRange;
      if (view.state.facet(editable) ? view.root.activeElement != this.dom : !hasSelection(view.dom, sel))
        return;
      let context = sel.anchorNode && view.docView.nearest(sel.anchorNode);
      if (context && context.ignoreEvent(event))
        return;
      if ((browser.ie && browser.ie_version <= 11 || browser.android && browser.chrome) && !view.state.selection.main.empty && sel.focusNode && isEquivalentPosition(sel.focusNode, sel.focusOffset, sel.anchorNode, sel.anchorOffset))
        this.flushSoon();
      else
        this.flush(false);
    }
    readSelectionRange() {
      let { root } = this.view, domSel = getSelection(root);
      let range = browser.safari && root.nodeType == 11 && deepActiveElement() == this.view.contentDOM && safariSelectionRangeHack(this.view) || domSel;
      if (this.selectionRange.eq(range))
        return false;
      this.selectionRange.setRange(range);
      return this.selectionChanged = true;
    }
    setSelectionRange(anchor, head) {
      this.selectionRange.set(anchor.node, anchor.offset, head.node, head.offset);
      this.selectionChanged = false;
    }
    listenForScroll() {
      this.parentCheck = -1;
      let i = 0, changed = null;
      for (let dom = this.dom; dom; ) {
        if (dom.nodeType == 1) {
          if (!changed && i < this.scrollTargets.length && this.scrollTargets[i] == dom)
            i++;
          else if (!changed)
            changed = this.scrollTargets.slice(0, i);
          if (changed)
            changed.push(dom);
          dom = dom.assignedSlot || dom.parentNode;
        } else if (dom.nodeType == 11) {
          dom = dom.host;
        } else {
          break;
        }
      }
      if (i < this.scrollTargets.length && !changed)
        changed = this.scrollTargets.slice(0, i);
      if (changed) {
        for (let dom of this.scrollTargets)
          dom.removeEventListener("scroll", this.onScroll);
        for (let dom of this.scrollTargets = changed)
          dom.addEventListener("scroll", this.onScroll);
      }
    }
    ignore(f) {
      if (!this.active)
        return f();
      try {
        this.stop();
        return f();
      } finally {
        this.start();
        this.clear();
      }
    }
    start() {
      if (this.active)
        return;
      this.observer.observe(this.dom, observeOptions);
      if (useCharData)
        this.dom.addEventListener("DOMCharacterDataModified", this.onCharData);
      this.active = true;
    }
    stop() {
      if (!this.active)
        return;
      this.active = false;
      this.observer.disconnect();
      if (useCharData)
        this.dom.removeEventListener("DOMCharacterDataModified", this.onCharData);
    }
    clear() {
      this.processRecords();
      this.queue.length = 0;
      this.selectionChanged = false;
    }
    delayAndroidKey(key, keyCode) {
      if (!this.delayedAndroidKey)
        requestAnimationFrame(() => {
          let key2 = this.delayedAndroidKey;
          this.delayedAndroidKey = null;
          let startState = this.view.state;
          if (dispatchKey(this.view.contentDOM, key2.key, key2.keyCode))
            this.processRecords();
          else
            this.flush();
          if (this.view.state == startState)
            this.view.update([]);
        });
      if (!this.delayedAndroidKey || key == "Enter")
        this.delayedAndroidKey = { key, keyCode };
    }
    flushSoon() {
      if (this.delayedFlush < 0)
        this.delayedFlush = window.setTimeout(() => {
          this.delayedFlush = -1;
          this.flush();
        }, 20);
    }
    forceFlush() {
      if (this.delayedFlush >= 0) {
        window.clearTimeout(this.delayedFlush);
        this.delayedFlush = -1;
        this.flush();
      }
    }
    processRecords() {
      let records = this.queue;
      for (let mut of this.observer.takeRecords())
        records.push(mut);
      if (records.length)
        this.queue = [];
      let from2 = -1, to = -1, typeOver = false;
      for (let record of records) {
        let range = this.readMutation(record);
        if (!range)
          continue;
        if (range.typeOver)
          typeOver = true;
        if (from2 == -1) {
          ({ from: from2, to } = range);
        } else {
          from2 = Math.min(range.from, from2);
          to = Math.max(range.to, to);
        }
      }
      return { from: from2, to, typeOver };
    }
    flush(readSelection = true) {
      if (this.delayedFlush >= 0 || this.delayedAndroidKey)
        return;
      if (readSelection)
        this.readSelectionRange();
      let { from: from2, to, typeOver } = this.processRecords();
      let newSel = this.selectionChanged && hasSelection(this.dom, this.selectionRange);
      if (from2 < 0 && !newSel)
        return;
      this.selectionChanged = false;
      let startState = this.view.state;
      this.onChange(from2, to, typeOver);
      if (this.view.state == startState)
        this.view.update([]);
    }
    readMutation(rec) {
      let cView = this.view.docView.nearest(rec.target);
      if (!cView || cView.ignoreMutation(rec))
        return null;
      cView.markDirty(rec.type == "attributes");
      if (rec.type == "attributes")
        cView.dirty |= 4;
      if (rec.type == "childList") {
        let childBefore = findChild(cView, rec.previousSibling || rec.target.previousSibling, -1);
        let childAfter = findChild(cView, rec.nextSibling || rec.target.nextSibling, 1);
        return {
          from: childBefore ? cView.posAfter(childBefore) : cView.posAtStart,
          to: childAfter ? cView.posBefore(childAfter) : cView.posAtEnd,
          typeOver: false
        };
      } else if (rec.type == "characterData") {
        return { from: cView.posAtStart, to: cView.posAtEnd, typeOver: rec.target.nodeValue == rec.oldValue };
      } else {
        return null;
      }
    }
    destroy() {
      var _a2, _b, _c;
      this.stop();
      (_a2 = this.intersection) === null || _a2 === void 0 ? void 0 : _a2.disconnect();
      (_b = this.gapIntersection) === null || _b === void 0 ? void 0 : _b.disconnect();
      (_c = this.resize) === null || _c === void 0 ? void 0 : _c.disconnect();
      for (let dom of this.scrollTargets)
        dom.removeEventListener("scroll", this.onScroll);
      window.removeEventListener("scroll", this.onScroll);
      window.removeEventListener("resize", this.onResize);
      window.removeEventListener("beforeprint", this.onPrint);
      this.dom.ownerDocument.removeEventListener("selectionchange", this.onSelectionChange);
      clearTimeout(this.parentCheck);
      clearTimeout(this.resizeTimeout);
    }
  };
  function findChild(cView, dom, dir) {
    while (dom) {
      let curView = ContentView.get(dom);
      if (curView && curView.parent == cView)
        return curView;
      let parent = dom.parentNode;
      dom = parent != cView.dom ? parent : dir > 0 ? dom.nextSibling : dom.previousSibling;
    }
    return null;
  }
  function safariSelectionRangeHack(view) {
    let found = null;
    function read(event) {
      event.preventDefault();
      event.stopImmediatePropagation();
      found = event.getTargetRanges()[0];
    }
    view.contentDOM.addEventListener("beforeinput", read, true);
    document.execCommand("indent");
    view.contentDOM.removeEventListener("beforeinput", read, true);
    if (!found)
      return null;
    let anchorNode = found.startContainer, anchorOffset = found.startOffset;
    let focusNode = found.endContainer, focusOffset = found.endOffset;
    let curAnchor = view.docView.domAtPos(view.state.selection.main.anchor);
    if (isEquivalentPosition(curAnchor.node, curAnchor.offset, focusNode, focusOffset))
      [anchorNode, anchorOffset, focusNode, focusOffset] = [focusNode, focusOffset, anchorNode, anchorOffset];
    return { anchorNode, anchorOffset, focusNode, focusOffset };
  }
  function applyDOMChange(view, start, end, typeOver) {
    let change, newSel;
    let sel = view.state.selection.main;
    if (start > -1) {
      let bounds = view.docView.domBoundsAround(start, end, 0);
      if (!bounds || view.state.readOnly)
        return;
      let { from: from2, to } = bounds;
      let selPoints = view.docView.impreciseHead || view.docView.impreciseAnchor ? [] : selectionPoints(view);
      let reader = new DOMReader(selPoints, view.state);
      reader.readRange(bounds.startDOM, bounds.endDOM);
      let preferredPos = sel.from, preferredSide = null;
      if (view.inputState.lastKeyCode === 8 && view.inputState.lastKeyTime > Date.now() - 100 || browser.android && reader.text.length < to - from2) {
        preferredPos = sel.to;
        preferredSide = "end";
      }
      let diff = findDiff(view.state.doc.sliceString(from2, to, LineBreakPlaceholder), reader.text, preferredPos - from2, preferredSide);
      if (diff) {
        if (browser.chrome && view.inputState.lastKeyCode == 13 && diff.toB == diff.from + 2 && reader.text.slice(diff.from, diff.toB) == LineBreakPlaceholder + LineBreakPlaceholder)
          diff.toB--;
        change = {
          from: from2 + diff.from,
          to: from2 + diff.toA,
          insert: Text.of(reader.text.slice(diff.from, diff.toB).split(LineBreakPlaceholder))
        };
      }
      newSel = selectionFromPoints(selPoints, from2);
    } else if (view.hasFocus || !view.state.facet(editable)) {
      let domSel = view.observer.selectionRange;
      let { impreciseHead: iHead, impreciseAnchor: iAnchor } = view.docView;
      let head = iHead && iHead.node == domSel.focusNode && iHead.offset == domSel.focusOffset || !contains(view.contentDOM, domSel.focusNode) ? view.state.selection.main.head : view.docView.posFromDOM(domSel.focusNode, domSel.focusOffset);
      let anchor = iAnchor && iAnchor.node == domSel.anchorNode && iAnchor.offset == domSel.anchorOffset || !contains(view.contentDOM, domSel.anchorNode) ? view.state.selection.main.anchor : view.docView.posFromDOM(domSel.anchorNode, domSel.anchorOffset);
      if (head != sel.head || anchor != sel.anchor)
        newSel = EditorSelection.single(anchor, head);
    }
    if (!change && !newSel)
      return;
    if (!change && typeOver && !sel.empty && newSel && newSel.main.empty)
      change = { from: sel.from, to: sel.to, insert: view.state.doc.slice(sel.from, sel.to) };
    else if (change && change.from >= sel.from && change.to <= sel.to && (change.from != sel.from || change.to != sel.to) && sel.to - sel.from - (change.to - change.from) <= 4)
      change = {
        from: sel.from,
        to: sel.to,
        insert: view.state.doc.slice(sel.from, change.from).append(change.insert).append(view.state.doc.slice(change.to, sel.to))
      };
    if (change) {
      let startState = view.state;
      if (browser.ios && view.inputState.flushIOSKey(view))
        return;
      if (browser.android && (change.from == sel.from && change.to == sel.to && change.insert.length == 1 && change.insert.lines == 2 && dispatchKey(view.contentDOM, "Enter", 13) || change.from == sel.from - 1 && change.to == sel.to && change.insert.length == 0 && dispatchKey(view.contentDOM, "Backspace", 8) || change.from == sel.from && change.to == sel.to + 1 && change.insert.length == 0 && dispatchKey(view.contentDOM, "Delete", 46)))
        return;
      let text = change.insert.toString();
      if (view.state.facet(inputHandler).some((h) => h(view, change.from, change.to, text)))
        return;
      if (view.inputState.composing >= 0)
        view.inputState.composing++;
      let tr;
      if (change.from >= sel.from && change.to <= sel.to && change.to - change.from >= (sel.to - sel.from) / 3 && (!newSel || newSel.main.empty && newSel.main.from == change.from + change.insert.length) && view.inputState.composing < 0) {
        let before = sel.from < change.from ? startState.sliceDoc(sel.from, change.from) : "";
        let after = sel.to > change.to ? startState.sliceDoc(change.to, sel.to) : "";
        tr = startState.replaceSelection(view.state.toText(before + change.insert.sliceString(0, void 0, view.state.lineBreak) + after));
      } else {
        let changes = startState.changes(change);
        let mainSel = newSel && !startState.selection.main.eq(newSel.main) && newSel.main.to <= changes.newLength ? newSel.main : void 0;
        if (startState.selection.ranges.length > 1 && view.inputState.composing >= 0 && change.to <= sel.to && change.to >= sel.to - 10) {
          let replaced = view.state.sliceDoc(change.from, change.to);
          let compositionRange = compositionSurroundingNode(view) || view.state.doc.lineAt(sel.head);
          let offset = sel.to - change.to, size = sel.to - sel.from;
          tr = startState.changeByRange((range) => {
            if (range.from == sel.from && range.to == sel.to)
              return { changes, range: mainSel || range.map(changes) };
            let to = range.to - offset, from2 = to - replaced.length;
            if (range.to - range.from != size || view.state.sliceDoc(from2, to) != replaced || compositionRange && range.to >= compositionRange.from && range.from <= compositionRange.to)
              return { range };
            let rangeChanges = startState.changes({ from: from2, to, insert: change.insert }), selOff = range.to - sel.to;
            return {
              changes: rangeChanges,
              range: !mainSel ? range.map(rangeChanges) : EditorSelection.range(Math.max(0, mainSel.anchor + selOff), Math.max(0, mainSel.head + selOff))
            };
          });
        } else {
          tr = {
            changes,
            selection: mainSel && startState.selection.replaceRange(mainSel)
          };
        }
      }
      let userEvent = "input.type";
      if (view.composing) {
        userEvent += ".compose";
        if (view.inputState.compositionFirstChange) {
          userEvent += ".start";
          view.inputState.compositionFirstChange = false;
        }
      }
      view.dispatch(tr, { scrollIntoView: true, userEvent });
    } else if (newSel && !newSel.main.eq(sel)) {
      let scrollIntoView3 = false, userEvent = "select";
      if (view.inputState.lastSelectionTime > Date.now() - 50) {
        if (view.inputState.lastSelectionOrigin == "select")
          scrollIntoView3 = true;
        userEvent = view.inputState.lastSelectionOrigin;
      }
      view.dispatch({ selection: newSel, scrollIntoView: scrollIntoView3, userEvent });
    }
  }
  function findDiff(a, b, preferredPos, preferredSide) {
    let minLen = Math.min(a.length, b.length);
    let from2 = 0;
    while (from2 < minLen && a.charCodeAt(from2) == b.charCodeAt(from2))
      from2++;
    if (from2 == minLen && a.length == b.length)
      return null;
    let toA = a.length, toB = b.length;
    while (toA > 0 && toB > 0 && a.charCodeAt(toA - 1) == b.charCodeAt(toB - 1)) {
      toA--;
      toB--;
    }
    if (preferredSide == "end") {
      let adjust = Math.max(0, from2 - Math.min(toA, toB));
      preferredPos -= toA + adjust - from2;
    }
    if (toA < from2 && a.length < b.length) {
      let move = preferredPos <= from2 && preferredPos >= toA ? from2 - preferredPos : 0;
      from2 -= move;
      toB = from2 + (toB - toA);
      toA = from2;
    } else if (toB < from2) {
      let move = preferredPos <= from2 && preferredPos >= toB ? from2 - preferredPos : 0;
      from2 -= move;
      toA = from2 + (toA - toB);
      toB = from2;
    }
    return { from: from2, toA, toB };
  }
  function selectionPoints(view) {
    let result = [];
    if (view.root.activeElement != view.contentDOM)
      return result;
    let { anchorNode, anchorOffset, focusNode, focusOffset } = view.observer.selectionRange;
    if (anchorNode) {
      result.push(new DOMPoint(anchorNode, anchorOffset));
      if (focusNode != anchorNode || focusOffset != anchorOffset)
        result.push(new DOMPoint(focusNode, focusOffset));
    }
    return result;
  }
  function selectionFromPoints(points, base2) {
    if (points.length == 0)
      return null;
    let anchor = points[0].pos, head = points.length == 2 ? points[1].pos : anchor;
    return anchor > -1 && head > -1 ? EditorSelection.single(anchor + base2, head + base2) : null;
  }
  var EditorView = class {
    constructor(config2 = {}) {
      this.plugins = [];
      this.pluginMap = /* @__PURE__ */ new Map();
      this.editorAttrs = {};
      this.contentAttrs = {};
      this.bidiCache = [];
      this.destroyed = false;
      this.updateState = 2;
      this.measureScheduled = -1;
      this.measureRequests = [];
      this.contentDOM = document.createElement("div");
      this.scrollDOM = document.createElement("div");
      this.scrollDOM.tabIndex = -1;
      this.scrollDOM.className = "cm-scroller";
      this.scrollDOM.appendChild(this.contentDOM);
      this.announceDOM = document.createElement("div");
      this.announceDOM.style.cssText = "position: absolute; top: -10000px";
      this.announceDOM.setAttribute("aria-live", "polite");
      this.dom = document.createElement("div");
      this.dom.appendChild(this.announceDOM);
      this.dom.appendChild(this.scrollDOM);
      this._dispatch = config2.dispatch || ((tr) => this.update([tr]));
      this.dispatch = this.dispatch.bind(this);
      this.root = config2.root || getRoot(config2.parent) || document;
      this.viewState = new ViewState(config2.state || EditorState.create());
      this.plugins = this.state.facet(viewPlugin).map((spec) => new PluginInstance(spec));
      for (let plugin of this.plugins)
        plugin.update(this);
      this.observer = new DOMObserver(this, (from2, to, typeOver) => {
        applyDOMChange(this, from2, to, typeOver);
      }, (event) => {
        this.inputState.runScrollHandlers(this, event);
        if (this.observer.intersecting)
          this.measure();
      });
      this.inputState = new InputState(this);
      this.docView = new DocView(this);
      this.mountStyles();
      this.updateAttrs();
      this.updateState = 0;
      this.requestMeasure();
      if (config2.parent)
        config2.parent.appendChild(this.dom);
    }
    get state() {
      return this.viewState.state;
    }
    get viewport() {
      return this.viewState.viewport;
    }
    get visibleRanges() {
      return this.viewState.visibleRanges;
    }
    get inView() {
      return this.viewState.inView;
    }
    get composing() {
      return this.inputState.composing > 0;
    }
    get compositionStarted() {
      return this.inputState.composing >= 0;
    }
    dispatch(...input) {
      this._dispatch(input.length == 1 && input[0] instanceof Transaction ? input[0] : this.state.update(...input));
    }
    update(transactions) {
      if (this.updateState != 0)
        throw new Error("Calls to EditorView.update are not allowed while an update is in progress");
      let redrawn = false, update;
      let state = this.state;
      for (let tr of transactions) {
        if (tr.startState != state)
          throw new RangeError("Trying to update state with a transaction that doesn't start from the previous state.");
        state = tr.state;
      }
      if (this.destroyed) {
        this.viewState.state = state;
        return;
      }
      if (state.facet(EditorState.phrases) != this.state.facet(EditorState.phrases))
        return this.setState(state);
      update = new ViewUpdate(this, state, transactions);
      let scrollTarget = this.viewState.scrollTarget;
      try {
        this.updateState = 2;
        for (let tr of transactions) {
          if (scrollTarget)
            scrollTarget = scrollTarget.map(tr.changes);
          if (tr.scrollIntoView) {
            let { main } = tr.state.selection;
            scrollTarget = new ScrollTarget(main.empty ? main : EditorSelection.cursor(main.head, main.head > main.anchor ? -1 : 1));
          }
          for (let e of tr.effects) {
            if (e.is(scrollTo))
              scrollTarget = new ScrollTarget(e.value);
            else if (e.is(centerOn))
              scrollTarget = new ScrollTarget(e.value, "center");
            else if (e.is(scrollIntoView))
              scrollTarget = e.value;
          }
        }
        this.viewState.update(update, scrollTarget);
        this.bidiCache = CachedOrder.update(this.bidiCache, update.changes);
        if (!update.empty) {
          this.updatePlugins(update);
          this.inputState.update(update);
        }
        redrawn = this.docView.update(update);
        if (this.state.facet(styleModule) != this.styleModules)
          this.mountStyles();
        this.updateAttrs();
        this.showAnnouncements(transactions);
        this.docView.updateSelection(redrawn, transactions.some((tr) => tr.isUserEvent("select.pointer")));
      } finally {
        this.updateState = 0;
      }
      if (update.startState.facet(theme) != update.state.facet(theme))
        this.viewState.mustMeasureContent = true;
      if (redrawn || scrollTarget || this.viewState.mustEnforceCursorAssoc || this.viewState.mustMeasureContent)
        this.requestMeasure();
      if (!update.empty)
        for (let listener of this.state.facet(updateListener))
          listener(update);
    }
    setState(newState) {
      if (this.updateState != 0)
        throw new Error("Calls to EditorView.setState are not allowed while an update is in progress");
      if (this.destroyed) {
        this.viewState.state = newState;
        return;
      }
      this.updateState = 2;
      let hadFocus = this.hasFocus;
      try {
        for (let plugin of this.plugins)
          plugin.destroy(this);
        this.viewState = new ViewState(newState);
        this.plugins = newState.facet(viewPlugin).map((spec) => new PluginInstance(spec));
        this.pluginMap.clear();
        for (let plugin of this.plugins)
          plugin.update(this);
        this.docView = new DocView(this);
        this.inputState.ensureHandlers(this);
        this.mountStyles();
        this.updateAttrs();
        this.bidiCache = [];
      } finally {
        this.updateState = 0;
      }
      if (hadFocus)
        this.focus();
      this.requestMeasure();
    }
    updatePlugins(update) {
      let prevSpecs = update.startState.facet(viewPlugin), specs = update.state.facet(viewPlugin);
      if (prevSpecs != specs) {
        let newPlugins = [];
        for (let spec of specs) {
          let found = prevSpecs.indexOf(spec);
          if (found < 0) {
            newPlugins.push(new PluginInstance(spec));
          } else {
            let plugin = this.plugins[found];
            plugin.mustUpdate = update;
            newPlugins.push(plugin);
          }
        }
        for (let plugin of this.plugins)
          if (plugin.mustUpdate != update)
            plugin.destroy(this);
        this.plugins = newPlugins;
        this.pluginMap.clear();
        this.inputState.ensureHandlers(this);
      } else {
        for (let p of this.plugins)
          p.mustUpdate = update;
      }
      for (let i = 0; i < this.plugins.length; i++)
        this.plugins[i].update(this);
    }
    measure(flush = true) {
      if (this.destroyed)
        return;
      if (this.measureScheduled > -1)
        cancelAnimationFrame(this.measureScheduled);
      this.measureScheduled = 0;
      if (flush)
        this.observer.flush();
      let updated = null;
      try {
        for (let i = 0; ; i++) {
          this.updateState = 1;
          let oldViewport = this.viewport;
          let changed = this.viewState.measure(this);
          if (!changed && !this.measureRequests.length && this.viewState.scrollTarget == null)
            break;
          if (i > 5) {
            console.warn(this.measureRequests.length ? "Measure loop restarted more than 5 times" : "Viewport failed to stabilize");
            break;
          }
          let measuring = [];
          if (!(changed & 4))
            [this.measureRequests, measuring] = [measuring, this.measureRequests];
          let measured = measuring.map((m) => {
            try {
              return m.read(this);
            } catch (e) {
              logException(this.state, e);
              return BadMeasure;
            }
          });
          let update = new ViewUpdate(this, this.state), redrawn = false, scrolled = false;
          update.flags |= changed;
          if (!updated)
            updated = update;
          else
            updated.flags |= changed;
          this.updateState = 2;
          if (!update.empty) {
            this.updatePlugins(update);
            this.inputState.update(update);
            this.updateAttrs();
            redrawn = this.docView.update(update);
          }
          for (let i2 = 0; i2 < measuring.length; i2++)
            if (measured[i2] != BadMeasure) {
              try {
                let m = measuring[i2];
                if (m.write)
                  m.write(measured[i2], this);
              } catch (e) {
                logException(this.state, e);
              }
            }
          if (this.viewState.scrollTarget) {
            this.docView.scrollIntoView(this.viewState.scrollTarget);
            this.viewState.scrollTarget = null;
            scrolled = true;
          }
          if (redrawn)
            this.docView.updateSelection(true);
          if (this.viewport.from == oldViewport.from && this.viewport.to == oldViewport.to && !scrolled && this.measureRequests.length == 0)
            break;
        }
      } finally {
        this.updateState = 0;
        this.measureScheduled = -1;
      }
      if (updated && !updated.empty)
        for (let listener of this.state.facet(updateListener))
          listener(updated);
    }
    get themeClasses() {
      return baseThemeID + " " + (this.state.facet(darkTheme) ? baseDarkID : baseLightID) + " " + this.state.facet(theme);
    }
    updateAttrs() {
      let editorAttrs = attrsFromFacet(this, editorAttributes, {
        class: "cm-editor" + (this.hasFocus ? " cm-focused " : " ") + this.themeClasses
      });
      let contentAttrs = {
        spellcheck: "false",
        autocorrect: "off",
        autocapitalize: "off",
        translate: "no",
        contenteditable: !this.state.facet(editable) ? "false" : "true",
        class: "cm-content",
        style: `${browser.tabSize}: ${this.state.tabSize}`,
        role: "textbox",
        "aria-multiline": "true"
      };
      if (this.state.readOnly)
        contentAttrs["aria-readonly"] = "true";
      attrsFromFacet(this, contentAttributes, contentAttrs);
      this.observer.ignore(() => {
        updateAttrs(this.contentDOM, this.contentAttrs, contentAttrs);
        updateAttrs(this.dom, this.editorAttrs, editorAttrs);
      });
      this.editorAttrs = editorAttrs;
      this.contentAttrs = contentAttrs;
    }
    showAnnouncements(trs) {
      let first = true;
      for (let tr of trs)
        for (let effect of tr.effects)
          if (effect.is(EditorView.announce)) {
            if (first)
              this.announceDOM.textContent = "";
            first = false;
            let div = this.announceDOM.appendChild(document.createElement("div"));
            div.textContent = effect.value;
          }
    }
    mountStyles() {
      this.styleModules = this.state.facet(styleModule);
      StyleModule.mount(this.root, this.styleModules.concat(baseTheme).reverse());
    }
    readMeasured() {
      if (this.updateState == 2)
        throw new Error("Reading the editor layout isn't allowed during an update");
      if (this.updateState == 0 && this.measureScheduled > -1)
        this.measure(false);
    }
    requestMeasure(request) {
      if (this.measureScheduled < 0)
        this.measureScheduled = requestAnimationFrame(() => this.measure());
      if (request) {
        if (request.key != null)
          for (let i = 0; i < this.measureRequests.length; i++) {
            if (this.measureRequests[i].key === request.key) {
              this.measureRequests[i] = request;
              return;
            }
          }
        this.measureRequests.push(request);
      }
    }
    pluginField(field) {
      let result = [];
      for (let plugin of this.plugins)
        plugin.update(this).takeField(field, result);
      return result;
    }
    plugin(plugin) {
      let known = this.pluginMap.get(plugin);
      if (known === void 0 || known && known.spec != plugin)
        this.pluginMap.set(plugin, known = this.plugins.find((p) => p.spec == plugin) || null);
      return known && known.update(this).value;
    }
    get documentTop() {
      return this.contentDOM.getBoundingClientRect().top + this.viewState.paddingTop;
    }
    get documentPadding() {
      return { top: this.viewState.paddingTop, bottom: this.viewState.paddingBottom };
    }
    blockAtHeight(height, docTop) {
      let top2 = ensureTop(docTop, this);
      return this.elementAtHeight(height - top2).moveY(top2);
    }
    elementAtHeight(height) {
      this.readMeasured();
      return this.viewState.elementAtHeight(height);
    }
    visualLineAtHeight(height, docTop) {
      let top2 = ensureTop(docTop, this);
      return this.lineBlockAtHeight(height - top2).moveY(top2);
    }
    lineBlockAtHeight(height) {
      this.readMeasured();
      return this.viewState.lineBlockAtHeight(height);
    }
    viewportLines(f, docTop) {
      let top2 = ensureTop(docTop, this);
      for (let line of this.viewportLineBlocks)
        f(line.moveY(top2));
    }
    get viewportLineBlocks() {
      return this.viewState.viewportLines;
    }
    visualLineAt(pos, docTop = 0) {
      return this.lineBlockAt(pos).moveY(docTop + this.viewState.paddingTop);
    }
    lineBlockAt(pos) {
      return this.viewState.lineBlockAt(pos);
    }
    get contentHeight() {
      return this.viewState.contentHeight;
    }
    moveByChar(start, forward, by) {
      return skipAtoms(this, start, moveByChar(this, start, forward, by));
    }
    moveByGroup(start, forward) {
      return skipAtoms(this, start, moveByChar(this, start, forward, (initial) => byGroup(this, start.head, initial)));
    }
    moveToLineBoundary(start, forward, includeWrap = true) {
      return moveToLineBoundary(this, start, forward, includeWrap);
    }
    moveVertically(start, forward, distance) {
      return skipAtoms(this, start, moveVertically(this, start, forward, distance));
    }
    scrollPosIntoView(pos) {
      this.dispatch({ effects: scrollTo.of(EditorSelection.cursor(pos)) });
    }
    domAtPos(pos) {
      return this.docView.domAtPos(pos);
    }
    posAtDOM(node, offset = 0) {
      return this.docView.posFromDOM(node, offset);
    }
    posAtCoords(coords, precise = true) {
      this.readMeasured();
      return posAtCoords(this, coords, precise);
    }
    coordsAtPos(pos, side = 1) {
      this.readMeasured();
      let rect = this.docView.coordsAt(pos, side);
      if (!rect || rect.left == rect.right)
        return rect;
      let line = this.state.doc.lineAt(pos), order = this.bidiSpans(line);
      let span2 = order[BidiSpan.find(order, pos - line.from, -1, side)];
      return flattenRect(rect, span2.dir == Direction.LTR == side > 0);
    }
    get defaultCharacterWidth() {
      return this.viewState.heightOracle.charWidth;
    }
    get defaultLineHeight() {
      return this.viewState.heightOracle.lineHeight;
    }
    get textDirection() {
      return this.viewState.heightOracle.direction;
    }
    get lineWrapping() {
      return this.viewState.heightOracle.lineWrapping;
    }
    bidiSpans(line) {
      if (line.length > MaxBidiLine)
        return trivialOrder(line.length);
      let dir = this.textDirection;
      for (let entry of this.bidiCache)
        if (entry.from == line.from && entry.dir == dir)
          return entry.order;
      let order = computeOrder(line.text, this.textDirection);
      this.bidiCache.push(new CachedOrder(line.from, line.to, dir, order));
      return order;
    }
    get hasFocus() {
      var _a2;
      return (document.hasFocus() || browser.safari && ((_a2 = this.inputState) === null || _a2 === void 0 ? void 0 : _a2.lastContextMenu) > Date.now() - 3e4) && this.root.activeElement == this.contentDOM;
    }
    focus() {
      this.observer.ignore(() => {
        focusPreventScroll(this.contentDOM);
        this.docView.updateSelection();
      });
    }
    destroy() {
      for (let plugin of this.plugins)
        plugin.destroy(this);
      this.plugins = [];
      this.inputState.destroy();
      this.dom.remove();
      this.observer.destroy();
      if (this.measureScheduled > -1)
        cancelAnimationFrame(this.measureScheduled);
      this.destroyed = true;
    }
    static scrollIntoView(pos, options = {}) {
      return scrollIntoView.of(new ScrollTarget(typeof pos == "number" ? EditorSelection.cursor(pos) : pos, options.y, options.x, options.yMargin, options.xMargin));
    }
    static domEventHandlers(handlers2) {
      return ViewPlugin.define(() => ({}), { eventHandlers: handlers2 });
    }
    static theme(spec, options) {
      let prefix = StyleModule.newName();
      let result = [theme.of(prefix), styleModule.of(buildTheme(`.${prefix}`, spec))];
      if (options && options.dark)
        result.push(darkTheme.of(true));
      return result;
    }
    static baseTheme(spec) {
      return Prec.lowest(styleModule.of(buildTheme("." + baseThemeID, spec, lightDarkIDs)));
    }
  };
  EditorView.scrollTo = scrollTo;
  EditorView.centerOn = centerOn;
  EditorView.styleModule = styleModule;
  EditorView.inputHandler = inputHandler;
  EditorView.exceptionSink = exceptionSink;
  EditorView.updateListener = updateListener;
  EditorView.editable = editable;
  EditorView.mouseSelectionStyle = mouseSelectionStyle;
  EditorView.dragMovesSelection = dragMovesSelection$1;
  EditorView.clickAddsSelectionRange = clickAddsSelectionRange;
  EditorView.decorations = decorations;
  EditorView.darkTheme = darkTheme;
  EditorView.contentAttributes = contentAttributes;
  EditorView.editorAttributes = editorAttributes;
  EditorView.lineWrapping = /* @__PURE__ */ EditorView.contentAttributes.of({ "class": "cm-lineWrapping" });
  EditorView.announce = /* @__PURE__ */ StateEffect.define();
  var MaxBidiLine = 4096;
  function ensureTop(given, view) {
    return (given == null ? view.contentDOM.getBoundingClientRect().top : given) + view.viewState.paddingTop;
  }
  var BadMeasure = {};
  var CachedOrder = class {
    constructor(from2, to, dir, order) {
      this.from = from2;
      this.to = to;
      this.dir = dir;
      this.order = order;
    }
    static update(cache, changes) {
      if (changes.empty)
        return cache;
      let result = [], lastDir = cache.length ? cache[cache.length - 1].dir : Direction.LTR;
      for (let i = Math.max(0, cache.length - 10); i < cache.length; i++) {
        let entry = cache[i];
        if (entry.dir == lastDir && !changes.touchesRange(entry.from, entry.to))
          result.push(new CachedOrder(changes.mapPos(entry.from, 1), changes.mapPos(entry.to, -1), entry.dir, entry.order));
      }
      return result;
    }
  };
  function attrsFromFacet(view, facet, base2) {
    for (let sources = view.state.facet(facet), i = sources.length - 1; i >= 0; i--) {
      let source = sources[i], value = typeof source == "function" ? source(view) : source;
      if (value)
        combineAttrs(value, base2);
    }
    return base2;
  }
  var currentPlatform = browser.mac ? "mac" : browser.windows ? "win" : browser.linux ? "linux" : "key";
  function normalizeKeyName(name2, platform) {
    const parts = name2.split(/-(?!$)/);
    let result = parts[parts.length - 1];
    if (result == "Space")
      result = " ";
    let alt, ctrl, shift2, meta2;
    for (let i = 0; i < parts.length - 1; ++i) {
      const mod = parts[i];
      if (/^(cmd|meta|m)$/i.test(mod))
        meta2 = true;
      else if (/^a(lt)?$/i.test(mod))
        alt = true;
      else if (/^(c|ctrl|control)$/i.test(mod))
        ctrl = true;
      else if (/^s(hift)?$/i.test(mod))
        shift2 = true;
      else if (/^mod$/i.test(mod)) {
        if (platform == "mac")
          meta2 = true;
        else
          ctrl = true;
      } else
        throw new Error("Unrecognized modifier name: " + mod);
    }
    if (alt)
      result = "Alt-" + result;
    if (ctrl)
      result = "Ctrl-" + result;
    if (meta2)
      result = "Meta-" + result;
    if (shift2)
      result = "Shift-" + result;
    return result;
  }
  function modifiers(name2, event, shift2) {
    if (event.altKey)
      name2 = "Alt-" + name2;
    if (event.ctrlKey)
      name2 = "Ctrl-" + name2;
    if (event.metaKey)
      name2 = "Meta-" + name2;
    if (shift2 !== false && event.shiftKey)
      name2 = "Shift-" + name2;
    return name2;
  }
  var handleKeyEvents = /* @__PURE__ */ EditorView.domEventHandlers({
    keydown(event, view) {
      return runHandlers(getKeymap(view.state), event, view, "editor");
    }
  });
  var keymap = /* @__PURE__ */ Facet.define({ enables: handleKeyEvents });
  var Keymaps = /* @__PURE__ */ new WeakMap();
  function getKeymap(state) {
    let bindings = state.facet(keymap);
    let map = Keymaps.get(bindings);
    if (!map)
      Keymaps.set(bindings, map = buildKeymap(bindings.reduce((a, b) => a.concat(b), [])));
    return map;
  }
  function runScopeHandlers(view, event, scope) {
    return runHandlers(getKeymap(view.state), event, view, scope);
  }
  var storedPrefix = null;
  var PrefixTimeout = 4e3;
  function buildKeymap(bindings, platform = currentPlatform) {
    let bound = /* @__PURE__ */ Object.create(null);
    let isPrefix = /* @__PURE__ */ Object.create(null);
    let checkPrefix = (name2, is) => {
      let current = isPrefix[name2];
      if (current == null)
        isPrefix[name2] = is;
      else if (current != is)
        throw new Error("Key binding " + name2 + " is used both as a regular binding and as a multi-stroke prefix");
    };
    let add2 = (scope, key, command2, preventDefault) => {
      let scopeObj = bound[scope] || (bound[scope] = /* @__PURE__ */ Object.create(null));
      let parts = key.split(/ (?!$)/).map((k) => normalizeKeyName(k, platform));
      for (let i = 1; i < parts.length; i++) {
        let prefix = parts.slice(0, i).join(" ");
        checkPrefix(prefix, true);
        if (!scopeObj[prefix])
          scopeObj[prefix] = {
            preventDefault: true,
            commands: [(view) => {
              let ourObj = storedPrefix = { view, prefix, scope };
              setTimeout(() => {
                if (storedPrefix == ourObj)
                  storedPrefix = null;
              }, PrefixTimeout);
              return true;
            }]
          };
      }
      let full = parts.join(" ");
      checkPrefix(full, false);
      let binding = scopeObj[full] || (scopeObj[full] = { preventDefault: false, commands: [] });
      binding.commands.push(command2);
      if (preventDefault)
        binding.preventDefault = true;
    };
    for (let b of bindings) {
      let name2 = b[platform] || b.key;
      if (!name2)
        continue;
      for (let scope of b.scope ? b.scope.split(" ") : ["editor"]) {
        add2(scope, name2, b.run, b.preventDefault);
        if (b.shift)
          add2(scope, "Shift-" + name2, b.shift, b.preventDefault);
      }
    }
    return bound;
  }
  function runHandlers(map, event, view, scope) {
    let name2 = keyName(event), isChar = name2.length == 1 && name2 != " ";
    let prefix = "", fallthrough = false;
    if (storedPrefix && storedPrefix.view == view && storedPrefix.scope == scope) {
      prefix = storedPrefix.prefix + " ";
      if (fallthrough = modifierCodes.indexOf(event.keyCode) < 0)
        storedPrefix = null;
    }
    let runFor = (binding) => {
      if (binding) {
        for (let cmd2 of binding.commands)
          if (cmd2(view))
            return true;
        if (binding.preventDefault)
          fallthrough = true;
      }
      return false;
    };
    let scopeObj = map[scope], baseName;
    if (scopeObj) {
      if (runFor(scopeObj[prefix + modifiers(name2, event, !isChar)]))
        return true;
      if (isChar && (event.shiftKey || event.altKey || event.metaKey) && (baseName = base[event.keyCode]) && baseName != name2) {
        if (runFor(scopeObj[prefix + modifiers(baseName, event, true)]))
          return true;
      } else if (isChar && event.shiftKey) {
        if (runFor(scopeObj[prefix + modifiers(name2, event, true)]))
          return true;
      }
    }
    return fallthrough;
  }
  var CanHidePrimary = !browser.ios;
  var selectionConfig = /* @__PURE__ */ Facet.define({
    combine(configs) {
      return combineConfig(configs, {
        cursorBlinkRate: 1200,
        drawRangeCursor: true
      }, {
        cursorBlinkRate: (a, b) => Math.min(a, b),
        drawRangeCursor: (a, b) => a || b
      });
    }
  });
  function drawSelection(config2 = {}) {
    return [
      selectionConfig.of(config2),
      drawSelectionPlugin,
      hideNativeSelection
    ];
  }
  var Piece = class {
    constructor(left, top2, width, height, className) {
      this.left = left;
      this.top = top2;
      this.width = width;
      this.height = height;
      this.className = className;
    }
    draw() {
      let elt = document.createElement("div");
      elt.className = this.className;
      this.adjust(elt);
      return elt;
    }
    adjust(elt) {
      elt.style.left = this.left + "px";
      elt.style.top = this.top + "px";
      if (this.width >= 0)
        elt.style.width = this.width + "px";
      elt.style.height = this.height + "px";
    }
    eq(p) {
      return this.left == p.left && this.top == p.top && this.width == p.width && this.height == p.height && this.className == p.className;
    }
  };
  var drawSelectionPlugin = /* @__PURE__ */ ViewPlugin.fromClass(class {
    constructor(view) {
      this.view = view;
      this.rangePieces = [];
      this.cursors = [];
      this.measureReq = { read: this.readPos.bind(this), write: this.drawSel.bind(this) };
      this.selectionLayer = view.scrollDOM.appendChild(document.createElement("div"));
      this.selectionLayer.className = "cm-selectionLayer";
      this.selectionLayer.setAttribute("aria-hidden", "true");
      this.cursorLayer = view.scrollDOM.appendChild(document.createElement("div"));
      this.cursorLayer.className = "cm-cursorLayer";
      this.cursorLayer.setAttribute("aria-hidden", "true");
      view.requestMeasure(this.measureReq);
      this.setBlinkRate();
    }
    setBlinkRate() {
      this.cursorLayer.style.animationDuration = this.view.state.facet(selectionConfig).cursorBlinkRate + "ms";
    }
    update(update) {
      let confChanged = update.startState.facet(selectionConfig) != update.state.facet(selectionConfig);
      if (confChanged || update.selectionSet || update.geometryChanged || update.viewportChanged)
        this.view.requestMeasure(this.measureReq);
      if (update.transactions.some((tr) => tr.scrollIntoView))
        this.cursorLayer.style.animationName = this.cursorLayer.style.animationName == "cm-blink" ? "cm-blink2" : "cm-blink";
      if (confChanged)
        this.setBlinkRate();
    }
    readPos() {
      let { state } = this.view, conf = state.facet(selectionConfig);
      let rangePieces = state.selection.ranges.map((r) => r.empty ? [] : measureRange(this.view, r)).reduce((a, b) => a.concat(b));
      let cursors = [];
      for (let r of state.selection.ranges) {
        let prim = r == state.selection.main;
        if (r.empty ? !prim || CanHidePrimary : conf.drawRangeCursor) {
          let piece = measureCursor(this.view, r, prim);
          if (piece)
            cursors.push(piece);
        }
      }
      return { rangePieces, cursors };
    }
    drawSel({ rangePieces, cursors }) {
      if (rangePieces.length != this.rangePieces.length || rangePieces.some((p, i) => !p.eq(this.rangePieces[i]))) {
        this.selectionLayer.textContent = "";
        for (let p of rangePieces)
          this.selectionLayer.appendChild(p.draw());
        this.rangePieces = rangePieces;
      }
      if (cursors.length != this.cursors.length || cursors.some((c, i) => !c.eq(this.cursors[i]))) {
        let oldCursors = this.cursorLayer.children;
        if (oldCursors.length !== cursors.length) {
          this.cursorLayer.textContent = "";
          for (const c of cursors)
            this.cursorLayer.appendChild(c.draw());
        } else {
          cursors.forEach((c, idx) => c.adjust(oldCursors[idx]));
        }
        this.cursors = cursors;
      }
    }
    destroy() {
      this.selectionLayer.remove();
      this.cursorLayer.remove();
    }
  });
  var themeSpec = {
    ".cm-line": {
      "& ::selection": { backgroundColor: "transparent !important" },
      "&::selection": { backgroundColor: "transparent !important" }
    }
  };
  if (CanHidePrimary)
    themeSpec[".cm-line"].caretColor = "transparent !important";
  var hideNativeSelection = /* @__PURE__ */ Prec.highest(/* @__PURE__ */ EditorView.theme(themeSpec));
  function getBase(view) {
    let rect = view.scrollDOM.getBoundingClientRect();
    let left = view.textDirection == Direction.LTR ? rect.left : rect.right - view.scrollDOM.clientWidth;
    return { left: left - view.scrollDOM.scrollLeft, top: rect.top - view.scrollDOM.scrollTop };
  }
  function wrappedLine(view, pos, inside2) {
    let range = EditorSelection.cursor(pos);
    return {
      from: Math.max(inside2.from, view.moveToLineBoundary(range, false, true).from),
      to: Math.min(inside2.to, view.moveToLineBoundary(range, true, true).from),
      type: BlockType.Text
    };
  }
  function blockAt(view, pos) {
    let line = view.lineBlockAt(pos);
    if (Array.isArray(line.type))
      for (let l of line.type) {
        if (l.to > pos || l.to == pos && (l.to == line.to || l.type == BlockType.Text))
          return l;
      }
    return line;
  }
  function measureRange(view, range) {
    if (range.to <= view.viewport.from || range.from >= view.viewport.to)
      return [];
    let from2 = Math.max(range.from, view.viewport.from), to = Math.min(range.to, view.viewport.to);
    let ltr = view.textDirection == Direction.LTR;
    let content2 = view.contentDOM, contentRect = content2.getBoundingClientRect(), base2 = getBase(view);
    let lineStyle = window.getComputedStyle(content2.firstChild);
    let leftSide = contentRect.left + parseInt(lineStyle.paddingLeft) + Math.min(0, parseInt(lineStyle.textIndent));
    let rightSide = contentRect.right - parseInt(lineStyle.paddingRight);
    let startBlock = blockAt(view, from2), endBlock = blockAt(view, to);
    let visualStart = startBlock.type == BlockType.Text ? startBlock : null;
    let visualEnd = endBlock.type == BlockType.Text ? endBlock : null;
    if (view.lineWrapping) {
      if (visualStart)
        visualStart = wrappedLine(view, from2, visualStart);
      if (visualEnd)
        visualEnd = wrappedLine(view, to, visualEnd);
    }
    if (visualStart && visualEnd && visualStart.from == visualEnd.from) {
      return pieces(drawForLine(range.from, range.to, visualStart));
    } else {
      let top2 = visualStart ? drawForLine(range.from, null, visualStart) : drawForWidget(startBlock, false);
      let bottom = visualEnd ? drawForLine(null, range.to, visualEnd) : drawForWidget(endBlock, true);
      let between = [];
      if ((visualStart || startBlock).to < (visualEnd || endBlock).from - 1)
        between.push(piece(leftSide, top2.bottom, rightSide, bottom.top));
      else if (top2.bottom < bottom.top && view.elementAtHeight((top2.bottom + bottom.top) / 2).type == BlockType.Text)
        top2.bottom = bottom.top = (top2.bottom + bottom.top) / 2;
      return pieces(top2).concat(between).concat(pieces(bottom));
    }
    function piece(left, top2, right, bottom) {
      return new Piece(left - base2.left, top2 - base2.top - 0.01, right - left, bottom - top2 + 0.01, "cm-selectionBackground");
    }
    function pieces({ top: top2, bottom, horizontal }) {
      let pieces2 = [];
      for (let i = 0; i < horizontal.length; i += 2)
        pieces2.push(piece(horizontal[i], top2, horizontal[i + 1], bottom));
      return pieces2;
    }
    function drawForLine(from3, to2, line) {
      let top2 = 1e9, bottom = -1e9, horizontal = [];
      function addSpan(from4, fromOpen, to3, toOpen, dir) {
        let fromCoords = view.coordsAtPos(from4, from4 == line.to ? -2 : 2);
        let toCoords = view.coordsAtPos(to3, to3 == line.from ? 2 : -2);
        top2 = Math.min(fromCoords.top, toCoords.top, top2);
        bottom = Math.max(fromCoords.bottom, toCoords.bottom, bottom);
        if (dir == Direction.LTR)
          horizontal.push(ltr && fromOpen ? leftSide : fromCoords.left, ltr && toOpen ? rightSide : toCoords.right);
        else
          horizontal.push(!ltr && toOpen ? leftSide : toCoords.left, !ltr && fromOpen ? rightSide : fromCoords.right);
      }
      let start = from3 !== null && from3 !== void 0 ? from3 : line.from, end = to2 !== null && to2 !== void 0 ? to2 : line.to;
      for (let r of view.visibleRanges)
        if (r.to > start && r.from < end) {
          for (let pos = Math.max(r.from, start), endPos = Math.min(r.to, end); ; ) {
            let docLine = view.state.doc.lineAt(pos);
            for (let span2 of view.bidiSpans(docLine)) {
              let spanFrom = span2.from + docLine.from, spanTo = span2.to + docLine.from;
              if (spanFrom >= endPos)
                break;
              if (spanTo > pos)
                addSpan(Math.max(spanFrom, pos), from3 == null && spanFrom <= start, Math.min(spanTo, endPos), to2 == null && spanTo >= end, span2.dir);
            }
            pos = docLine.to + 1;
            if (pos >= endPos)
              break;
          }
        }
      if (horizontal.length == 0)
        addSpan(start, from3 == null, end, to2 == null, view.textDirection);
      return { top: top2, bottom, horizontal };
    }
    function drawForWidget(block, top2) {
      let y = contentRect.top + (top2 ? block.top : block.bottom);
      return { top: y, bottom: y, horizontal: [] };
    }
  }
  function measureCursor(view, cursor, primary) {
    let pos = view.coordsAtPos(cursor.head, cursor.assoc || 1);
    if (!pos)
      return null;
    let base2 = getBase(view);
    return new Piece(pos.left - base2.left, pos.top - base2.top, -1, pos.bottom - pos.top, primary ? "cm-cursor cm-cursor-primary" : "cm-cursor cm-cursor-secondary");
  }
  var setDropCursorPos = /* @__PURE__ */ StateEffect.define({
    map(pos, mapping) {
      return pos == null ? null : mapping.mapPos(pos);
    }
  });
  var dropCursorPos = /* @__PURE__ */ StateField.define({
    create() {
      return null;
    },
    update(pos, tr) {
      if (pos != null)
        pos = tr.changes.mapPos(pos);
      return tr.effects.reduce((pos2, e) => e.is(setDropCursorPos) ? e.value : pos2, pos);
    }
  });
  var drawDropCursor = /* @__PURE__ */ ViewPlugin.fromClass(class {
    constructor(view) {
      this.view = view;
      this.cursor = null;
      this.measureReq = { read: this.readPos.bind(this), write: this.drawCursor.bind(this) };
    }
    update(update) {
      var _a2;
      let cursorPos = update.state.field(dropCursorPos);
      if (cursorPos == null) {
        if (this.cursor != null) {
          (_a2 = this.cursor) === null || _a2 === void 0 ? void 0 : _a2.remove();
          this.cursor = null;
        }
      } else {
        if (!this.cursor) {
          this.cursor = this.view.scrollDOM.appendChild(document.createElement("div"));
          this.cursor.className = "cm-dropCursor";
        }
        if (update.startState.field(dropCursorPos) != cursorPos || update.docChanged || update.geometryChanged)
          this.view.requestMeasure(this.measureReq);
      }
    }
    readPos() {
      let pos = this.view.state.field(dropCursorPos);
      let rect = pos != null && this.view.coordsAtPos(pos);
      if (!rect)
        return null;
      let outer = this.view.scrollDOM.getBoundingClientRect();
      return {
        left: rect.left - outer.left + this.view.scrollDOM.scrollLeft,
        top: rect.top - outer.top + this.view.scrollDOM.scrollTop,
        height: rect.bottom - rect.top
      };
    }
    drawCursor(pos) {
      if (this.cursor) {
        if (pos) {
          this.cursor.style.left = pos.left + "px";
          this.cursor.style.top = pos.top + "px";
          this.cursor.style.height = pos.height + "px";
        } else {
          this.cursor.style.left = "-100000px";
        }
      }
    }
    destroy() {
      if (this.cursor)
        this.cursor.remove();
    }
    setDropPos(pos) {
      if (this.view.state.field(dropCursorPos) != pos)
        this.view.dispatch({ effects: setDropCursorPos.of(pos) });
    }
  }, {
    eventHandlers: {
      dragover(event) {
        this.setDropPos(this.view.posAtCoords({ x: event.clientX, y: event.clientY }));
      },
      dragleave(event) {
        if (event.target == this.view.contentDOM || !this.view.contentDOM.contains(event.relatedTarget))
          this.setDropPos(null);
      },
      dragend() {
        this.setDropPos(null);
      },
      drop() {
        this.setDropPos(null);
      }
    }
  });
  function dropCursor() {
    return [dropCursorPos, drawDropCursor];
  }
  function iterMatches(doc2, re, from2, to, f) {
    re.lastIndex = 0;
    for (let cursor = doc2.iterRange(from2, to), pos = from2, m; !cursor.next().done; pos += cursor.value.length) {
      if (!cursor.lineBreak)
        while (m = re.exec(cursor.value))
          f(pos + m.index, pos + m.index + m[0].length, m);
    }
  }
  function matchRanges(view, maxLength) {
    let visible = view.visibleRanges;
    if (visible.length == 1 && visible[0].from == view.viewport.from && visible[0].to == view.viewport.to)
      return visible;
    let result = [];
    for (let { from: from2, to } of visible) {
      from2 = Math.max(view.state.doc.lineAt(from2).from, from2 - maxLength);
      to = Math.min(view.state.doc.lineAt(to).to, to + maxLength);
      if (result.length && result[result.length - 1].to >= from2)
        result[result.length - 1].to = to;
      else
        result.push({ from: from2, to });
    }
    return result;
  }
  var MatchDecorator = class {
    constructor(config2) {
      let { regexp, decoration, boundary, maxLength = 1e3 } = config2;
      if (!regexp.global)
        throw new RangeError("The regular expression given to MatchDecorator should have its 'g' flag set");
      this.regexp = regexp;
      this.getDeco = typeof decoration == "function" ? decoration : () => decoration;
      this.boundary = boundary;
      this.maxLength = maxLength;
    }
    createDeco(view) {
      let build = new RangeSetBuilder();
      for (let { from: from2, to } of matchRanges(view, this.maxLength))
        iterMatches(view.state.doc, this.regexp, from2, to, (a, b, m) => build.add(a, b, this.getDeco(m, view, a)));
      return build.finish();
    }
    updateDeco(update, deco) {
      let changeFrom = 1e9, changeTo = -1;
      if (update.docChanged)
        update.changes.iterChanges((_f, _t, from2, to) => {
          if (to > update.view.viewport.from && from2 < update.view.viewport.to) {
            changeFrom = Math.min(from2, changeFrom);
            changeTo = Math.max(to, changeTo);
          }
        });
      if (update.viewportChanged || changeTo - changeFrom > 1e3)
        return this.createDeco(update.view);
      if (changeTo > -1)
        return this.updateRange(update.view, deco.map(update.changes), changeFrom, changeTo);
      return deco;
    }
    updateRange(view, deco, updateFrom, updateTo) {
      for (let r of view.visibleRanges) {
        let from2 = Math.max(r.from, updateFrom), to = Math.min(r.to, updateTo);
        if (to > from2) {
          let fromLine = view.state.doc.lineAt(from2), toLine = fromLine.to < to ? view.state.doc.lineAt(to) : fromLine;
          let start = Math.max(r.from, fromLine.from), end = Math.min(r.to, toLine.to);
          if (this.boundary) {
            for (; from2 > fromLine.from; from2--)
              if (this.boundary.test(fromLine.text[from2 - 1 - fromLine.from])) {
                start = from2;
                break;
              }
            for (; to < toLine.to; to++)
              if (this.boundary.test(toLine.text[to - toLine.from])) {
                end = to;
                break;
              }
          }
          let ranges = [], m;
          if (fromLine == toLine) {
            this.regexp.lastIndex = start - fromLine.from;
            while ((m = this.regexp.exec(fromLine.text)) && m.index < end - fromLine.from) {
              let pos = m.index + fromLine.from;
              ranges.push(this.getDeco(m, view, pos).range(pos, pos + m[0].length));
            }
          } else {
            iterMatches(view.state.doc, this.regexp, start, end, (from3, to2, m2) => ranges.push(this.getDeco(m2, view, from3).range(from3, to2)));
          }
          deco = deco.update({ filterFrom: start, filterTo: end, filter: (from3, to2) => from3 < start || to2 > end, add: ranges });
        }
      }
      return deco;
    }
  };
  var UnicodeRegexpSupport = /x/.unicode != null ? "gu" : "g";
  var Specials = /* @__PURE__ */ new RegExp("[\0-\b\n-\x7F-\x9F\xAD\u061C\u200B\u200E\u200F\u2028\u2029\u202D\u202E\uFEFF\uFFF9-\uFFFC]", UnicodeRegexpSupport);
  var Names = {
    0: "null",
    7: "bell",
    8: "backspace",
    10: "newline",
    11: "vertical tab",
    13: "carriage return",
    27: "escape",
    8203: "zero width space",
    8204: "zero width non-joiner",
    8205: "zero width joiner",
    8206: "left-to-right mark",
    8207: "right-to-left mark",
    8232: "line separator",
    8237: "left-to-right override",
    8238: "right-to-left override",
    8233: "paragraph separator",
    65279: "zero width no-break space",
    65532: "object replacement"
  };
  var _supportsTabSize = null;
  function supportsTabSize() {
    var _a2;
    if (_supportsTabSize == null && typeof document != "undefined" && document.body) {
      let styles = document.body.style;
      _supportsTabSize = ((_a2 = styles.tabSize) !== null && _a2 !== void 0 ? _a2 : styles.MozTabSize) != null;
    }
    return _supportsTabSize || false;
  }
  var specialCharConfig = /* @__PURE__ */ Facet.define({
    combine(configs) {
      let config2 = combineConfig(configs, {
        render: null,
        specialChars: Specials,
        addSpecialChars: null
      });
      if (config2.replaceTabs = !supportsTabSize())
        config2.specialChars = new RegExp("	|" + config2.specialChars.source, UnicodeRegexpSupport);
      if (config2.addSpecialChars)
        config2.specialChars = new RegExp(config2.specialChars.source + "|" + config2.addSpecialChars.source, UnicodeRegexpSupport);
      return config2;
    }
  });
  function highlightSpecialChars(config2 = {}) {
    return [specialCharConfig.of(config2), specialCharPlugin()];
  }
  var _plugin = null;
  function specialCharPlugin() {
    return _plugin || (_plugin = ViewPlugin.fromClass(class {
      constructor(view) {
        this.view = view;
        this.decorations = Decoration.none;
        this.decorationCache = /* @__PURE__ */ Object.create(null);
        this.decorator = this.makeDecorator(view.state.facet(specialCharConfig));
        this.decorations = this.decorator.createDeco(view);
      }
      makeDecorator(conf) {
        return new MatchDecorator({
          regexp: conf.specialChars,
          decoration: (m, view, pos) => {
            let { doc: doc2 } = view.state;
            let code = codePointAt(m[0], 0);
            if (code == 9) {
              let line = doc2.lineAt(pos);
              let size = view.state.tabSize, col = countColumn(line.text, size, pos - line.from);
              return Decoration.replace({ widget: new TabWidget((size - col % size) * this.view.defaultCharacterWidth) });
            }
            return this.decorationCache[code] || (this.decorationCache[code] = Decoration.replace({ widget: new SpecialCharWidget(conf, code) }));
          },
          boundary: conf.replaceTabs ? void 0 : /[^]/
        });
      }
      update(update) {
        let conf = update.state.facet(specialCharConfig);
        if (update.startState.facet(specialCharConfig) != conf) {
          this.decorator = this.makeDecorator(conf);
          this.decorations = this.decorator.createDeco(update.view);
        } else {
          this.decorations = this.decorator.updateDeco(update, this.decorations);
        }
      }
    }, {
      decorations: (v) => v.decorations
    }));
  }
  var DefaultPlaceholder = "\u2022";
  function placeholder$1(code) {
    if (code >= 32)
      return DefaultPlaceholder;
    if (code == 10)
      return "\u2424";
    return String.fromCharCode(9216 + code);
  }
  var SpecialCharWidget = class extends WidgetType {
    constructor(options, code) {
      super();
      this.options = options;
      this.code = code;
    }
    eq(other) {
      return other.code == this.code;
    }
    toDOM(view) {
      let ph = placeholder$1(this.code);
      let desc = view.state.phrase("Control character") + " " + (Names[this.code] || "0x" + this.code.toString(16));
      let custom = this.options.render && this.options.render(this.code, desc, ph);
      if (custom)
        return custom;
      let span2 = document.createElement("span");
      span2.textContent = ph;
      span2.title = desc;
      span2.setAttribute("aria-label", desc);
      span2.className = "cm-specialChar";
      return span2;
    }
    ignoreEvent() {
      return false;
    }
  };
  var TabWidget = class extends WidgetType {
    constructor(width) {
      super();
      this.width = width;
    }
    eq(other) {
      return other.width == this.width;
    }
    toDOM() {
      let span2 = document.createElement("span");
      span2.textContent = "	";
      span2.className = "cm-tab";
      span2.style.width = this.width + "px";
      return span2;
    }
    ignoreEvent() {
      return false;
    }
  };
  function highlightActiveLine() {
    return activeLineHighlighter;
  }
  var lineDeco = /* @__PURE__ */ Decoration.line({ class: "cm-activeLine" });
  var activeLineHighlighter = /* @__PURE__ */ ViewPlugin.fromClass(class {
    constructor(view) {
      this.decorations = this.getDeco(view);
    }
    update(update) {
      if (update.docChanged || update.selectionSet)
        this.decorations = this.getDeco(update.view);
    }
    getDeco(view) {
      let lastLineStart = -1, deco = [];
      for (let r of view.state.selection.ranges) {
        if (!r.empty)
          return Decoration.none;
        let line = view.lineBlockAt(r.head);
        if (line.from > lastLineStart) {
          deco.push(lineDeco.range(line.from));
          lastLineStart = line.from;
        }
      }
      return Decoration.set(deco);
    }
  }, {
    decorations: (v) => v.decorations
  });

  // node_modules/@codemirror/history/dist/index.js
  var fromHistory = /* @__PURE__ */ Annotation.define();
  var isolateHistory = /* @__PURE__ */ Annotation.define();
  var invertedEffects = /* @__PURE__ */ Facet.define();
  var historyConfig = /* @__PURE__ */ Facet.define({
    combine(configs) {
      return combineConfig(configs, {
        minDepth: 100,
        newGroupDelay: 500
      }, { minDepth: Math.max, newGroupDelay: Math.min });
    }
  });
  function changeEnd(changes) {
    let end = 0;
    changes.iterChangedRanges((_, to) => end = to);
    return end;
  }
  var historyField_ = /* @__PURE__ */ StateField.define({
    create() {
      return HistoryState.empty;
    },
    update(state, tr) {
      let config2 = tr.state.facet(historyConfig);
      let fromHist = tr.annotation(fromHistory);
      if (fromHist) {
        let selection = tr.docChanged ? EditorSelection.single(changeEnd(tr.changes)) : void 0;
        let item = HistEvent.fromTransaction(tr, selection), from2 = fromHist.side;
        let other = from2 == 0 ? state.undone : state.done;
        if (item)
          other = updateBranch(other, other.length, config2.minDepth, item);
        else
          other = addSelection(other, tr.startState.selection);
        return new HistoryState(from2 == 0 ? fromHist.rest : other, from2 == 0 ? other : fromHist.rest);
      }
      let isolate = tr.annotation(isolateHistory);
      if (isolate == "full" || isolate == "before")
        state = state.isolate();
      if (tr.annotation(Transaction.addToHistory) === false)
        return !tr.changes.empty ? state.addMapping(tr.changes.desc) : state;
      let event = HistEvent.fromTransaction(tr);
      let time = tr.annotation(Transaction.time), userEvent = tr.annotation(Transaction.userEvent);
      if (event)
        state = state.addChanges(event, time, userEvent, config2.newGroupDelay, config2.minDepth);
      else if (tr.selection)
        state = state.addSelection(tr.startState.selection, time, userEvent, config2.newGroupDelay);
      if (isolate == "full" || isolate == "after")
        state = state.isolate();
      return state;
    },
    toJSON(value) {
      return { done: value.done.map((e) => e.toJSON()), undone: value.undone.map((e) => e.toJSON()) };
    },
    fromJSON(json2) {
      return new HistoryState(json2.done.map(HistEvent.fromJSON), json2.undone.map(HistEvent.fromJSON));
    }
  });
  function history(config2 = {}) {
    return [
      historyField_,
      historyConfig.of(config2),
      EditorView.domEventHandlers({
        beforeinput(e, view) {
          let command2 = e.inputType == "historyUndo" ? undo : e.inputType == "historyRedo" ? redo : null;
          if (!command2)
            return false;
          e.preventDefault();
          return command2(view);
        }
      })
    ];
  }
  function cmd(side, selection) {
    return function({ state, dispatch }) {
      if (!selection && state.readOnly)
        return false;
      let historyState = state.field(historyField_, false);
      if (!historyState)
        return false;
      let tr = historyState.pop(side, state, selection);
      if (!tr)
        return false;
      dispatch(tr);
      return true;
    };
  }
  var undo = /* @__PURE__ */ cmd(0, false);
  var redo = /* @__PURE__ */ cmd(1, false);
  var undoSelection = /* @__PURE__ */ cmd(0, true);
  var redoSelection = /* @__PURE__ */ cmd(1, true);
  var HistEvent = class {
    constructor(changes, effects, mapped, startSelection, selectionsAfter) {
      this.changes = changes;
      this.effects = effects;
      this.mapped = mapped;
      this.startSelection = startSelection;
      this.selectionsAfter = selectionsAfter;
    }
    setSelAfter(after) {
      return new HistEvent(this.changes, this.effects, this.mapped, this.startSelection, after);
    }
    toJSON() {
      var _a2, _b, _c;
      return {
        changes: (_a2 = this.changes) === null || _a2 === void 0 ? void 0 : _a2.toJSON(),
        mapped: (_b = this.mapped) === null || _b === void 0 ? void 0 : _b.toJSON(),
        startSelection: (_c = this.startSelection) === null || _c === void 0 ? void 0 : _c.toJSON(),
        selectionsAfter: this.selectionsAfter.map((s) => s.toJSON())
      };
    }
    static fromJSON(json2) {
      return new HistEvent(json2.changes && ChangeSet.fromJSON(json2.changes), [], json2.mapped && ChangeDesc.fromJSON(json2.mapped), json2.startSelection && EditorSelection.fromJSON(json2.startSelection), json2.selectionsAfter.map(EditorSelection.fromJSON));
    }
    static fromTransaction(tr, selection) {
      let effects = none3;
      for (let invert of tr.startState.facet(invertedEffects)) {
        let result = invert(tr);
        if (result.length)
          effects = effects.concat(result);
      }
      if (!effects.length && tr.changes.empty)
        return null;
      return new HistEvent(tr.changes.invert(tr.startState.doc), effects, void 0, selection || tr.startState.selection, none3);
    }
    static selection(selections) {
      return new HistEvent(void 0, none3, void 0, void 0, selections);
    }
  };
  function updateBranch(branch, to, maxLen, newEvent) {
    let start = to + 1 > maxLen + 20 ? to - maxLen - 1 : 0;
    let newBranch = branch.slice(start, to);
    newBranch.push(newEvent);
    return newBranch;
  }
  function isAdjacent(a, b) {
    let ranges = [], isAdjacent2 = false;
    a.iterChangedRanges((f, t2) => ranges.push(f, t2));
    b.iterChangedRanges((_f, _t, f, t2) => {
      for (let i = 0; i < ranges.length; ) {
        let from2 = ranges[i++], to = ranges[i++];
        if (t2 >= from2 && f <= to)
          isAdjacent2 = true;
      }
    });
    return isAdjacent2;
  }
  function eqSelectionShape(a, b) {
    return a.ranges.length == b.ranges.length && a.ranges.filter((r, i) => r.empty != b.ranges[i].empty).length === 0;
  }
  function conc(a, b) {
    return !a.length ? b : !b.length ? a : a.concat(b);
  }
  var none3 = [];
  var MaxSelectionsPerEvent = 200;
  function addSelection(branch, selection) {
    if (!branch.length) {
      return [HistEvent.selection([selection])];
    } else {
      let lastEvent = branch[branch.length - 1];
      let sels = lastEvent.selectionsAfter.slice(Math.max(0, lastEvent.selectionsAfter.length - MaxSelectionsPerEvent));
      if (sels.length && sels[sels.length - 1].eq(selection))
        return branch;
      sels.push(selection);
      return updateBranch(branch, branch.length - 1, 1e9, lastEvent.setSelAfter(sels));
    }
  }
  function popSelection(branch) {
    let last = branch[branch.length - 1];
    let newBranch = branch.slice();
    newBranch[branch.length - 1] = last.setSelAfter(last.selectionsAfter.slice(0, last.selectionsAfter.length - 1));
    return newBranch;
  }
  function addMappingToBranch(branch, mapping) {
    if (!branch.length)
      return branch;
    let length = branch.length, selections = none3;
    while (length) {
      let event = mapEvent(branch[length - 1], mapping, selections);
      if (event.changes && !event.changes.empty || event.effects.length) {
        let result = branch.slice(0, length);
        result[length - 1] = event;
        return result;
      } else {
        mapping = event.mapped;
        length--;
        selections = event.selectionsAfter;
      }
    }
    return selections.length ? [HistEvent.selection(selections)] : none3;
  }
  function mapEvent(event, mapping, extraSelections) {
    let selections = conc(event.selectionsAfter.length ? event.selectionsAfter.map((s) => s.map(mapping)) : none3, extraSelections);
    if (!event.changes)
      return HistEvent.selection(selections);
    let mappedChanges = event.changes.map(mapping), before = mapping.mapDesc(event.changes, true);
    let fullMapping = event.mapped ? event.mapped.composeDesc(before) : before;
    return new HistEvent(mappedChanges, StateEffect.mapEffects(event.effects, mapping), fullMapping, event.startSelection.map(before), selections);
  }
  var joinableUserEvent = /^(input\.type|delete)($|\.)/;
  var HistoryState = class {
    constructor(done, undone, prevTime = 0, prevUserEvent = void 0) {
      this.done = done;
      this.undone = undone;
      this.prevTime = prevTime;
      this.prevUserEvent = prevUserEvent;
    }
    isolate() {
      return this.prevTime ? new HistoryState(this.done, this.undone) : this;
    }
    addChanges(event, time, userEvent, newGroupDelay, maxLen) {
      let done = this.done, lastEvent = done[done.length - 1];
      if (lastEvent && lastEvent.changes && !lastEvent.changes.empty && event.changes && (!userEvent || joinableUserEvent.test(userEvent)) && (!lastEvent.selectionsAfter.length && time - this.prevTime < newGroupDelay && isAdjacent(lastEvent.changes, event.changes) || userEvent == "input.type.compose")) {
        done = updateBranch(done, done.length - 1, maxLen, new HistEvent(event.changes.compose(lastEvent.changes), conc(event.effects, lastEvent.effects), lastEvent.mapped, lastEvent.startSelection, none3));
      } else {
        done = updateBranch(done, done.length, maxLen, event);
      }
      return new HistoryState(done, none3, time, userEvent);
    }
    addSelection(selection, time, userEvent, newGroupDelay) {
      let last = this.done.length ? this.done[this.done.length - 1].selectionsAfter : none3;
      if (last.length > 0 && time - this.prevTime < newGroupDelay && userEvent == this.prevUserEvent && userEvent && /^select($|\.)/.test(userEvent) && eqSelectionShape(last[last.length - 1], selection))
        return this;
      return new HistoryState(addSelection(this.done, selection), this.undone, time, userEvent);
    }
    addMapping(mapping) {
      return new HistoryState(addMappingToBranch(this.done, mapping), addMappingToBranch(this.undone, mapping), this.prevTime, this.prevUserEvent);
    }
    pop(side, state, selection) {
      let branch = side == 0 ? this.done : this.undone;
      if (branch.length == 0)
        return null;
      let event = branch[branch.length - 1];
      if (selection && event.selectionsAfter.length) {
        return state.update({
          selection: event.selectionsAfter[event.selectionsAfter.length - 1],
          annotations: fromHistory.of({ side, rest: popSelection(branch) }),
          userEvent: side == 0 ? "select.undo" : "select.redo",
          scrollIntoView: true
        });
      } else if (!event.changes) {
        return null;
      } else {
        let rest = branch.length == 1 ? none3 : branch.slice(0, branch.length - 1);
        if (event.mapped)
          rest = addMappingToBranch(rest, event.mapped);
        return state.update({
          changes: event.changes,
          selection: event.startSelection,
          effects: event.effects,
          annotations: fromHistory.of({ side, rest }),
          filter: false,
          userEvent: side == 0 ? "undo" : "redo",
          scrollIntoView: true
        });
      }
    }
  };
  HistoryState.empty = /* @__PURE__ */ new HistoryState(none3, none3);
  var historyKeymap = [
    { key: "Mod-z", run: undo, preventDefault: true },
    { key: "Mod-y", mac: "Mod-Shift-z", run: redo, preventDefault: true },
    { key: "Mod-u", run: undoSelection, preventDefault: true },
    { key: "Alt-u", mac: "Mod-Shift-u", run: redoSelection, preventDefault: true }
  ];

  // node_modules/@lezer/common/dist/index.js
  var DefaultBufferLength = 1024;
  var nextPropID = 0;
  var Range2 = class {
    constructor(from2, to) {
      this.from = from2;
      this.to = to;
    }
  };
  var NodeProp = class {
    constructor(config2 = {}) {
      this.id = nextPropID++;
      this.perNode = !!config2.perNode;
      this.deserialize = config2.deserialize || (() => {
        throw new Error("This node type doesn't define a deserialize function");
      });
    }
    add(match2) {
      if (this.perNode)
        throw new RangeError("Can't add per-node props to node types");
      if (typeof match2 != "function")
        match2 = NodeType.match(match2);
      return (type) => {
        let result = match2(type);
        return result === void 0 ? null : [this, result];
      };
    }
  };
  NodeProp.closedBy = new NodeProp({ deserialize: (str) => str.split(" ") });
  NodeProp.openedBy = new NodeProp({ deserialize: (str) => str.split(" ") });
  NodeProp.group = new NodeProp({ deserialize: (str) => str.split(" ") });
  NodeProp.contextHash = new NodeProp({ perNode: true });
  NodeProp.lookAhead = new NodeProp({ perNode: true });
  NodeProp.mounted = new NodeProp({ perNode: true });
  var MountedTree = class {
    constructor(tree, overlay, parser6) {
      this.tree = tree;
      this.overlay = overlay;
      this.parser = parser6;
    }
  };
  var noProps = /* @__PURE__ */ Object.create(null);
  var NodeType = class {
    constructor(name2, props, id2, flags = 0) {
      this.name = name2;
      this.props = props;
      this.id = id2;
      this.flags = flags;
    }
    static define(spec) {
      let props = spec.props && spec.props.length ? /* @__PURE__ */ Object.create(null) : noProps;
      let flags = (spec.top ? 1 : 0) | (spec.skipped ? 2 : 0) | (spec.error ? 4 : 0) | (spec.name == null ? 8 : 0);
      let type = new NodeType(spec.name || "", props, spec.id, flags);
      if (spec.props)
        for (let src of spec.props) {
          if (!Array.isArray(src))
            src = src(type);
          if (src) {
            if (src[0].perNode)
              throw new RangeError("Can't store a per-node prop on a node type");
            props[src[0].id] = src[1];
          }
        }
      return type;
    }
    prop(prop) {
      return this.props[prop.id];
    }
    get isTop() {
      return (this.flags & 1) > 0;
    }
    get isSkipped() {
      return (this.flags & 2) > 0;
    }
    get isError() {
      return (this.flags & 4) > 0;
    }
    get isAnonymous() {
      return (this.flags & 8) > 0;
    }
    is(name2) {
      if (typeof name2 == "string") {
        if (this.name == name2)
          return true;
        let group = this.prop(NodeProp.group);
        return group ? group.indexOf(name2) > -1 : false;
      }
      return this.id == name2;
    }
    static match(map) {
      let direct = /* @__PURE__ */ Object.create(null);
      for (let prop in map)
        for (let name2 of prop.split(" "))
          direct[name2] = map[prop];
      return (node) => {
        for (let groups = node.prop(NodeProp.group), i = -1; i < (groups ? groups.length : 0); i++) {
          let found = direct[i < 0 ? node.name : groups[i]];
          if (found)
            return found;
        }
      };
    }
  };
  NodeType.none = new NodeType("", /* @__PURE__ */ Object.create(null), 0, 8);
  var NodeSet = class {
    constructor(types2) {
      this.types = types2;
      for (let i = 0; i < types2.length; i++)
        if (types2[i].id != i)
          throw new RangeError("Node type ids should correspond to array positions when creating a node set");
    }
    extend(...props) {
      let newTypes = [];
      for (let type of this.types) {
        let newProps = null;
        for (let source of props) {
          let add2 = source(type);
          if (add2) {
            if (!newProps)
              newProps = Object.assign({}, type.props);
            newProps[add2[0].id] = add2[1];
          }
        }
        newTypes.push(newProps ? new NodeType(type.name, newProps, type.id, type.flags) : type);
      }
      return new NodeSet(newTypes);
    }
  };
  var CachedNode = /* @__PURE__ */ new WeakMap();
  var CachedInnerNode = /* @__PURE__ */ new WeakMap();
  var Tree = class {
    constructor(type, children, positions, length, props) {
      this.type = type;
      this.children = children;
      this.positions = positions;
      this.length = length;
      this.props = null;
      if (props && props.length) {
        this.props = /* @__PURE__ */ Object.create(null);
        for (let [prop, value] of props)
          this.props[typeof prop == "number" ? prop : prop.id] = value;
      }
    }
    toString() {
      let mounted = this.prop(NodeProp.mounted);
      if (mounted && !mounted.overlay)
        return mounted.tree.toString();
      let children = "";
      for (let ch of this.children) {
        let str = ch.toString();
        if (str) {
          if (children)
            children += ",";
          children += str;
        }
      }
      return !this.type.name ? children : (/\W/.test(this.type.name) && !this.type.isError ? JSON.stringify(this.type.name) : this.type.name) + (children.length ? "(" + children + ")" : "");
    }
    cursor(pos, side = 0) {
      let scope = pos != null && CachedNode.get(this) || this.topNode;
      let cursor = new TreeCursor(scope);
      if (pos != null) {
        cursor.moveTo(pos, side);
        CachedNode.set(this, cursor._tree);
      }
      return cursor;
    }
    fullCursor() {
      return new TreeCursor(this.topNode, 1);
    }
    get topNode() {
      return new TreeNode(this, 0, 0, null);
    }
    resolve(pos, side = 0) {
      let node = resolveNode(CachedNode.get(this) || this.topNode, pos, side, false);
      CachedNode.set(this, node);
      return node;
    }
    resolveInner(pos, side = 0) {
      let node = resolveNode(CachedInnerNode.get(this) || this.topNode, pos, side, true);
      CachedInnerNode.set(this, node);
      return node;
    }
    iterate(spec) {
      let { enter, leave, from: from2 = 0, to = this.length } = spec;
      for (let c = this.cursor(), get = () => c.node; ; ) {
        let mustLeave = false;
        if (c.from <= to && c.to >= from2 && (c.type.isAnonymous || enter(c.type, c.from, c.to, get) !== false)) {
          if (c.firstChild())
            continue;
          if (!c.type.isAnonymous)
            mustLeave = true;
        }
        for (; ; ) {
          if (mustLeave && leave)
            leave(c.type, c.from, c.to, get);
          mustLeave = c.type.isAnonymous;
          if (c.nextSibling())
            break;
          if (!c.parent())
            return;
          mustLeave = true;
        }
      }
    }
    prop(prop) {
      return !prop.perNode ? this.type.prop(prop) : this.props ? this.props[prop.id] : void 0;
    }
    get propValues() {
      let result = [];
      if (this.props)
        for (let id2 in this.props)
          result.push([+id2, this.props[id2]]);
      return result;
    }
    balance(config2 = {}) {
      return this.children.length <= 8 ? this : balanceRange(NodeType.none, this.children, this.positions, 0, this.children.length, 0, this.length, (children, positions, length) => new Tree(this.type, children, positions, length, this.propValues), config2.makeTree || ((children, positions, length) => new Tree(NodeType.none, children, positions, length)));
    }
    static build(data) {
      return buildTree(data);
    }
  };
  Tree.empty = new Tree(NodeType.none, [], [], 0);
  var FlatBufferCursor = class {
    constructor(buffer, index) {
      this.buffer = buffer;
      this.index = index;
    }
    get id() {
      return this.buffer[this.index - 4];
    }
    get start() {
      return this.buffer[this.index - 3];
    }
    get end() {
      return this.buffer[this.index - 2];
    }
    get size() {
      return this.buffer[this.index - 1];
    }
    get pos() {
      return this.index;
    }
    next() {
      this.index -= 4;
    }
    fork() {
      return new FlatBufferCursor(this.buffer, this.index);
    }
  };
  var TreeBuffer = class {
    constructor(buffer, length, set) {
      this.buffer = buffer;
      this.length = length;
      this.set = set;
    }
    get type() {
      return NodeType.none;
    }
    toString() {
      let result = [];
      for (let index = 0; index < this.buffer.length; ) {
        result.push(this.childString(index));
        index = this.buffer[index + 3];
      }
      return result.join(",");
    }
    childString(index) {
      let id2 = this.buffer[index], endIndex = this.buffer[index + 3];
      let type = this.set.types[id2], result = type.name;
      if (/\W/.test(result) && !type.isError)
        result = JSON.stringify(result);
      index += 4;
      if (endIndex == index)
        return result;
      let children = [];
      while (index < endIndex) {
        children.push(this.childString(index));
        index = this.buffer[index + 3];
      }
      return result + "(" + children.join(",") + ")";
    }
    findChild(startIndex, endIndex, dir, pos, side) {
      let { buffer } = this, pick = -1;
      for (let i = startIndex; i != endIndex; i = buffer[i + 3]) {
        if (checkSide(side, pos, buffer[i + 1], buffer[i + 2])) {
          pick = i;
          if (dir > 0)
            break;
        }
      }
      return pick;
    }
    slice(startI, endI, from2, to) {
      let b = this.buffer;
      let copy = new Uint16Array(endI - startI);
      for (let i = startI, j = 0; i < endI; ) {
        copy[j++] = b[i++];
        copy[j++] = b[i++] - from2;
        copy[j++] = b[i++] - from2;
        copy[j++] = b[i++] - startI;
      }
      return new TreeBuffer(copy, to - from2, this.set);
    }
  };
  function checkSide(side, pos, from2, to) {
    switch (side) {
      case -2:
        return from2 < pos;
      case -1:
        return to >= pos && from2 < pos;
      case 0:
        return from2 < pos && to > pos;
      case 1:
        return from2 <= pos && to > pos;
      case 2:
        return to > pos;
      case 4:
        return true;
    }
  }
  function enterUnfinishedNodesBefore(node, pos) {
    let scan = node.childBefore(pos);
    while (scan) {
      let last = scan.lastChild;
      if (!last || last.to != scan.to)
        break;
      if (last.type.isError && last.from == last.to) {
        node = scan;
        scan = last.prevSibling;
      } else {
        scan = last;
      }
    }
    return node;
  }
  function resolveNode(node, pos, side, overlays) {
    var _a2;
    while (node.from == node.to || (side < 1 ? node.from >= pos : node.from > pos) || (side > -1 ? node.to <= pos : node.to < pos)) {
      let parent = !overlays && node instanceof TreeNode && node.index < 0 ? null : node.parent;
      if (!parent)
        return node;
      node = parent;
    }
    if (overlays)
      for (let scan = node, parent = scan.parent; parent; scan = parent, parent = scan.parent) {
        if (scan instanceof TreeNode && scan.index < 0 && ((_a2 = parent.enter(pos, side, true)) === null || _a2 === void 0 ? void 0 : _a2.from) != scan.from)
          node = parent;
      }
    for (; ; ) {
      let inner = node.enter(pos, side, overlays);
      if (!inner)
        return node;
      node = inner;
    }
  }
  var TreeNode = class {
    constructor(node, _from, index, _parent) {
      this.node = node;
      this._from = _from;
      this.index = index;
      this._parent = _parent;
    }
    get type() {
      return this.node.type;
    }
    get name() {
      return this.node.type.name;
    }
    get from() {
      return this._from;
    }
    get to() {
      return this._from + this.node.length;
    }
    nextChild(i, dir, pos, side, mode = 0) {
      for (let parent = this; ; ) {
        for (let { children, positions } = parent.node, e = dir > 0 ? children.length : -1; i != e; i += dir) {
          let next = children[i], start = positions[i] + parent._from;
          if (!checkSide(side, pos, start, start + next.length))
            continue;
          if (next instanceof TreeBuffer) {
            if (mode & 2)
              continue;
            let index = next.findChild(0, next.buffer.length, dir, pos - start, side);
            if (index > -1)
              return new BufferNode(new BufferContext(parent, next, i, start), null, index);
          } else if (mode & 1 || (!next.type.isAnonymous || hasChild(next))) {
            let mounted;
            if (!(mode & 1) && next.props && (mounted = next.prop(NodeProp.mounted)) && !mounted.overlay)
              return new TreeNode(mounted.tree, start, i, parent);
            let inner = new TreeNode(next, start, i, parent);
            return mode & 1 || !inner.type.isAnonymous ? inner : inner.nextChild(dir < 0 ? next.children.length - 1 : 0, dir, pos, side);
          }
        }
        if (mode & 1 || !parent.type.isAnonymous)
          return null;
        if (parent.index >= 0)
          i = parent.index + dir;
        else
          i = dir < 0 ? -1 : parent._parent.node.children.length;
        parent = parent._parent;
        if (!parent)
          return null;
      }
    }
    get firstChild() {
      return this.nextChild(0, 1, 0, 4);
    }
    get lastChild() {
      return this.nextChild(this.node.children.length - 1, -1, 0, 4);
    }
    childAfter(pos) {
      return this.nextChild(0, 1, pos, 2);
    }
    childBefore(pos) {
      return this.nextChild(this.node.children.length - 1, -1, pos, -2);
    }
    enter(pos, side, overlays = true, buffers = true) {
      let mounted;
      if (overlays && (mounted = this.node.prop(NodeProp.mounted)) && mounted.overlay) {
        let rPos = pos - this.from;
        for (let { from: from2, to } of mounted.overlay) {
          if ((side > 0 ? from2 <= rPos : from2 < rPos) && (side < 0 ? to >= rPos : to > rPos))
            return new TreeNode(mounted.tree, mounted.overlay[0].from + this.from, -1, this);
        }
      }
      return this.nextChild(0, 1, pos, side, buffers ? 0 : 2);
    }
    nextSignificantParent() {
      let val = this;
      while (val.type.isAnonymous && val._parent)
        val = val._parent;
      return val;
    }
    get parent() {
      return this._parent ? this._parent.nextSignificantParent() : null;
    }
    get nextSibling() {
      return this._parent && this.index >= 0 ? this._parent.nextChild(this.index + 1, 1, 0, 4) : null;
    }
    get prevSibling() {
      return this._parent && this.index >= 0 ? this._parent.nextChild(this.index - 1, -1, 0, 4) : null;
    }
    get cursor() {
      return new TreeCursor(this);
    }
    get tree() {
      return this.node;
    }
    toTree() {
      return this.node;
    }
    resolve(pos, side = 0) {
      return resolveNode(this, pos, side, false);
    }
    resolveInner(pos, side = 0) {
      return resolveNode(this, pos, side, true);
    }
    enterUnfinishedNodesBefore(pos) {
      return enterUnfinishedNodesBefore(this, pos);
    }
    getChild(type, before = null, after = null) {
      let r = getChildren(this, type, before, after);
      return r.length ? r[0] : null;
    }
    getChildren(type, before = null, after = null) {
      return getChildren(this, type, before, after);
    }
    toString() {
      return this.node.toString();
    }
  };
  function getChildren(node, type, before, after) {
    let cur2 = node.cursor, result = [];
    if (!cur2.firstChild())
      return result;
    if (before != null) {
      while (!cur2.type.is(before))
        if (!cur2.nextSibling())
          return result;
    }
    for (; ; ) {
      if (after != null && cur2.type.is(after))
        return result;
      if (cur2.type.is(type))
        result.push(cur2.node);
      if (!cur2.nextSibling())
        return after == null ? result : [];
    }
  }
  var BufferContext = class {
    constructor(parent, buffer, index, start) {
      this.parent = parent;
      this.buffer = buffer;
      this.index = index;
      this.start = start;
    }
  };
  var BufferNode = class {
    constructor(context, _parent, index) {
      this.context = context;
      this._parent = _parent;
      this.index = index;
      this.type = context.buffer.set.types[context.buffer.buffer[index]];
    }
    get name() {
      return this.type.name;
    }
    get from() {
      return this.context.start + this.context.buffer.buffer[this.index + 1];
    }
    get to() {
      return this.context.start + this.context.buffer.buffer[this.index + 2];
    }
    child(dir, pos, side) {
      let { buffer } = this.context;
      let index = buffer.findChild(this.index + 4, buffer.buffer[this.index + 3], dir, pos - this.context.start, side);
      return index < 0 ? null : new BufferNode(this.context, this, index);
    }
    get firstChild() {
      return this.child(1, 0, 4);
    }
    get lastChild() {
      return this.child(-1, 0, 4);
    }
    childAfter(pos) {
      return this.child(1, pos, 2);
    }
    childBefore(pos) {
      return this.child(-1, pos, -2);
    }
    enter(pos, side, overlays, buffers = true) {
      if (!buffers)
        return null;
      let { buffer } = this.context;
      let index = buffer.findChild(this.index + 4, buffer.buffer[this.index + 3], side > 0 ? 1 : -1, pos - this.context.start, side);
      return index < 0 ? null : new BufferNode(this.context, this, index);
    }
    get parent() {
      return this._parent || this.context.parent.nextSignificantParent();
    }
    externalSibling(dir) {
      return this._parent ? null : this.context.parent.nextChild(this.context.index + dir, dir, 0, 4);
    }
    get nextSibling() {
      let { buffer } = this.context;
      let after = buffer.buffer[this.index + 3];
      if (after < (this._parent ? buffer.buffer[this._parent.index + 3] : buffer.buffer.length))
        return new BufferNode(this.context, this._parent, after);
      return this.externalSibling(1);
    }
    get prevSibling() {
      let { buffer } = this.context;
      let parentStart = this._parent ? this._parent.index + 4 : 0;
      if (this.index == parentStart)
        return this.externalSibling(-1);
      return new BufferNode(this.context, this._parent, buffer.findChild(parentStart, this.index, -1, 0, 4));
    }
    get cursor() {
      return new TreeCursor(this);
    }
    get tree() {
      return null;
    }
    toTree() {
      let children = [], positions = [];
      let { buffer } = this.context;
      let startI = this.index + 4, endI = buffer.buffer[this.index + 3];
      if (endI > startI) {
        let from2 = buffer.buffer[this.index + 1], to = buffer.buffer[this.index + 2];
        children.push(buffer.slice(startI, endI, from2, to));
        positions.push(0);
      }
      return new Tree(this.type, children, positions, this.to - this.from);
    }
    resolve(pos, side = 0) {
      return resolveNode(this, pos, side, false);
    }
    resolveInner(pos, side = 0) {
      return resolveNode(this, pos, side, true);
    }
    enterUnfinishedNodesBefore(pos) {
      return enterUnfinishedNodesBefore(this, pos);
    }
    toString() {
      return this.context.buffer.childString(this.index);
    }
    getChild(type, before = null, after = null) {
      let r = getChildren(this, type, before, after);
      return r.length ? r[0] : null;
    }
    getChildren(type, before = null, after = null) {
      return getChildren(this, type, before, after);
    }
  };
  var TreeCursor = class {
    constructor(node, mode = 0) {
      this.mode = mode;
      this.buffer = null;
      this.stack = [];
      this.index = 0;
      this.bufferNode = null;
      if (node instanceof TreeNode) {
        this.yieldNode(node);
      } else {
        this._tree = node.context.parent;
        this.buffer = node.context;
        for (let n = node._parent; n; n = n._parent)
          this.stack.unshift(n.index);
        this.bufferNode = node;
        this.yieldBuf(node.index);
      }
    }
    get name() {
      return this.type.name;
    }
    yieldNode(node) {
      if (!node)
        return false;
      this._tree = node;
      this.type = node.type;
      this.from = node.from;
      this.to = node.to;
      return true;
    }
    yieldBuf(index, type) {
      this.index = index;
      let { start, buffer } = this.buffer;
      this.type = type || buffer.set.types[buffer.buffer[index]];
      this.from = start + buffer.buffer[index + 1];
      this.to = start + buffer.buffer[index + 2];
      return true;
    }
    yield(node) {
      if (!node)
        return false;
      if (node instanceof TreeNode) {
        this.buffer = null;
        return this.yieldNode(node);
      }
      this.buffer = node.context;
      return this.yieldBuf(node.index, node.type);
    }
    toString() {
      return this.buffer ? this.buffer.buffer.childString(this.index) : this._tree.toString();
    }
    enterChild(dir, pos, side) {
      if (!this.buffer)
        return this.yield(this._tree.nextChild(dir < 0 ? this._tree.node.children.length - 1 : 0, dir, pos, side, this.mode));
      let { buffer } = this.buffer;
      let index = buffer.findChild(this.index + 4, buffer.buffer[this.index + 3], dir, pos - this.buffer.start, side);
      if (index < 0)
        return false;
      this.stack.push(this.index);
      return this.yieldBuf(index);
    }
    firstChild() {
      return this.enterChild(1, 0, 4);
    }
    lastChild() {
      return this.enterChild(-1, 0, 4);
    }
    childAfter(pos) {
      return this.enterChild(1, pos, 2);
    }
    childBefore(pos) {
      return this.enterChild(-1, pos, -2);
    }
    enter(pos, side, overlays = true, buffers = true) {
      if (!this.buffer)
        return this.yield(this._tree.enter(pos, side, overlays && !(this.mode & 1), buffers));
      return buffers ? this.enterChild(1, pos, side) : false;
    }
    parent() {
      if (!this.buffer)
        return this.yieldNode(this.mode & 1 ? this._tree._parent : this._tree.parent);
      if (this.stack.length)
        return this.yieldBuf(this.stack.pop());
      let parent = this.mode & 1 ? this.buffer.parent : this.buffer.parent.nextSignificantParent();
      this.buffer = null;
      return this.yieldNode(parent);
    }
    sibling(dir) {
      if (!this.buffer)
        return !this._tree._parent ? false : this.yield(this._tree.index < 0 ? null : this._tree._parent.nextChild(this._tree.index + dir, dir, 0, 4, this.mode));
      let { buffer } = this.buffer, d = this.stack.length - 1;
      if (dir < 0) {
        let parentStart = d < 0 ? 0 : this.stack[d] + 4;
        if (this.index != parentStart)
          return this.yieldBuf(buffer.findChild(parentStart, this.index, -1, 0, 4));
      } else {
        let after = buffer.buffer[this.index + 3];
        if (after < (d < 0 ? buffer.buffer.length : buffer.buffer[this.stack[d] + 3]))
          return this.yieldBuf(after);
      }
      return d < 0 ? this.yield(this.buffer.parent.nextChild(this.buffer.index + dir, dir, 0, 4, this.mode)) : false;
    }
    nextSibling() {
      return this.sibling(1);
    }
    prevSibling() {
      return this.sibling(-1);
    }
    atLastNode(dir) {
      let index, parent, { buffer } = this;
      if (buffer) {
        if (dir > 0) {
          if (this.index < buffer.buffer.buffer.length)
            return false;
        } else {
          for (let i = 0; i < this.index; i++)
            if (buffer.buffer.buffer[i + 3] < this.index)
              return false;
        }
        ({ index, parent } = buffer);
      } else {
        ({ index, _parent: parent } = this._tree);
      }
      for (; parent; { index, _parent: parent } = parent) {
        if (index > -1)
          for (let i = index + dir, e = dir < 0 ? -1 : parent.node.children.length; i != e; i += dir) {
            let child = parent.node.children[i];
            if (this.mode & 1 || child instanceof TreeBuffer || !child.type.isAnonymous || hasChild(child))
              return false;
          }
      }
      return true;
    }
    move(dir, enter) {
      if (enter && this.enterChild(dir, 0, 4))
        return true;
      for (; ; ) {
        if (this.sibling(dir))
          return true;
        if (this.atLastNode(dir) || !this.parent())
          return false;
      }
    }
    next(enter = true) {
      return this.move(1, enter);
    }
    prev(enter = true) {
      return this.move(-1, enter);
    }
    moveTo(pos, side = 0) {
      while (this.from == this.to || (side < 1 ? this.from >= pos : this.from > pos) || (side > -1 ? this.to <= pos : this.to < pos))
        if (!this.parent())
          break;
      while (this.enterChild(1, pos, side)) {
      }
      return this;
    }
    get node() {
      if (!this.buffer)
        return this._tree;
      let cache = this.bufferNode, result = null, depth = 0;
      if (cache && cache.context == this.buffer) {
        scan:
          for (let index = this.index, d = this.stack.length; d >= 0; ) {
            for (let c = cache; c; c = c._parent)
              if (c.index == index) {
                if (index == this.index)
                  return c;
                result = c;
                depth = d + 1;
                break scan;
              }
            index = this.stack[--d];
          }
      }
      for (let i = depth; i < this.stack.length; i++)
        result = new BufferNode(this.buffer, result, this.stack[i]);
      return this.bufferNode = new BufferNode(this.buffer, result, this.index);
    }
    get tree() {
      return this.buffer ? null : this._tree.node;
    }
  };
  function hasChild(tree) {
    return tree.children.some((ch) => ch instanceof TreeBuffer || !ch.type.isAnonymous || hasChild(ch));
  }
  function buildTree(data) {
    var _a2;
    let { buffer, nodeSet, maxBufferLength = DefaultBufferLength, reused = [], minRepeatType = nodeSet.types.length } = data;
    let cursor = Array.isArray(buffer) ? new FlatBufferCursor(buffer, buffer.length) : buffer;
    let types2 = nodeSet.types;
    let contextHash = 0, lookAhead = 0;
    function takeNode(parentStart, minPos, children2, positions2, inRepeat) {
      let { id: id2, start, end, size } = cursor;
      let lookAheadAtStart = lookAhead;
      while (size < 0) {
        cursor.next();
        if (size == -1) {
          let node2 = reused[id2];
          children2.push(node2);
          positions2.push(start - parentStart);
          return;
        } else if (size == -3) {
          contextHash = id2;
          return;
        } else if (size == -4) {
          lookAhead = id2;
          return;
        } else {
          throw new RangeError(`Unrecognized record size: ${size}`);
        }
      }
      let type = types2[id2], node, buffer2;
      let startPos = start - parentStart;
      if (end - start <= maxBufferLength && (buffer2 = findBufferSize(cursor.pos - minPos, inRepeat))) {
        let data2 = new Uint16Array(buffer2.size - buffer2.skip);
        let endPos = cursor.pos - buffer2.size, index = data2.length;
        while (cursor.pos > endPos)
          index = copyToBuffer(buffer2.start, data2, index);
        node = new TreeBuffer(data2, end - buffer2.start, nodeSet);
        startPos = buffer2.start - parentStart;
      } else {
        let endPos = cursor.pos - size;
        cursor.next();
        let localChildren = [], localPositions = [];
        let localInRepeat = id2 >= minRepeatType ? id2 : -1;
        let lastGroup = 0, lastEnd = end;
        while (cursor.pos > endPos) {
          if (localInRepeat >= 0 && cursor.id == localInRepeat && cursor.size >= 0) {
            if (cursor.end <= lastEnd - maxBufferLength) {
              makeRepeatLeaf(localChildren, localPositions, start, lastGroup, cursor.end, lastEnd, localInRepeat, lookAheadAtStart);
              lastGroup = localChildren.length;
              lastEnd = cursor.end;
            }
            cursor.next();
          } else {
            takeNode(start, endPos, localChildren, localPositions, localInRepeat);
          }
        }
        if (localInRepeat >= 0 && lastGroup > 0 && lastGroup < localChildren.length)
          makeRepeatLeaf(localChildren, localPositions, start, lastGroup, start, lastEnd, localInRepeat, lookAheadAtStart);
        localChildren.reverse();
        localPositions.reverse();
        if (localInRepeat > -1 && lastGroup > 0) {
          let make = makeBalanced(type);
          node = balanceRange(type, localChildren, localPositions, 0, localChildren.length, 0, end - start, make, make);
        } else {
          node = makeTree(type, localChildren, localPositions, end - start, lookAheadAtStart - end);
        }
      }
      children2.push(node);
      positions2.push(startPos);
    }
    function makeBalanced(type) {
      return (children2, positions2, length2) => {
        let lookAhead2 = 0, lastI = children2.length - 1, last, lookAheadProp;
        if (lastI >= 0 && (last = children2[lastI]) instanceof Tree) {
          if (!lastI && last.type == type && last.length == length2)
            return last;
          if (lookAheadProp = last.prop(NodeProp.lookAhead))
            lookAhead2 = positions2[lastI] + last.length + lookAheadProp;
        }
        return makeTree(type, children2, positions2, length2, lookAhead2);
      };
    }
    function makeRepeatLeaf(children2, positions2, base2, i, from2, to, type, lookAhead2) {
      let localChildren = [], localPositions = [];
      while (children2.length > i) {
        localChildren.push(children2.pop());
        localPositions.push(positions2.pop() + base2 - from2);
      }
      children2.push(makeTree(nodeSet.types[type], localChildren, localPositions, to - from2, lookAhead2 - to));
      positions2.push(from2 - base2);
    }
    function makeTree(type, children2, positions2, length2, lookAhead2 = 0, props) {
      if (contextHash) {
        let pair2 = [NodeProp.contextHash, contextHash];
        props = props ? [pair2].concat(props) : [pair2];
      }
      if (lookAhead2 > 25) {
        let pair2 = [NodeProp.lookAhead, lookAhead2];
        props = props ? [pair2].concat(props) : [pair2];
      }
      return new Tree(type, children2, positions2, length2, props);
    }
    function findBufferSize(maxSize, inRepeat) {
      let fork = cursor.fork();
      let size = 0, start = 0, skip = 0, minStart = fork.end - maxBufferLength;
      let result = { size: 0, start: 0, skip: 0 };
      scan:
        for (let minPos = fork.pos - maxSize; fork.pos > minPos; ) {
          let nodeSize2 = fork.size;
          if (fork.id == inRepeat && nodeSize2 >= 0) {
            result.size = size;
            result.start = start;
            result.skip = skip;
            skip += 4;
            size += 4;
            fork.next();
            continue;
          }
          let startPos = fork.pos - nodeSize2;
          if (nodeSize2 < 0 || startPos < minPos || fork.start < minStart)
            break;
          let localSkipped = fork.id >= minRepeatType ? 4 : 0;
          let nodeStart2 = fork.start;
          fork.next();
          while (fork.pos > startPos) {
            if (fork.size < 0) {
              if (fork.size == -3)
                localSkipped += 4;
              else
                break scan;
            } else if (fork.id >= minRepeatType) {
              localSkipped += 4;
            }
            fork.next();
          }
          start = nodeStart2;
          size += nodeSize2;
          skip += localSkipped;
        }
      if (inRepeat < 0 || size == maxSize) {
        result.size = size;
        result.start = start;
        result.skip = skip;
      }
      return result.size > 4 ? result : void 0;
    }
    function copyToBuffer(bufferStart, buffer2, index) {
      let { id: id2, start, end, size } = cursor;
      cursor.next();
      if (size >= 0 && id2 < minRepeatType) {
        let startIndex = index;
        if (size > 4) {
          let endPos = cursor.pos - (size - 4);
          while (cursor.pos > endPos)
            index = copyToBuffer(bufferStart, buffer2, index);
        }
        buffer2[--index] = startIndex;
        buffer2[--index] = end - bufferStart;
        buffer2[--index] = start - bufferStart;
        buffer2[--index] = id2;
      } else if (size == -3) {
        contextHash = id2;
      } else if (size == -4) {
        lookAhead = id2;
      }
      return index;
    }
    let children = [], positions = [];
    while (cursor.pos > 0)
      takeNode(data.start || 0, data.bufferStart || 0, children, positions, -1);
    let length = (_a2 = data.length) !== null && _a2 !== void 0 ? _a2 : children.length ? positions[0] + children[0].length : 0;
    return new Tree(types2[data.topID], children.reverse(), positions.reverse(), length);
  }
  var nodeSizeCache = /* @__PURE__ */ new WeakMap();
  function nodeSize(balanceType, node) {
    if (!balanceType.isAnonymous || node instanceof TreeBuffer || node.type != balanceType)
      return 1;
    let size = nodeSizeCache.get(node);
    if (size == null) {
      size = 1;
      for (let child of node.children) {
        if (child.type != balanceType || !(child instanceof Tree)) {
          size = 1;
          break;
        }
        size += nodeSize(balanceType, child);
      }
      nodeSizeCache.set(node, size);
    }
    return size;
  }
  function balanceRange(balanceType, children, positions, from2, to, start, length, mkTop, mkTree) {
    let total = 0;
    for (let i = from2; i < to; i++)
      total += nodeSize(balanceType, children[i]);
    let maxChild = Math.ceil(total * 1.5 / 8);
    let localChildren = [], localPositions = [];
    function divide(children2, positions2, from3, to2, offset) {
      for (let i = from3; i < to2; ) {
        let groupFrom = i, groupStart = positions2[i], groupSize = nodeSize(balanceType, children2[i]);
        i++;
        for (; i < to2; i++) {
          let nextSize = nodeSize(balanceType, children2[i]);
          if (groupSize + nextSize >= maxChild)
            break;
          groupSize += nextSize;
        }
        if (i == groupFrom + 1) {
          if (groupSize > maxChild) {
            let only = children2[groupFrom];
            divide(only.children, only.positions, 0, only.children.length, positions2[groupFrom] + offset);
            continue;
          }
          localChildren.push(children2[groupFrom]);
        } else {
          let length2 = positions2[i - 1] + children2[i - 1].length - groupStart;
          localChildren.push(balanceRange(balanceType, children2, positions2, groupFrom, i, groupStart, length2, null, mkTree));
        }
        localPositions.push(groupStart + offset - start);
      }
    }
    divide(children, positions, from2, to, 0);
    return (mkTop || mkTree)(localChildren, localPositions, length);
  }
  var TreeFragment = class {
    constructor(from2, to, tree, offset, openStart = false, openEnd = false) {
      this.from = from2;
      this.to = to;
      this.tree = tree;
      this.offset = offset;
      this.open = (openStart ? 1 : 0) | (openEnd ? 2 : 0);
    }
    get openStart() {
      return (this.open & 1) > 0;
    }
    get openEnd() {
      return (this.open & 2) > 0;
    }
    static addTree(tree, fragments = [], partial = false) {
      let result = [new TreeFragment(0, tree.length, tree, 0, false, partial)];
      for (let f of fragments)
        if (f.to > tree.length)
          result.push(f);
      return result;
    }
    static applyChanges(fragments, changes, minGap = 128) {
      if (!changes.length)
        return fragments;
      let result = [];
      let fI = 1, nextF = fragments.length ? fragments[0] : null;
      for (let cI = 0, pos = 0, off = 0; ; cI++) {
        let nextC = cI < changes.length ? changes[cI] : null;
        let nextPos = nextC ? nextC.fromA : 1e9;
        if (nextPos - pos >= minGap)
          while (nextF && nextF.from < nextPos) {
            let cut = nextF;
            if (pos >= cut.from || nextPos <= cut.to || off) {
              let fFrom = Math.max(cut.from, pos) - off, fTo = Math.min(cut.to, nextPos) - off;
              cut = fFrom >= fTo ? null : new TreeFragment(fFrom, fTo, cut.tree, cut.offset + off, cI > 0, !!nextC);
            }
            if (cut)
              result.push(cut);
            if (nextF.to > nextPos)
              break;
            nextF = fI < fragments.length ? fragments[fI++] : null;
          }
        if (!nextC)
          break;
        pos = nextC.toA;
        off = nextC.toA - nextC.toB;
      }
      return result;
    }
  };
  var Parser = class {
    startParse(input, fragments, ranges) {
      if (typeof input == "string")
        input = new StringInput(input);
      ranges = !ranges ? [new Range2(0, input.length)] : ranges.length ? ranges.map((r) => new Range2(r.from, r.to)) : [new Range2(0, 0)];
      return this.createParse(input, fragments || [], ranges);
    }
    parse(input, fragments, ranges) {
      let parse = this.startParse(input, fragments, ranges);
      for (; ; ) {
        let done = parse.advance();
        if (done)
          return done;
      }
    }
  };
  var StringInput = class {
    constructor(string2) {
      this.string = string2;
    }
    get length() {
      return this.string.length;
    }
    chunk(from2) {
      return this.string.slice(from2);
    }
    get lineChunks() {
      return false;
    }
    read(from2, to) {
      return this.string.slice(from2, to);
    }
  };
  function parseMixed(nest) {
    return (parse, input, fragments, ranges) => new MixedParse(parse, nest, input, fragments, ranges);
  }
  var InnerParse = class {
    constructor(parser6, parse, overlay, target, ranges) {
      this.parser = parser6;
      this.parse = parse;
      this.overlay = overlay;
      this.target = target;
      this.ranges = ranges;
    }
  };
  var ActiveOverlay = class {
    constructor(parser6, predicate, mounts, index, start, target, prev) {
      this.parser = parser6;
      this.predicate = predicate;
      this.mounts = mounts;
      this.index = index;
      this.start = start;
      this.target = target;
      this.prev = prev;
      this.depth = 0;
      this.ranges = [];
    }
  };
  var stoppedInner = new NodeProp({ perNode: true });
  var MixedParse = class {
    constructor(base2, nest, input, fragments, ranges) {
      this.nest = nest;
      this.input = input;
      this.fragments = fragments;
      this.ranges = ranges;
      this.inner = [];
      this.innerDone = 0;
      this.baseTree = null;
      this.stoppedAt = null;
      this.baseParse = base2;
    }
    advance() {
      if (this.baseParse) {
        let done2 = this.baseParse.advance();
        if (!done2)
          return null;
        this.baseParse = null;
        this.baseTree = done2;
        this.startInner();
        if (this.stoppedAt != null)
          for (let inner2 of this.inner)
            inner2.parse.stopAt(this.stoppedAt);
      }
      if (this.innerDone == this.inner.length) {
        let result = this.baseTree;
        if (this.stoppedAt != null)
          result = new Tree(result.type, result.children, result.positions, result.length, result.propValues.concat([[stoppedInner, this.stoppedAt]]));
        return result;
      }
      let inner = this.inner[this.innerDone], done = inner.parse.advance();
      if (done) {
        this.innerDone++;
        let props = Object.assign(/* @__PURE__ */ Object.create(null), inner.target.props);
        props[NodeProp.mounted.id] = new MountedTree(done, inner.overlay, inner.parser);
        inner.target.props = props;
      }
      return null;
    }
    get parsedPos() {
      if (this.baseParse)
        return 0;
      let pos = this.input.length;
      for (let i = this.innerDone; i < this.inner.length; i++) {
        if (this.inner[i].ranges[0].from < pos)
          pos = Math.min(pos, this.inner[i].parse.parsedPos);
      }
      return pos;
    }
    stopAt(pos) {
      this.stoppedAt = pos;
      if (this.baseParse)
        this.baseParse.stopAt(pos);
      else
        for (let i = this.innerDone; i < this.inner.length; i++)
          this.inner[i].parse.stopAt(pos);
    }
    startInner() {
      let fragmentCursor = new FragmentCursor(this.fragments);
      let overlay = null;
      let covered = null;
      let cursor = new TreeCursor(new TreeNode(this.baseTree, this.ranges[0].from, 0, null), 1);
      scan:
        for (let nest, isCovered; this.stoppedAt == null || cursor.from < this.stoppedAt; ) {
          let enter = true, range;
          if (fragmentCursor.hasNode(cursor)) {
            if (overlay) {
              let match2 = overlay.mounts.find((m) => m.frag.from <= cursor.from && m.frag.to >= cursor.to && m.mount.overlay);
              if (match2)
                for (let r of match2.mount.overlay) {
                  let from2 = r.from + match2.pos, to = r.to + match2.pos;
                  if (from2 >= cursor.from && to <= cursor.to && !overlay.ranges.some((r2) => r2.from < to && r2.to > from2))
                    overlay.ranges.push({ from: from2, to });
                }
            }
            enter = false;
          } else if (covered && (isCovered = checkCover(covered.ranges, cursor.from, cursor.to))) {
            enter = isCovered != 2;
          } else if (!cursor.type.isAnonymous && cursor.from < cursor.to && (nest = this.nest(cursor, this.input))) {
            if (!cursor.tree)
              materialize(cursor);
            let oldMounts = fragmentCursor.findMounts(cursor.from, nest.parser);
            if (typeof nest.overlay == "function") {
              overlay = new ActiveOverlay(nest.parser, nest.overlay, oldMounts, this.inner.length, cursor.from, cursor.tree, overlay);
            } else {
              let ranges = punchRanges(this.ranges, nest.overlay || [new Range2(cursor.from, cursor.to)]);
              if (ranges.length)
                this.inner.push(new InnerParse(nest.parser, nest.parser.startParse(this.input, enterFragments(oldMounts, ranges), ranges), nest.overlay ? nest.overlay.map((r) => new Range2(r.from - cursor.from, r.to - cursor.from)) : null, cursor.tree, ranges));
              if (!nest.overlay)
                enter = false;
              else if (ranges.length)
                covered = { ranges, depth: 0, prev: covered };
            }
          } else if (overlay && (range = overlay.predicate(cursor))) {
            if (range === true)
              range = new Range2(cursor.from, cursor.to);
            if (range.from < range.to)
              overlay.ranges.push(range);
          }
          if (enter && cursor.firstChild()) {
            if (overlay)
              overlay.depth++;
            if (covered)
              covered.depth++;
          } else {
            for (; ; ) {
              if (cursor.nextSibling())
                break;
              if (!cursor.parent())
                break scan;
              if (overlay && !--overlay.depth) {
                let ranges = punchRanges(this.ranges, overlay.ranges);
                if (ranges.length)
                  this.inner.splice(overlay.index, 0, new InnerParse(overlay.parser, overlay.parser.startParse(this.input, enterFragments(overlay.mounts, ranges), ranges), overlay.ranges.map((r) => new Range2(r.from - overlay.start, r.to - overlay.start)), overlay.target, ranges));
                overlay = overlay.prev;
              }
              if (covered && !--covered.depth)
                covered = covered.prev;
            }
          }
        }
    }
  };
  function checkCover(covered, from2, to) {
    for (let range of covered) {
      if (range.from >= to)
        break;
      if (range.to > from2)
        return range.from <= from2 && range.to >= to ? 2 : 1;
    }
    return 0;
  }
  function sliceBuf(buf, startI, endI, nodes, positions, off) {
    if (startI < endI) {
      let from2 = buf.buffer[startI + 1], to = buf.buffer[endI - 2];
      nodes.push(buf.slice(startI, endI, from2, to));
      positions.push(from2 - off);
    }
  }
  function materialize(cursor) {
    let { node } = cursor, depth = 0;
    do {
      cursor.parent();
      depth++;
    } while (!cursor.tree);
    let i = 0, base2 = cursor.tree, off = 0;
    for (; ; i++) {
      off = base2.positions[i] + cursor.from;
      if (off <= node.from && off + base2.children[i].length >= node.to)
        break;
    }
    let buf = base2.children[i], b = buf.buffer;
    function split(startI, endI, type, innerOffset, length) {
      let i2 = startI;
      while (b[i2 + 2] + off <= node.from)
        i2 = b[i2 + 3];
      let children = [], positions = [];
      sliceBuf(buf, startI, i2, children, positions, innerOffset);
      let from2 = b[i2 + 1], to = b[i2 + 2];
      let isTarget = from2 + off == node.from && to + off == node.to && b[i2] == node.type.id;
      children.push(isTarget ? node.toTree() : split(i2 + 4, b[i2 + 3], buf.set.types[b[i2]], from2, to - from2));
      positions.push(from2 - innerOffset);
      sliceBuf(buf, b[i2 + 3], endI, children, positions, innerOffset);
      return new Tree(type, children, positions, length);
    }
    base2.children[i] = split(0, b.length, NodeType.none, 0, buf.length);
    for (let d = 0; d <= depth; d++)
      cursor.childAfter(node.from);
  }
  var StructureCursor = class {
    constructor(root, offset) {
      this.offset = offset;
      this.done = false;
      this.cursor = root.fullCursor();
    }
    moveTo(pos) {
      let { cursor } = this, p = pos - this.offset;
      while (!this.done && cursor.from < p) {
        if (cursor.to >= pos && cursor.enter(p, 1, false, false))
          ;
        else if (!cursor.next(false))
          this.done = true;
      }
    }
    hasNode(cursor) {
      this.moveTo(cursor.from);
      if (!this.done && this.cursor.from + this.offset == cursor.from && this.cursor.tree) {
        for (let tree = this.cursor.tree; ; ) {
          if (tree == cursor.tree)
            return true;
          if (tree.children.length && tree.positions[0] == 0 && tree.children[0] instanceof Tree)
            tree = tree.children[0];
          else
            break;
        }
      }
      return false;
    }
  };
  var FragmentCursor = class {
    constructor(fragments) {
      var _a2;
      this.fragments = fragments;
      this.curTo = 0;
      this.fragI = 0;
      if (fragments.length) {
        let first = this.curFrag = fragments[0];
        this.curTo = (_a2 = first.tree.prop(stoppedInner)) !== null && _a2 !== void 0 ? _a2 : first.to;
        this.inner = new StructureCursor(first.tree, -first.offset);
      } else {
        this.curFrag = this.inner = null;
      }
    }
    hasNode(node) {
      while (this.curFrag && node.from >= this.curTo)
        this.nextFrag();
      return this.curFrag && this.curFrag.from <= node.from && this.curTo >= node.to && this.inner.hasNode(node);
    }
    nextFrag() {
      var _a2;
      this.fragI++;
      if (this.fragI == this.fragments.length) {
        this.curFrag = this.inner = null;
      } else {
        let frag = this.curFrag = this.fragments[this.fragI];
        this.curTo = (_a2 = frag.tree.prop(stoppedInner)) !== null && _a2 !== void 0 ? _a2 : frag.to;
        this.inner = new StructureCursor(frag.tree, -frag.offset);
      }
    }
    findMounts(pos, parser6) {
      var _a2;
      let result = [];
      if (this.inner) {
        this.inner.cursor.moveTo(pos, 1);
        for (let pos2 = this.inner.cursor.node; pos2; pos2 = pos2.parent) {
          let mount = (_a2 = pos2.tree) === null || _a2 === void 0 ? void 0 : _a2.prop(NodeProp.mounted);
          if (mount && mount.parser == parser6) {
            for (let i = this.fragI; i < this.fragments.length; i++) {
              let frag = this.fragments[i];
              if (frag.from >= pos2.to)
                break;
              if (frag.tree == this.curFrag.tree)
                result.push({
                  frag,
                  pos: pos2.from - frag.offset,
                  mount
                });
            }
          }
        }
      }
      return result;
    }
  };
  function punchRanges(outer, ranges) {
    let copy = null, current = ranges;
    for (let i = 1, j = 0; i < outer.length; i++) {
      let gapFrom = outer[i - 1].to, gapTo = outer[i].from;
      for (; j < current.length; j++) {
        let r = current[j];
        if (r.from >= gapTo)
          break;
        if (r.to <= gapFrom)
          continue;
        if (!copy)
          current = copy = ranges.slice();
        if (r.from < gapFrom) {
          copy[j] = new Range2(r.from, gapFrom);
          if (r.to > gapTo)
            copy.splice(j + 1, 0, new Range2(gapTo, r.to));
        } else if (r.to > gapTo) {
          copy[j--] = new Range2(gapTo, r.to);
        } else {
          copy.splice(j--, 1);
        }
      }
    }
    return current;
  }
  function findCoverChanges(a, b, from2, to) {
    let iA = 0, iB = 0, inA = false, inB = false, pos = -1e9;
    let result = [];
    for (; ; ) {
      let nextA = iA == a.length ? 1e9 : inA ? a[iA].to : a[iA].from;
      let nextB = iB == b.length ? 1e9 : inB ? b[iB].to : b[iB].from;
      if (inA != inB) {
        let start = Math.max(pos, from2), end = Math.min(nextA, nextB, to);
        if (start < end)
          result.push(new Range2(start, end));
      }
      pos = Math.min(nextA, nextB);
      if (pos == 1e9)
        break;
      if (nextA == pos) {
        if (!inA)
          inA = true;
        else {
          inA = false;
          iA++;
        }
      }
      if (nextB == pos) {
        if (!inB)
          inB = true;
        else {
          inB = false;
          iB++;
        }
      }
    }
    return result;
  }
  function enterFragments(mounts, ranges) {
    let result = [];
    for (let { pos, mount, frag } of mounts) {
      let startPos = pos + (mount.overlay ? mount.overlay[0].from : 0), endPos = startPos + mount.tree.length;
      let from2 = Math.max(frag.from, startPos), to = Math.min(frag.to, endPos);
      if (mount.overlay) {
        let overlay = mount.overlay.map((r) => new Range2(r.from + pos, r.to + pos));
        let changes = findCoverChanges(ranges, overlay, from2, to);
        for (let i = 0, pos2 = from2; ; i++) {
          let last = i == changes.length, end = last ? to : changes[i].from;
          if (end > pos2)
            result.push(new TreeFragment(pos2, end, mount.tree, -startPos, frag.from >= pos2, frag.to <= end));
          if (last)
            break;
          pos2 = changes[i].to;
        }
      } else {
        result.push(new TreeFragment(from2, to, mount.tree, -startPos, frag.from >= startPos, frag.to <= endPos));
      }
    }
    return result;
  }

  // node_modules/@codemirror/language/dist/index.js
  var _a;
  var languageDataProp = /* @__PURE__ */ new NodeProp();
  function defineLanguageFacet(baseData) {
    return Facet.define({
      combine: baseData ? (values2) => values2.concat(baseData) : void 0
    });
  }
  var Language = class {
    constructor(data, parser6, topNode, extraExtensions = []) {
      this.data = data;
      this.topNode = topNode;
      if (!EditorState.prototype.hasOwnProperty("tree"))
        Object.defineProperty(EditorState.prototype, "tree", { get() {
          return syntaxTree(this);
        } });
      this.parser = parser6;
      this.extension = [
        language.of(this),
        EditorState.languageData.of((state, pos, side) => state.facet(languageDataFacetAt(state, pos, side)))
      ].concat(extraExtensions);
    }
    isActiveAt(state, pos, side = -1) {
      return languageDataFacetAt(state, pos, side) == this.data;
    }
    findRegions(state) {
      let lang = state.facet(language);
      if ((lang === null || lang === void 0 ? void 0 : lang.data) == this.data)
        return [{ from: 0, to: state.doc.length }];
      if (!lang || !lang.allowsNesting)
        return [];
      let result = [];
      let explore = (tree, from2) => {
        if (tree.prop(languageDataProp) == this.data) {
          result.push({ from: from2, to: from2 + tree.length });
          return;
        }
        let mount = tree.prop(NodeProp.mounted);
        if (mount) {
          if (mount.tree.prop(languageDataProp) == this.data) {
            if (mount.overlay)
              for (let r of mount.overlay)
                result.push({ from: r.from + from2, to: r.to + from2 });
            else
              result.push({ from: from2, to: from2 + tree.length });
            return;
          } else if (mount.overlay) {
            let size = result.length;
            explore(mount.tree, mount.overlay[0].from + from2);
            if (result.length > size)
              return;
          }
        }
        for (let i = 0; i < tree.children.length; i++) {
          let ch = tree.children[i];
          if (ch instanceof Tree)
            explore(ch, tree.positions[i] + from2);
        }
      };
      explore(syntaxTree(state), 0);
      return result;
    }
    get allowsNesting() {
      return true;
    }
  };
  Language.setState = /* @__PURE__ */ StateEffect.define();
  function languageDataFacetAt(state, pos, side) {
    let topLang = state.facet(language);
    if (!topLang)
      return null;
    let facet = topLang.data;
    if (topLang.allowsNesting) {
      for (let node = syntaxTree(state).topNode; node; node = node.enter(pos, side, true, false))
        facet = node.type.prop(languageDataProp) || facet;
    }
    return facet;
  }
  var LRLanguage = class extends Language {
    constructor(data, parser6) {
      super(data, parser6, parser6.topNode);
      this.parser = parser6;
    }
    static define(spec) {
      let data = defineLanguageFacet(spec.languageData);
      return new LRLanguage(data, spec.parser.configure({
        props: [languageDataProp.add((type) => type.isTop ? data : void 0)]
      }));
    }
    configure(options) {
      return new LRLanguage(this.data, this.parser.configure(options));
    }
    get allowsNesting() {
      return this.parser.wrappers.length > 0;
    }
  };
  function syntaxTree(state) {
    let field = state.field(Language.state, false);
    return field ? field.tree : Tree.empty;
  }
  var DocInput = class {
    constructor(doc2, length = doc2.length) {
      this.doc = doc2;
      this.length = length;
      this.cursorPos = 0;
      this.string = "";
      this.cursor = doc2.iter();
    }
    syncTo(pos) {
      this.string = this.cursor.next(pos - this.cursorPos).value;
      this.cursorPos = pos + this.string.length;
      return this.cursorPos - this.string.length;
    }
    chunk(pos) {
      this.syncTo(pos);
      return this.string;
    }
    get lineChunks() {
      return true;
    }
    read(from2, to) {
      let stringStart = this.cursorPos - this.string.length;
      if (from2 < stringStart || to >= this.cursorPos)
        return this.doc.sliceString(from2, to);
      else
        return this.string.slice(from2 - stringStart, to - stringStart);
    }
  };
  var currentContext = null;
  var ParseContext = class {
    constructor(parser6, state, fragments = [], tree, treeLen, viewport, skipped, scheduleOn) {
      this.parser = parser6;
      this.state = state;
      this.fragments = fragments;
      this.tree = tree;
      this.treeLen = treeLen;
      this.viewport = viewport;
      this.skipped = skipped;
      this.scheduleOn = scheduleOn;
      this.parse = null;
      this.tempSkipped = [];
    }
    startParse() {
      return this.parser.startParse(new DocInput(this.state.doc), this.fragments);
    }
    work(until, upto) {
      if (upto != null && upto >= this.state.doc.length)
        upto = void 0;
      if (this.tree != Tree.empty && this.isDone(upto !== null && upto !== void 0 ? upto : this.state.doc.length)) {
        this.takeTree();
        return true;
      }
      return this.withContext(() => {
        var _a2;
        if (typeof until == "number") {
          let endTime = Date.now() + until;
          until = () => Date.now() > endTime;
        }
        if (!this.parse)
          this.parse = this.startParse();
        if (upto != null && (this.parse.stoppedAt == null || this.parse.stoppedAt > upto) && upto < this.state.doc.length)
          this.parse.stopAt(upto);
        for (; ; ) {
          let done = this.parse.advance();
          if (done) {
            this.fragments = this.withoutTempSkipped(TreeFragment.addTree(done, this.fragments, this.parse.stoppedAt != null));
            this.treeLen = (_a2 = this.parse.stoppedAt) !== null && _a2 !== void 0 ? _a2 : this.state.doc.length;
            this.tree = done;
            this.parse = null;
            if (this.treeLen < (upto !== null && upto !== void 0 ? upto : this.state.doc.length))
              this.parse = this.startParse();
            else
              return true;
          }
          if (until())
            return false;
        }
      });
    }
    takeTree() {
      let pos, tree;
      if (this.parse && (pos = this.parse.parsedPos) >= this.treeLen) {
        if (this.parse.stoppedAt == null || this.parse.stoppedAt > pos)
          this.parse.stopAt(pos);
        this.withContext(() => {
          while (!(tree = this.parse.advance())) {
          }
        });
        this.treeLen = pos;
        this.tree = tree;
        this.fragments = this.withoutTempSkipped(TreeFragment.addTree(this.tree, this.fragments, true));
        this.parse = null;
      }
    }
    withContext(f) {
      let prev = currentContext;
      currentContext = this;
      try {
        return f();
      } finally {
        currentContext = prev;
      }
    }
    withoutTempSkipped(fragments) {
      for (let r; r = this.tempSkipped.pop(); )
        fragments = cutFragments(fragments, r.from, r.to);
      return fragments;
    }
    changes(changes, newState) {
      let { fragments, tree, treeLen, viewport, skipped } = this;
      this.takeTree();
      if (!changes.empty) {
        let ranges = [];
        changes.iterChangedRanges((fromA, toA, fromB, toB) => ranges.push({ fromA, toA, fromB, toB }));
        fragments = TreeFragment.applyChanges(fragments, ranges);
        tree = Tree.empty;
        treeLen = 0;
        viewport = { from: changes.mapPos(viewport.from, -1), to: changes.mapPos(viewport.to, 1) };
        if (this.skipped.length) {
          skipped = [];
          for (let r of this.skipped) {
            let from2 = changes.mapPos(r.from, 1), to = changes.mapPos(r.to, -1);
            if (from2 < to)
              skipped.push({ from: from2, to });
          }
        }
      }
      return new ParseContext(this.parser, newState, fragments, tree, treeLen, viewport, skipped, this.scheduleOn);
    }
    updateViewport(viewport) {
      if (this.viewport.from == viewport.from && this.viewport.to == viewport.to)
        return false;
      this.viewport = viewport;
      let startLen = this.skipped.length;
      for (let i = 0; i < this.skipped.length; i++) {
        let { from: from2, to } = this.skipped[i];
        if (from2 < viewport.to && to > viewport.from) {
          this.fragments = cutFragments(this.fragments, from2, to);
          this.skipped.splice(i--, 1);
        }
      }
      if (this.skipped.length >= startLen)
        return false;
      this.reset();
      return true;
    }
    reset() {
      if (this.parse) {
        this.takeTree();
        this.parse = null;
      }
    }
    skipUntilInView(from2, to) {
      this.skipped.push({ from: from2, to });
    }
    static getSkippingParser(until) {
      return new class extends Parser {
        createParse(input, fragments, ranges) {
          let from2 = ranges[0].from, to = ranges[ranges.length - 1].to;
          let parser6 = {
            parsedPos: from2,
            advance() {
              let cx = currentContext;
              if (cx) {
                for (let r of ranges)
                  cx.tempSkipped.push(r);
                if (until)
                  cx.scheduleOn = cx.scheduleOn ? Promise.all([cx.scheduleOn, until]) : until;
              }
              this.parsedPos = to;
              return new Tree(NodeType.none, [], [], to - from2);
            },
            stoppedAt: null,
            stopAt() {
            }
          };
          return parser6;
        }
      }();
    }
    isDone(upto) {
      upto = Math.min(upto, this.state.doc.length);
      let frags = this.fragments;
      return this.treeLen >= upto && frags.length && frags[0].from == 0 && frags[0].to >= upto;
    }
    static get() {
      return currentContext;
    }
  };
  function cutFragments(fragments, from2, to) {
    return TreeFragment.applyChanges(fragments, [{ fromA: from2, toA: to, fromB: from2, toB: to }]);
  }
  var LanguageState = class {
    constructor(context) {
      this.context = context;
      this.tree = context.tree;
    }
    apply(tr) {
      if (!tr.docChanged && this.tree == this.context.tree)
        return this;
      let newCx = this.context.changes(tr.changes, tr.state);
      let upto = this.context.treeLen == tr.startState.doc.length ? void 0 : Math.max(tr.changes.mapPos(this.context.treeLen), newCx.viewport.to);
      if (!newCx.work(20, upto))
        newCx.takeTree();
      return new LanguageState(newCx);
    }
    static init(state) {
      let vpTo = Math.min(3e3, state.doc.length);
      let parseState = new ParseContext(state.facet(language).parser, state, [], Tree.empty, 0, { from: 0, to: vpTo }, [], null);
      if (!parseState.work(20, vpTo))
        parseState.takeTree();
      return new LanguageState(parseState);
    }
  };
  Language.state = /* @__PURE__ */ StateField.define({
    create: LanguageState.init,
    update(value, tr) {
      for (let e of tr.effects)
        if (e.is(Language.setState))
          return e.value;
      if (tr.startState.facet(language) != tr.state.facet(language))
        return LanguageState.init(tr.state);
      return value.apply(tr);
    }
  });
  var requestIdle = (callback) => {
    let timeout = setTimeout(() => callback(), 500);
    return () => clearTimeout(timeout);
  };
  if (typeof requestIdleCallback != "undefined")
    requestIdle = (callback) => {
      let idle = -1, timeout = setTimeout(() => {
        idle = requestIdleCallback(callback, { timeout: 500 - 100 });
      }, 100);
      return () => idle < 0 ? clearTimeout(timeout) : cancelIdleCallback(idle);
    };
  var isInputPending = typeof navigator != "undefined" && ((_a = navigator.scheduling) === null || _a === void 0 ? void 0 : _a.isInputPending) ? () => navigator.scheduling.isInputPending() : null;
  var parseWorker = /* @__PURE__ */ ViewPlugin.fromClass(class ParseWorker {
    constructor(view) {
      this.view = view;
      this.working = null;
      this.workScheduled = 0;
      this.chunkEnd = -1;
      this.chunkBudget = -1;
      this.work = this.work.bind(this);
      this.scheduleWork();
    }
    update(update) {
      let cx = this.view.state.field(Language.state).context;
      if (cx.updateViewport(update.view.viewport) || this.view.viewport.to > cx.treeLen)
        this.scheduleWork();
      if (update.docChanged) {
        if (this.view.hasFocus)
          this.chunkBudget += 50;
        this.scheduleWork();
      }
      this.checkAsyncSchedule(cx);
    }
    scheduleWork() {
      if (this.working)
        return;
      let { state } = this.view, field = state.field(Language.state);
      if (field.tree != field.context.tree || !field.context.isDone(state.doc.length))
        this.working = requestIdle(this.work);
    }
    work(deadline) {
      this.working = null;
      let now = Date.now();
      if (this.chunkEnd < now && (this.chunkEnd < 0 || this.view.hasFocus)) {
        this.chunkEnd = now + 3e4;
        this.chunkBudget = 3e3;
      }
      if (this.chunkBudget <= 0)
        return;
      let { state, viewport: { to: vpTo } } = this.view, field = state.field(Language.state);
      if (field.tree == field.context.tree && field.context.isDone(vpTo + 1e5))
        return;
      let endTime = Date.now() + Math.min(this.chunkBudget, 100, deadline && !isInputPending ? Math.max(25, deadline.timeRemaining() - 5) : 1e9);
      let viewportFirst = field.context.treeLen < vpTo && state.doc.length > vpTo + 1e3;
      let done = field.context.work(() => {
        return isInputPending && isInputPending() || Date.now() > endTime;
      }, vpTo + (viewportFirst ? 0 : 1e5));
      this.chunkBudget -= Date.now() - now;
      if (done || this.chunkBudget <= 0) {
        field.context.takeTree();
        this.view.dispatch({ effects: Language.setState.of(new LanguageState(field.context)) });
      }
      if (this.chunkBudget > 0 && !(done && !viewportFirst))
        this.scheduleWork();
      this.checkAsyncSchedule(field.context);
    }
    checkAsyncSchedule(cx) {
      if (cx.scheduleOn) {
        this.workScheduled++;
        cx.scheduleOn.then(() => this.scheduleWork()).catch((err) => logException(this.view.state, err)).then(() => this.workScheduled--);
        cx.scheduleOn = null;
      }
    }
    destroy() {
      if (this.working)
        this.working();
    }
    isWorking() {
      return !!(this.working || this.workScheduled > 0);
    }
  }, {
    eventHandlers: { focus() {
      this.scheduleWork();
    } }
  });
  var language = /* @__PURE__ */ Facet.define({
    combine(languages) {
      return languages.length ? languages[0] : null;
    },
    enables: [Language.state, parseWorker]
  });
  var LanguageSupport = class {
    constructor(language2, support = []) {
      this.language = language2;
      this.support = support;
      this.extension = [language2, support];
    }
  };
  var indentService = /* @__PURE__ */ Facet.define();
  var indentUnit = /* @__PURE__ */ Facet.define({
    combine: (values2) => {
      if (!values2.length)
        return "  ";
      if (!/^(?: +|\t+)$/.test(values2[0]))
        throw new Error("Invalid indent unit: " + JSON.stringify(values2[0]));
      return values2[0];
    }
  });
  function getIndentUnit(state) {
    let unit = state.facet(indentUnit);
    return unit.charCodeAt(0) == 9 ? state.tabSize * unit.length : unit.length;
  }
  function indentString(state, cols) {
    let result = "", ts = state.tabSize;
    if (state.facet(indentUnit).charCodeAt(0) == 9)
      while (cols >= ts) {
        result += "	";
        cols -= ts;
      }
    for (let i = 0; i < cols; i++)
      result += " ";
    return result;
  }
  function getIndentation(context, pos) {
    if (context instanceof EditorState)
      context = new IndentContext(context);
    for (let service of context.state.facet(indentService)) {
      let result = service(context, pos);
      if (result != null)
        return result;
    }
    let tree = syntaxTree(context.state);
    return tree ? syntaxIndentation(context, tree, pos) : null;
  }
  var IndentContext = class {
    constructor(state, options = {}) {
      this.state = state;
      this.options = options;
      this.unit = getIndentUnit(state);
    }
    lineAt(pos, bias = 1) {
      let line = this.state.doc.lineAt(pos);
      let { simulateBreak, simulateDoubleBreak } = this.options;
      if (simulateBreak != null && simulateBreak >= line.from && simulateBreak <= line.to) {
        if (simulateDoubleBreak && simulateBreak == pos)
          return { text: "", from: pos };
        else if (bias < 0 ? simulateBreak < pos : simulateBreak <= pos)
          return { text: line.text.slice(simulateBreak - line.from), from: simulateBreak };
        else
          return { text: line.text.slice(0, simulateBreak - line.from), from: line.from };
      }
      return line;
    }
    textAfterPos(pos, bias = 1) {
      if (this.options.simulateDoubleBreak && pos == this.options.simulateBreak)
        return "";
      let { text, from: from2 } = this.lineAt(pos, bias);
      return text.slice(pos - from2, Math.min(text.length, pos + 100 - from2));
    }
    column(pos, bias = 1) {
      let { text, from: from2 } = this.lineAt(pos, bias);
      let result = this.countColumn(text, pos - from2);
      let override = this.options.overrideIndentation ? this.options.overrideIndentation(from2) : -1;
      if (override > -1)
        result += override - this.countColumn(text, text.search(/\S|$/));
      return result;
    }
    countColumn(line, pos = line.length) {
      return countColumn(line, this.state.tabSize, pos);
    }
    lineIndent(pos, bias = 1) {
      let { text, from: from2 } = this.lineAt(pos, bias);
      let override = this.options.overrideIndentation;
      if (override) {
        let overriden = override(from2);
        if (overriden > -1)
          return overriden;
      }
      return this.countColumn(text, text.search(/\S|$/));
    }
    get simulatedBreak() {
      return this.options.simulateBreak || null;
    }
  };
  var indentNodeProp = /* @__PURE__ */ new NodeProp();
  function syntaxIndentation(cx, ast, pos) {
    return indentFrom(ast.resolveInner(pos).enterUnfinishedNodesBefore(pos), pos, cx);
  }
  function ignoreClosed(cx) {
    return cx.pos == cx.options.simulateBreak && cx.options.simulateDoubleBreak;
  }
  function indentStrategy(tree) {
    let strategy = tree.type.prop(indentNodeProp);
    if (strategy)
      return strategy;
    let first = tree.firstChild, close;
    if (first && (close = first.type.prop(NodeProp.closedBy))) {
      let last = tree.lastChild, closed = last && close.indexOf(last.name) > -1;
      return (cx) => delimitedStrategy(cx, true, 1, void 0, closed && !ignoreClosed(cx) ? last.from : void 0);
    }
    return tree.parent == null ? topIndent : null;
  }
  function indentFrom(node, pos, base2) {
    for (; node; node = node.parent) {
      let strategy = indentStrategy(node);
      if (strategy)
        return strategy(new TreeIndentContext(base2, pos, node));
    }
    return null;
  }
  function topIndent() {
    return 0;
  }
  var TreeIndentContext = class extends IndentContext {
    constructor(base2, pos, node) {
      super(base2.state, base2.options);
      this.base = base2;
      this.pos = pos;
      this.node = node;
    }
    get textAfter() {
      return this.textAfterPos(this.pos);
    }
    get baseIndent() {
      let line = this.state.doc.lineAt(this.node.from);
      for (; ; ) {
        let atBreak = this.node.resolve(line.from);
        while (atBreak.parent && atBreak.parent.from == atBreak.from)
          atBreak = atBreak.parent;
        if (isParent(atBreak, this.node))
          break;
        line = this.state.doc.lineAt(atBreak.from);
      }
      return this.lineIndent(line.from);
    }
    continue() {
      let parent = this.node.parent;
      return parent ? indentFrom(parent, this.pos, this.base) : 0;
    }
  };
  function isParent(parent, of) {
    for (let cur2 = of; cur2; cur2 = cur2.parent)
      if (parent == cur2)
        return true;
    return false;
  }
  function bracketedAligned(context) {
    let tree = context.node;
    let openToken = tree.childAfter(tree.from), last = tree.lastChild;
    if (!openToken)
      return null;
    let sim = context.options.simulateBreak;
    let openLine = context.state.doc.lineAt(openToken.from);
    let lineEnd = sim == null || sim <= openLine.from ? openLine.to : Math.min(openLine.to, sim);
    for (let pos = openToken.to; ; ) {
      let next = tree.childAfter(pos);
      if (!next || next == last)
        return null;
      if (!next.type.isSkipped)
        return next.from < lineEnd ? openToken : null;
      pos = next.to;
    }
  }
  function delimitedIndent({ closing: closing2, align = true, units = 1 }) {
    return (context) => delimitedStrategy(context, align, units, closing2);
  }
  function delimitedStrategy(context, align, units, closing2, closedAt) {
    let after = context.textAfter, space3 = after.match(/^\s*/)[0].length;
    let closed = closing2 && after.slice(space3, space3 + closing2.length) == closing2 || closedAt == context.pos + space3;
    let aligned = align ? bracketedAligned(context) : null;
    if (aligned)
      return closed ? context.column(aligned.from) : context.column(aligned.to);
    return context.baseIndent + (closed ? 0 : context.unit * units);
  }
  var flatIndent = (context) => context.baseIndent;
  function continuedIndent({ except, units = 1 } = {}) {
    return (context) => {
      let matchExcept = except && except.test(context.textAfter);
      return context.baseIndent + (matchExcept ? 0 : units * context.unit);
    };
  }
  var DontIndentBeyond = 200;
  function indentOnInput() {
    return EditorState.transactionFilter.of((tr) => {
      if (!tr.docChanged || !tr.isUserEvent("input.type") && !tr.isUserEvent("input.complete"))
        return tr;
      let rules = tr.startState.languageDataAt("indentOnInput", tr.startState.selection.main.head);
      if (!rules.length)
        return tr;
      let doc2 = tr.newDoc, { head } = tr.newSelection.main, line = doc2.lineAt(head);
      if (head > line.from + DontIndentBeyond)
        return tr;
      let lineStart = doc2.sliceString(line.from, head);
      if (!rules.some((r) => r.test(lineStart)))
        return tr;
      let { state } = tr, last = -1, changes = [];
      for (let { head: head2 } of state.selection.ranges) {
        let line2 = state.doc.lineAt(head2);
        if (line2.from == last)
          continue;
        last = line2.from;
        let indent = getIndentation(state, line2.from);
        if (indent == null)
          continue;
        let cur2 = /^\s*/.exec(line2.text)[0];
        let norm = indentString(state, indent);
        if (cur2 != norm)
          changes.push({ from: line2.from, to: line2.from + cur2.length, insert: norm });
      }
      return changes.length ? [tr, { changes, sequential: true }] : tr;
    });
  }
  var foldService = /* @__PURE__ */ Facet.define();
  var foldNodeProp = /* @__PURE__ */ new NodeProp();
  function foldInside(node) {
    let first = node.firstChild, last = node.lastChild;
    return first && first.to < last.from ? { from: first.to, to: last.type.isError ? node.to : last.from } : null;
  }
  function syntaxFolding(state, start, end) {
    let tree = syntaxTree(state);
    if (tree.length == 0)
      return null;
    let inner = tree.resolveInner(end);
    let found = null;
    for (let cur2 = inner; cur2; cur2 = cur2.parent) {
      if (cur2.to <= end || cur2.from > end)
        continue;
      if (found && cur2.from < start)
        break;
      let prop = cur2.type.prop(foldNodeProp);
      if (prop && (cur2.to < tree.length - 50 || tree.length == state.doc.length || !isUnfinished(cur2))) {
        let value = prop(cur2, state);
        if (value && value.from <= end && value.from >= start && value.to > end)
          found = value;
      }
    }
    return found;
  }
  function isUnfinished(node) {
    let ch = node.lastChild;
    return ch && ch.to == node.to && ch.type.isError;
  }
  function foldable(state, lineStart, lineEnd) {
    for (let service of state.facet(foldService)) {
      let result = service(state, lineStart, lineEnd);
      if (result)
        return result;
    }
    return syntaxFolding(state, lineStart, lineEnd);
  }

  // node_modules/@codemirror/gutter/dist/index.js
  var GutterMarker = class extends RangeValue {
    compare(other) {
      return this == other || this.constructor == other.constructor && this.eq(other);
    }
    eq(other) {
      return false;
    }
    destroy(dom) {
    }
  };
  GutterMarker.prototype.elementClass = "";
  GutterMarker.prototype.toDOM = void 0;
  GutterMarker.prototype.mapMode = MapMode.TrackBefore;
  GutterMarker.prototype.startSide = GutterMarker.prototype.endSide = -1;
  GutterMarker.prototype.point = true;
  var gutterLineClass = /* @__PURE__ */ Facet.define();
  var defaults = {
    class: "",
    renderEmptyElements: false,
    elementStyle: "",
    markers: () => RangeSet.empty,
    lineMarker: () => null,
    lineMarkerChange: null,
    initialSpacer: null,
    updateSpacer: null,
    domEventHandlers: {}
  };
  var activeGutters = /* @__PURE__ */ Facet.define();
  function gutter(config2) {
    return [gutters(), activeGutters.of(Object.assign(Object.assign({}, defaults), config2))];
  }
  var baseTheme2 = /* @__PURE__ */ EditorView.baseTheme({
    ".cm-gutters": {
      display: "flex",
      height: "100%",
      boxSizing: "border-box",
      left: 0,
      zIndex: 200
    },
    "&light .cm-gutters": {
      backgroundColor: "#f5f5f5",
      color: "#999",
      borderRight: "1px solid #ddd"
    },
    "&dark .cm-gutters": {
      backgroundColor: "#333338",
      color: "#ccc"
    },
    ".cm-gutter": {
      display: "flex !important",
      flexDirection: "column",
      flexShrink: 0,
      boxSizing: "border-box",
      minHeight: "100%",
      overflow: "hidden"
    },
    ".cm-gutterElement": {
      boxSizing: "border-box"
    },
    ".cm-lineNumbers .cm-gutterElement": {
      padding: "0 3px 0 5px",
      minWidth: "20px",
      textAlign: "right",
      whiteSpace: "nowrap"
    },
    "&light .cm-activeLineGutter": {
      backgroundColor: "#e2f2ff"
    },
    "&dark .cm-activeLineGutter": {
      backgroundColor: "#222227"
    }
  });
  var unfixGutters = /* @__PURE__ */ Facet.define({
    combine: (values2) => values2.some((x) => x)
  });
  function gutters(config2) {
    let result = [
      gutterView,
      baseTheme2
    ];
    if (config2 && config2.fixed === false)
      result.push(unfixGutters.of(true));
    return result;
  }
  var gutterView = /* @__PURE__ */ ViewPlugin.fromClass(class {
    constructor(view) {
      this.view = view;
      this.prevViewport = view.viewport;
      this.dom = document.createElement("div");
      this.dom.className = "cm-gutters";
      this.dom.setAttribute("aria-hidden", "true");
      this.dom.style.minHeight = this.view.contentHeight + "px";
      this.gutters = view.state.facet(activeGutters).map((conf) => new SingleGutterView(view, conf));
      for (let gutter2 of this.gutters)
        this.dom.appendChild(gutter2.dom);
      this.fixed = !view.state.facet(unfixGutters);
      if (this.fixed) {
        this.dom.style.position = "sticky";
      }
      this.syncGutters(false);
      view.scrollDOM.insertBefore(this.dom, view.contentDOM);
    }
    update(update) {
      if (this.updateGutters(update)) {
        let vpA = this.prevViewport, vpB = update.view.viewport;
        let vpOverlap = Math.min(vpA.to, vpB.to) - Math.max(vpA.from, vpB.from);
        this.syncGutters(vpOverlap < (vpB.to - vpB.from) * 0.8);
      }
      if (update.geometryChanged)
        this.dom.style.minHeight = this.view.contentHeight + "px";
      if (this.view.state.facet(unfixGutters) != !this.fixed) {
        this.fixed = !this.fixed;
        this.dom.style.position = this.fixed ? "sticky" : "";
      }
      this.prevViewport = update.view.viewport;
    }
    syncGutters(detach) {
      let after = this.dom.nextSibling;
      if (detach)
        this.dom.remove();
      let lineClasses = RangeSet.iter(this.view.state.facet(gutterLineClass), this.view.viewport.from);
      let classSet = [];
      let contexts = this.gutters.map((gutter2) => new UpdateContext(gutter2, this.view.viewport, -this.view.documentPadding.top));
      for (let line of this.view.viewportLineBlocks) {
        let text;
        if (Array.isArray(line.type)) {
          for (let b of line.type)
            if (b.type == BlockType.Text) {
              text = b;
              break;
            }
        } else {
          text = line.type == BlockType.Text ? line : void 0;
        }
        if (!text)
          continue;
        if (classSet.length)
          classSet = [];
        advanceCursor(lineClasses, classSet, line.from);
        for (let cx of contexts)
          cx.line(this.view, text, classSet);
      }
      for (let cx of contexts)
        cx.finish();
      if (detach)
        this.view.scrollDOM.insertBefore(this.dom, after);
    }
    updateGutters(update) {
      let prev = update.startState.facet(activeGutters), cur2 = update.state.facet(activeGutters);
      let change = update.docChanged || update.heightChanged || update.viewportChanged || !RangeSet.eq(update.startState.facet(gutterLineClass), update.state.facet(gutterLineClass), update.view.viewport.from, update.view.viewport.to);
      if (prev == cur2) {
        for (let gutter2 of this.gutters)
          if (gutter2.update(update))
            change = true;
      } else {
        change = true;
        let gutters2 = [];
        for (let conf of cur2) {
          let known = prev.indexOf(conf);
          if (known < 0) {
            gutters2.push(new SingleGutterView(this.view, conf));
          } else {
            this.gutters[known].update(update);
            gutters2.push(this.gutters[known]);
          }
        }
        for (let g of this.gutters) {
          g.dom.remove();
          if (gutters2.indexOf(g) < 0)
            g.destroy();
        }
        for (let g of gutters2)
          this.dom.appendChild(g.dom);
        this.gutters = gutters2;
      }
      return change;
    }
    destroy() {
      for (let view of this.gutters)
        view.destroy();
      this.dom.remove();
    }
  }, {
    provide: /* @__PURE__ */ PluginField.scrollMargins.from((value) => {
      if (value.gutters.length == 0 || !value.fixed)
        return null;
      return value.view.textDirection == Direction.LTR ? { left: value.dom.offsetWidth } : { right: value.dom.offsetWidth };
    })
  });
  function asArray2(val) {
    return Array.isArray(val) ? val : [val];
  }
  function advanceCursor(cursor, collect, pos) {
    while (cursor.value && cursor.from <= pos) {
      if (cursor.from == pos)
        collect.push(cursor.value);
      cursor.next();
    }
  }
  var UpdateContext = class {
    constructor(gutter2, viewport, height) {
      this.gutter = gutter2;
      this.height = height;
      this.localMarkers = [];
      this.i = 0;
      this.cursor = RangeSet.iter(gutter2.markers, viewport.from);
    }
    line(view, line, extraMarkers) {
      if (this.localMarkers.length)
        this.localMarkers = [];
      advanceCursor(this.cursor, this.localMarkers, line.from);
      let localMarkers = extraMarkers.length ? this.localMarkers.concat(extraMarkers) : this.localMarkers;
      let forLine = this.gutter.config.lineMarker(view, line, localMarkers);
      if (forLine)
        localMarkers.unshift(forLine);
      let gutter2 = this.gutter;
      if (localMarkers.length == 0 && !gutter2.config.renderEmptyElements)
        return;
      let above = line.top - this.height;
      if (this.i == gutter2.elements.length) {
        let newElt = new GutterElement(view, line.height, above, localMarkers);
        gutter2.elements.push(newElt);
        gutter2.dom.appendChild(newElt.dom);
      } else {
        gutter2.elements[this.i].update(view, line.height, above, localMarkers);
      }
      this.height = line.bottom;
      this.i++;
    }
    finish() {
      let gutter2 = this.gutter;
      while (gutter2.elements.length > this.i) {
        let last = gutter2.elements.pop();
        gutter2.dom.removeChild(last.dom);
        last.destroy();
      }
    }
  };
  var SingleGutterView = class {
    constructor(view, config2) {
      this.view = view;
      this.config = config2;
      this.elements = [];
      this.spacer = null;
      this.dom = document.createElement("div");
      this.dom.className = "cm-gutter" + (this.config.class ? " " + this.config.class : "");
      for (let prop in config2.domEventHandlers) {
        this.dom.addEventListener(prop, (event) => {
          let line = view.lineBlockAtHeight(event.clientY - view.documentTop);
          if (config2.domEventHandlers[prop](view, line, event))
            event.preventDefault();
        });
      }
      this.markers = asArray2(config2.markers(view));
      if (config2.initialSpacer) {
        this.spacer = new GutterElement(view, 0, 0, [config2.initialSpacer(view)]);
        this.dom.appendChild(this.spacer.dom);
        this.spacer.dom.style.cssText += "visibility: hidden; pointer-events: none";
      }
    }
    update(update) {
      let prevMarkers = this.markers;
      this.markers = asArray2(this.config.markers(update.view));
      if (this.spacer && this.config.updateSpacer) {
        let updated = this.config.updateSpacer(this.spacer.markers[0], update);
        if (updated != this.spacer.markers[0])
          this.spacer.update(update.view, 0, 0, [updated]);
      }
      let vp = update.view.viewport;
      return !RangeSet.eq(this.markers, prevMarkers, vp.from, vp.to) || (this.config.lineMarkerChange ? this.config.lineMarkerChange(update) : false);
    }
    destroy() {
      for (let elt of this.elements)
        elt.destroy();
    }
  };
  var GutterElement = class {
    constructor(view, height, above, markers) {
      this.height = -1;
      this.above = 0;
      this.markers = [];
      this.dom = document.createElement("div");
      this.update(view, height, above, markers);
    }
    update(view, height, above, markers) {
      if (this.height != height)
        this.dom.style.height = (this.height = height) + "px";
      if (this.above != above)
        this.dom.style.marginTop = (this.above = above) ? above + "px" : "";
      if (!sameMarkers(this.markers, markers))
        this.setMarkers(view, markers);
    }
    setMarkers(view, markers) {
      let cls = "cm-gutterElement", domPos = this.dom.firstChild;
      for (let iNew = 0, iOld = 0; ; ) {
        let skipTo = iOld, marker = iNew < markers.length ? markers[iNew++] : null, matched = false;
        if (marker) {
          let c = marker.elementClass;
          if (c)
            cls += " " + c;
          for (let i = iOld; i < this.markers.length; i++)
            if (this.markers[i].compare(marker)) {
              skipTo = i;
              matched = true;
              break;
            }
        } else {
          skipTo = this.markers.length;
        }
        while (iOld < skipTo) {
          let next = this.markers[iOld++];
          if (next.toDOM) {
            next.destroy(domPos);
            let after = domPos.nextSibling;
            domPos.remove();
            domPos = after;
          }
        }
        if (!marker)
          break;
        if (marker.toDOM) {
          if (matched)
            domPos = domPos.nextSibling;
          else
            this.dom.insertBefore(marker.toDOM(view), domPos);
        }
        if (matched)
          iOld++;
      }
      this.dom.className = cls;
      this.markers = markers;
    }
    destroy() {
      this.setMarkers(null, []);
    }
  };
  function sameMarkers(a, b) {
    if (a.length != b.length)
      return false;
    for (let i = 0; i < a.length; i++)
      if (!a[i].compare(b[i]))
        return false;
    return true;
  }
  var lineNumberMarkers = /* @__PURE__ */ Facet.define();
  var lineNumberConfig = /* @__PURE__ */ Facet.define({
    combine(values2) {
      return combineConfig(values2, { formatNumber: String, domEventHandlers: {} }, {
        domEventHandlers(a, b) {
          let result = Object.assign({}, a);
          for (let event in b) {
            let exists = result[event], add2 = b[event];
            result[event] = exists ? (view, line, event2) => exists(view, line, event2) || add2(view, line, event2) : add2;
          }
          return result;
        }
      });
    }
  });
  var NumberMarker = class extends GutterMarker {
    constructor(number2) {
      super();
      this.number = number2;
    }
    eq(other) {
      return this.number == other.number;
    }
    toDOM() {
      return document.createTextNode(this.number);
    }
  };
  function formatNumber(view, number2) {
    return view.state.facet(lineNumberConfig).formatNumber(number2, view.state);
  }
  var lineNumberGutter = /* @__PURE__ */ activeGutters.compute([lineNumberConfig], (state) => ({
    class: "cm-lineNumbers",
    renderEmptyElements: false,
    markers(view) {
      return view.state.facet(lineNumberMarkers);
    },
    lineMarker(view, line, others) {
      if (others.some((m) => m.toDOM))
        return null;
      return new NumberMarker(formatNumber(view, view.state.doc.lineAt(line.from).number));
    },
    lineMarkerChange: (update) => update.startState.facet(lineNumberConfig) != update.state.facet(lineNumberConfig),
    initialSpacer(view) {
      return new NumberMarker(formatNumber(view, maxLineNumber(view.state.doc.lines)));
    },
    updateSpacer(spacer, update) {
      let max = formatNumber(update.view, maxLineNumber(update.view.state.doc.lines));
      return max == spacer.number ? spacer : new NumberMarker(max);
    },
    domEventHandlers: state.facet(lineNumberConfig).domEventHandlers
  }));
  function lineNumbers(config2 = {}) {
    return [
      lineNumberConfig.of(config2),
      gutters(),
      lineNumberGutter
    ];
  }
  function maxLineNumber(lines) {
    let last = 9;
    while (last < lines)
      last = last * 10 + 9;
    return last;
  }
  var activeLineGutterMarker = /* @__PURE__ */ new class extends GutterMarker {
    constructor() {
      super(...arguments);
      this.elementClass = "cm-activeLineGutter";
    }
  }();
  var activeLineGutterHighlighter = /* @__PURE__ */ gutterLineClass.compute(["selection"], (state) => {
    let marks = [], last = -1;
    for (let range of state.selection.ranges)
      if (range.empty) {
        let linePos = state.doc.lineAt(range.head).from;
        if (linePos > last) {
          last = linePos;
          marks.push(activeLineGutterMarker.range(linePos));
        }
      }
    return RangeSet.of(marks);
  });
  function highlightActiveLineGutter() {
    return activeLineGutterHighlighter;
  }

  // node_modules/@codemirror/fold/dist/index.js
  function mapRange(range, mapping) {
    let from2 = mapping.mapPos(range.from, 1), to = mapping.mapPos(range.to, -1);
    return from2 >= to ? void 0 : { from: from2, to };
  }
  var foldEffect = /* @__PURE__ */ StateEffect.define({ map: mapRange });
  var unfoldEffect = /* @__PURE__ */ StateEffect.define({ map: mapRange });
  function selectedLines(view) {
    let lines = [];
    for (let { head } of view.state.selection.ranges) {
      if (lines.some((l) => l.from <= head && l.to >= head))
        continue;
      lines.push(view.lineBlockAt(head));
    }
    return lines;
  }
  var foldState = /* @__PURE__ */ StateField.define({
    create() {
      return Decoration.none;
    },
    update(folded, tr) {
      folded = folded.map(tr.changes);
      for (let e of tr.effects) {
        if (e.is(foldEffect) && !foldExists(folded, e.value.from, e.value.to))
          folded = folded.update({ add: [foldWidget.range(e.value.from, e.value.to)] });
        else if (e.is(unfoldEffect))
          folded = folded.update({
            filter: (from2, to) => e.value.from != from2 || e.value.to != to,
            filterFrom: e.value.from,
            filterTo: e.value.to
          });
      }
      if (tr.selection) {
        let onSelection = false, { head } = tr.selection.main;
        folded.between(head, head, (a, b) => {
          if (a < head && b > head)
            onSelection = true;
        });
        if (onSelection)
          folded = folded.update({
            filterFrom: head,
            filterTo: head,
            filter: (a, b) => b <= head || a >= head
          });
      }
      return folded;
    },
    provide: (f) => EditorView.decorations.from(f)
  });
  function foldInside2(state, from2, to) {
    var _a2;
    let found = null;
    (_a2 = state.field(foldState, false)) === null || _a2 === void 0 ? void 0 : _a2.between(from2, to, (from3, to2) => {
      if (!found || found.from > from3)
        found = { from: from3, to: to2 };
    });
    return found;
  }
  function foldExists(folded, from2, to) {
    let found = false;
    folded.between(from2, from2, (a, b) => {
      if (a == from2 && b == to)
        found = true;
    });
    return found;
  }
  function maybeEnable(state, other) {
    return state.field(foldState, false) ? other : other.concat(StateEffect.appendConfig.of(codeFolding()));
  }
  var foldCode = (view) => {
    for (let line of selectedLines(view)) {
      let range = foldable(view.state, line.from, line.to);
      if (range) {
        view.dispatch({ effects: maybeEnable(view.state, [foldEffect.of(range), announceFold(view, range)]) });
        return true;
      }
    }
    return false;
  };
  var unfoldCode = (view) => {
    if (!view.state.field(foldState, false))
      return false;
    let effects = [];
    for (let line of selectedLines(view)) {
      let folded = foldInside2(view.state, line.from, line.to);
      if (folded)
        effects.push(unfoldEffect.of(folded), announceFold(view, folded, false));
    }
    if (effects.length)
      view.dispatch({ effects });
    return effects.length > 0;
  };
  function announceFold(view, range, fold = true) {
    let lineFrom = view.state.doc.lineAt(range.from).number, lineTo = view.state.doc.lineAt(range.to).number;
    return EditorView.announce.of(`${view.state.phrase(fold ? "Folded lines" : "Unfolded lines")} ${lineFrom} ${view.state.phrase("to")} ${lineTo}.`);
  }
  var foldAll = (view) => {
    let { state } = view, effects = [];
    for (let pos = 0; pos < state.doc.length; ) {
      let line = view.lineBlockAt(pos), range = foldable(state, line.from, line.to);
      if (range)
        effects.push(foldEffect.of(range));
      pos = (range ? view.lineBlockAt(range.to) : line).to + 1;
    }
    if (effects.length)
      view.dispatch({ effects: maybeEnable(view.state, effects) });
    return !!effects.length;
  };
  var unfoldAll = (view) => {
    let field = view.state.field(foldState, false);
    if (!field || !field.size)
      return false;
    let effects = [];
    field.between(0, view.state.doc.length, (from2, to) => {
      effects.push(unfoldEffect.of({ from: from2, to }));
    });
    view.dispatch({ effects });
    return true;
  };
  var foldKeymap = [
    { key: "Ctrl-Shift-[", mac: "Cmd-Alt-[", run: foldCode },
    { key: "Ctrl-Shift-]", mac: "Cmd-Alt-]", run: unfoldCode },
    { key: "Ctrl-Alt-[", run: foldAll },
    { key: "Ctrl-Alt-]", run: unfoldAll }
  ];
  var defaultConfig = {
    placeholderDOM: null,
    placeholderText: "\u2026"
  };
  var foldConfig = /* @__PURE__ */ Facet.define({
    combine(values2) {
      return combineConfig(values2, defaultConfig);
    }
  });
  function codeFolding(config2) {
    let result = [foldState, baseTheme3];
    if (config2)
      result.push(foldConfig.of(config2));
    return result;
  }
  var foldWidget = /* @__PURE__ */ Decoration.replace({ widget: /* @__PURE__ */ new class extends WidgetType {
    toDOM(view) {
      let { state } = view, conf = state.facet(foldConfig);
      let onclick = (event) => {
        let line = view.lineBlockAt(view.posAtDOM(event.target));
        let folded = foldInside2(view.state, line.from, line.to);
        if (folded)
          view.dispatch({ effects: unfoldEffect.of(folded) });
        event.preventDefault();
      };
      if (conf.placeholderDOM)
        return conf.placeholderDOM(view, onclick);
      let element = document.createElement("span");
      element.textContent = conf.placeholderText;
      element.setAttribute("aria-label", state.phrase("folded code"));
      element.title = state.phrase("unfold");
      element.className = "cm-foldPlaceholder";
      element.onclick = onclick;
      return element;
    }
  }() });
  var foldGutterDefaults = {
    openText: "\u2304",
    closedText: "\u203A",
    markerDOM: null,
    domEventHandlers: {}
  };
  var FoldMarker = class extends GutterMarker {
    constructor(config2, open) {
      super();
      this.config = config2;
      this.open = open;
    }
    eq(other) {
      return this.config == other.config && this.open == other.open;
    }
    toDOM(view) {
      if (this.config.markerDOM)
        return this.config.markerDOM(this.open);
      let span2 = document.createElement("span");
      span2.textContent = this.open ? this.config.openText : this.config.closedText;
      span2.title = view.state.phrase(this.open ? "Fold line" : "Unfold line");
      return span2;
    }
  };
  function foldGutter(config2 = {}) {
    let fullConfig = Object.assign(Object.assign({}, foldGutterDefaults), config2);
    let canFold = new FoldMarker(fullConfig, true), canUnfold = new FoldMarker(fullConfig, false);
    let markers = ViewPlugin.fromClass(class {
      constructor(view) {
        this.from = view.viewport.from;
        this.markers = this.buildMarkers(view);
      }
      update(update) {
        if (update.docChanged || update.viewportChanged || update.startState.facet(language) != update.state.facet(language) || update.startState.field(foldState, false) != update.state.field(foldState, false))
          this.markers = this.buildMarkers(update.view);
      }
      buildMarkers(view) {
        let builder = new RangeSetBuilder();
        for (let line of view.viewportLineBlocks) {
          let mark = foldInside2(view.state, line.from, line.to) ? canUnfold : foldable(view.state, line.from, line.to) ? canFold : null;
          if (mark)
            builder.add(line.from, line.from, mark);
        }
        return builder.finish();
      }
    });
    let { domEventHandlers: domEventHandlers2 } = fullConfig;
    return [
      markers,
      gutter({
        class: "cm-foldGutter",
        markers(view) {
          var _a2;
          return ((_a2 = view.plugin(markers)) === null || _a2 === void 0 ? void 0 : _a2.markers) || RangeSet.empty;
        },
        initialSpacer() {
          return new FoldMarker(fullConfig, false);
        },
        domEventHandlers: Object.assign(Object.assign({}, domEventHandlers2), { click: (view, line, event) => {
          if (domEventHandlers2.click && domEventHandlers2.click(view, line, event))
            return true;
          let folded = foldInside2(view.state, line.from, line.to);
          if (folded) {
            view.dispatch({ effects: unfoldEffect.of(folded) });
            return true;
          }
          let range = foldable(view.state, line.from, line.to);
          if (range) {
            view.dispatch({ effects: foldEffect.of(range) });
            return true;
          }
          return false;
        } })
      }),
      codeFolding()
    ];
  }
  var baseTheme3 = /* @__PURE__ */ EditorView.baseTheme({
    ".cm-foldPlaceholder": {
      backgroundColor: "#eee",
      border: "1px solid #ddd",
      color: "#888",
      borderRadius: ".2em",
      margin: "0 1px",
      padding: "0 1px",
      cursor: "pointer"
    },
    ".cm-foldGutter span": {
      padding: "0 1px",
      cursor: "pointer"
    }
  });

  // node_modules/@codemirror/matchbrackets/dist/index.js
  var baseTheme4 = /* @__PURE__ */ EditorView.baseTheme({
    "&.cm-focused .cm-matchingBracket": { backgroundColor: "#328c8252" },
    "&.cm-focused .cm-nonmatchingBracket": { backgroundColor: "#bb555544" }
  });
  var DefaultScanDist = 1e4;
  var DefaultBrackets = "()[]{}";
  var bracketMatchingConfig = /* @__PURE__ */ Facet.define({
    combine(configs) {
      return combineConfig(configs, {
        afterCursor: true,
        brackets: DefaultBrackets,
        maxScanDistance: DefaultScanDist
      });
    }
  });
  var matchingMark = /* @__PURE__ */ Decoration.mark({ class: "cm-matchingBracket" });
  var nonmatchingMark = /* @__PURE__ */ Decoration.mark({ class: "cm-nonmatchingBracket" });
  var bracketMatchingState = /* @__PURE__ */ StateField.define({
    create() {
      return Decoration.none;
    },
    update(deco, tr) {
      if (!tr.docChanged && !tr.selection)
        return deco;
      let decorations2 = [];
      let config2 = tr.state.facet(bracketMatchingConfig);
      for (let range of tr.state.selection.ranges) {
        if (!range.empty)
          continue;
        let match2 = matchBrackets(tr.state, range.head, -1, config2) || range.head > 0 && matchBrackets(tr.state, range.head - 1, 1, config2) || config2.afterCursor && (matchBrackets(tr.state, range.head, 1, config2) || range.head < tr.state.doc.length && matchBrackets(tr.state, range.head + 1, -1, config2));
        if (!match2)
          continue;
        let mark = match2.matched ? matchingMark : nonmatchingMark;
        decorations2.push(mark.range(match2.start.from, match2.start.to));
        if (match2.end)
          decorations2.push(mark.range(match2.end.from, match2.end.to));
      }
      return Decoration.set(decorations2, true);
    },
    provide: (f) => EditorView.decorations.from(f)
  });
  var bracketMatchingUnique = [
    bracketMatchingState,
    baseTheme4
  ];
  function bracketMatching(config2 = {}) {
    return [bracketMatchingConfig.of(config2), bracketMatchingUnique];
  }
  function matchingNodes(node, dir, brackets) {
    let byProp = node.prop(dir < 0 ? NodeProp.openedBy : NodeProp.closedBy);
    if (byProp)
      return byProp;
    if (node.name.length == 1) {
      let index = brackets.indexOf(node.name);
      if (index > -1 && index % 2 == (dir < 0 ? 1 : 0))
        return [brackets[index + dir]];
    }
    return null;
  }
  function matchBrackets(state, pos, dir, config2 = {}) {
    let maxScanDistance = config2.maxScanDistance || DefaultScanDist, brackets = config2.brackets || DefaultBrackets;
    let tree = syntaxTree(state), node = tree.resolveInner(pos, dir);
    for (let cur2 = node; cur2; cur2 = cur2.parent) {
      let matches = matchingNodes(cur2.type, dir, brackets);
      if (matches && cur2.from < cur2.to)
        return matchMarkedBrackets(state, pos, dir, cur2, matches, brackets);
    }
    return matchPlainBrackets(state, pos, dir, tree, node.type, maxScanDistance, brackets);
  }
  function matchMarkedBrackets(_state, _pos, dir, token, matching, brackets) {
    let parent = token.parent, firstToken = { from: token.from, to: token.to };
    let depth = 0, cursor = parent === null || parent === void 0 ? void 0 : parent.cursor;
    if (cursor && (dir < 0 ? cursor.childBefore(token.from) : cursor.childAfter(token.to)))
      do {
        if (dir < 0 ? cursor.to <= token.from : cursor.from >= token.to) {
          if (depth == 0 && matching.indexOf(cursor.type.name) > -1 && cursor.from < cursor.to) {
            return { start: firstToken, end: { from: cursor.from, to: cursor.to }, matched: true };
          } else if (matchingNodes(cursor.type, dir, brackets)) {
            depth++;
          } else if (matchingNodes(cursor.type, -dir, brackets)) {
            depth--;
            if (depth == 0)
              return {
                start: firstToken,
                end: cursor.from == cursor.to ? void 0 : { from: cursor.from, to: cursor.to },
                matched: false
              };
          }
        }
      } while (dir < 0 ? cursor.prevSibling() : cursor.nextSibling());
    return { start: firstToken, matched: false };
  }
  function matchPlainBrackets(state, pos, dir, tree, tokenType, maxScanDistance, brackets) {
    let startCh = dir < 0 ? state.sliceDoc(pos - 1, pos) : state.sliceDoc(pos, pos + 1);
    let bracket2 = brackets.indexOf(startCh);
    if (bracket2 < 0 || bracket2 % 2 == 0 != dir > 0)
      return null;
    let startToken = { from: dir < 0 ? pos - 1 : pos, to: dir > 0 ? pos + 1 : pos };
    let iter = state.doc.iterRange(pos, dir > 0 ? state.doc.length : 0), depth = 0;
    for (let distance = 0; !iter.next().done && distance <= maxScanDistance; ) {
      let text = iter.value;
      if (dir < 0)
        distance += text.length;
      let basePos = pos + distance * dir;
      for (let pos2 = dir > 0 ? 0 : text.length - 1, end = dir > 0 ? text.length : -1; pos2 != end; pos2 += dir) {
        let found = brackets.indexOf(text[pos2]);
        if (found < 0 || tree.resolve(basePos + pos2, 1).type != tokenType)
          continue;
        if (found % 2 == 0 == dir > 0) {
          depth++;
        } else if (depth == 1) {
          return { start: startToken, end: { from: basePos + pos2, to: basePos + pos2 + 1 }, matched: found >> 1 == bracket2 >> 1 };
        } else {
          depth--;
        }
      }
      if (dir > 0)
        distance += text.length;
    }
    return iter.done ? { start: startToken, matched: false } : null;
  }

  // node_modules/@codemirror/commands/dist/index.js
  function updateSel(sel, by) {
    return EditorSelection.create(sel.ranges.map(by), sel.mainIndex);
  }
  function setSel(state, selection) {
    return state.update({ selection, scrollIntoView: true, userEvent: "select" });
  }
  function moveSel({ state, dispatch }, how) {
    let selection = updateSel(state.selection, how);
    if (selection.eq(state.selection))
      return false;
    dispatch(setSel(state, selection));
    return true;
  }
  function rangeEnd(range, forward) {
    return EditorSelection.cursor(forward ? range.to : range.from);
  }
  function cursorByChar(view, forward) {
    return moveSel(view, (range) => range.empty ? view.moveByChar(range, forward) : rangeEnd(range, forward));
  }
  var cursorCharLeft = (view) => cursorByChar(view, view.textDirection != Direction.LTR);
  var cursorCharRight = (view) => cursorByChar(view, view.textDirection == Direction.LTR);
  function cursorByGroup(view, forward) {
    return moveSel(view, (range) => range.empty ? view.moveByGroup(range, forward) : rangeEnd(range, forward));
  }
  var cursorGroupLeft = (view) => cursorByGroup(view, view.textDirection != Direction.LTR);
  var cursorGroupRight = (view) => cursorByGroup(view, view.textDirection == Direction.LTR);
  function interestingNode(state, node, bracketProp) {
    if (node.type.prop(bracketProp))
      return true;
    let len = node.to - node.from;
    return len && (len > 2 || /[^\s,.;:]/.test(state.sliceDoc(node.from, node.to))) || node.firstChild;
  }
  function moveBySyntax(state, start, forward) {
    let pos = syntaxTree(state).resolveInner(start.head);
    let bracketProp = forward ? NodeProp.closedBy : NodeProp.openedBy;
    for (let at = start.head; ; ) {
      let next = forward ? pos.childAfter(at) : pos.childBefore(at);
      if (!next)
        break;
      if (interestingNode(state, next, bracketProp))
        pos = next;
      else
        at = forward ? next.to : next.from;
    }
    let bracket2 = pos.type.prop(bracketProp), match2, newPos;
    if (bracket2 && (match2 = forward ? matchBrackets(state, pos.from, 1) : matchBrackets(state, pos.to, -1)) && match2.matched)
      newPos = forward ? match2.end.to : match2.end.from;
    else
      newPos = forward ? pos.to : pos.from;
    return EditorSelection.cursor(newPos, forward ? -1 : 1);
  }
  var cursorSyntaxLeft = (view) => moveSel(view, (range) => moveBySyntax(view.state, range, view.textDirection != Direction.LTR));
  var cursorSyntaxRight = (view) => moveSel(view, (range) => moveBySyntax(view.state, range, view.textDirection == Direction.LTR));
  function cursorByLine(view, forward) {
    return moveSel(view, (range) => {
      if (!range.empty)
        return rangeEnd(range, forward);
      let moved = view.moveVertically(range, forward);
      return moved.head != range.head ? moved : view.moveToLineBoundary(range, forward);
    });
  }
  var cursorLineUp = (view) => cursorByLine(view, false);
  var cursorLineDown = (view) => cursorByLine(view, true);
  function cursorByPage(view, forward) {
    let { state } = view, selection = updateSel(state.selection, (range) => {
      return range.empty ? view.moveVertically(range, forward, view.dom.clientHeight) : rangeEnd(range, forward);
    });
    if (selection.eq(state.selection))
      return false;
    let startPos = view.coordsAtPos(state.selection.main.head);
    let scrollRect = view.scrollDOM.getBoundingClientRect();
    view.dispatch(setSel(state, selection), {
      effects: startPos && startPos.top > scrollRect.top && startPos.bottom < scrollRect.bottom ? EditorView.scrollIntoView(selection.main.head, { y: "start", yMargin: startPos.top - scrollRect.top }) : void 0
    });
    return true;
  }
  var cursorPageUp = (view) => cursorByPage(view, false);
  var cursorPageDown = (view) => cursorByPage(view, true);
  function moveByLineBoundary(view, start, forward) {
    let line = view.lineBlockAt(start.head), moved = view.moveToLineBoundary(start, forward);
    if (moved.head == start.head && moved.head != (forward ? line.to : line.from))
      moved = view.moveToLineBoundary(start, forward, false);
    if (!forward && moved.head == line.from && line.length) {
      let space3 = /^\s*/.exec(view.state.sliceDoc(line.from, Math.min(line.from + 100, line.to)))[0].length;
      if (space3 && start.head != line.from + space3)
        moved = EditorSelection.cursor(line.from + space3);
    }
    return moved;
  }
  var cursorLineBoundaryForward = (view) => moveSel(view, (range) => moveByLineBoundary(view, range, true));
  var cursorLineBoundaryBackward = (view) => moveSel(view, (range) => moveByLineBoundary(view, range, false));
  var cursorLineStart = (view) => moveSel(view, (range) => EditorSelection.cursor(view.lineBlockAt(range.head).from, 1));
  var cursorLineEnd = (view) => moveSel(view, (range) => EditorSelection.cursor(view.lineBlockAt(range.head).to, -1));
  function toMatchingBracket(state, dispatch, extend2) {
    let found = false, selection = updateSel(state.selection, (range) => {
      let matching = matchBrackets(state, range.head, -1) || matchBrackets(state, range.head, 1) || range.head > 0 && matchBrackets(state, range.head - 1, 1) || range.head < state.doc.length && matchBrackets(state, range.head + 1, -1);
      if (!matching || !matching.end)
        return range;
      found = true;
      let head = matching.start.from == range.head ? matching.end.to : matching.end.from;
      return extend2 ? EditorSelection.range(range.anchor, head) : EditorSelection.cursor(head);
    });
    if (!found)
      return false;
    dispatch(setSel(state, selection));
    return true;
  }
  var cursorMatchingBracket = ({ state, dispatch }) => toMatchingBracket(state, dispatch, false);
  function extendSel(view, how) {
    let selection = updateSel(view.state.selection, (range) => {
      let head = how(range);
      return EditorSelection.range(range.anchor, head.head, head.goalColumn);
    });
    if (selection.eq(view.state.selection))
      return false;
    view.dispatch(setSel(view.state, selection));
    return true;
  }
  function selectByChar(view, forward) {
    return extendSel(view, (range) => view.moveByChar(range, forward));
  }
  var selectCharLeft = (view) => selectByChar(view, view.textDirection != Direction.LTR);
  var selectCharRight = (view) => selectByChar(view, view.textDirection == Direction.LTR);
  function selectByGroup(view, forward) {
    return extendSel(view, (range) => view.moveByGroup(range, forward));
  }
  var selectGroupLeft = (view) => selectByGroup(view, view.textDirection != Direction.LTR);
  var selectGroupRight = (view) => selectByGroup(view, view.textDirection == Direction.LTR);
  var selectSyntaxLeft = (view) => extendSel(view, (range) => moveBySyntax(view.state, range, view.textDirection != Direction.LTR));
  var selectSyntaxRight = (view) => extendSel(view, (range) => moveBySyntax(view.state, range, view.textDirection == Direction.LTR));
  function selectByLine(view, forward) {
    return extendSel(view, (range) => view.moveVertically(range, forward));
  }
  var selectLineUp = (view) => selectByLine(view, false);
  var selectLineDown = (view) => selectByLine(view, true);
  function selectByPage(view, forward) {
    return extendSel(view, (range) => view.moveVertically(range, forward, view.dom.clientHeight));
  }
  var selectPageUp = (view) => selectByPage(view, false);
  var selectPageDown = (view) => selectByPage(view, true);
  var selectLineBoundaryForward = (view) => extendSel(view, (range) => moveByLineBoundary(view, range, true));
  var selectLineBoundaryBackward = (view) => extendSel(view, (range) => moveByLineBoundary(view, range, false));
  var selectLineStart = (view) => extendSel(view, (range) => EditorSelection.cursor(view.lineBlockAt(range.head).from));
  var selectLineEnd = (view) => extendSel(view, (range) => EditorSelection.cursor(view.lineBlockAt(range.head).to));
  var cursorDocStart = ({ state, dispatch }) => {
    dispatch(setSel(state, { anchor: 0 }));
    return true;
  };
  var cursorDocEnd = ({ state, dispatch }) => {
    dispatch(setSel(state, { anchor: state.doc.length }));
    return true;
  };
  var selectDocStart = ({ state, dispatch }) => {
    dispatch(setSel(state, { anchor: state.selection.main.anchor, head: 0 }));
    return true;
  };
  var selectDocEnd = ({ state, dispatch }) => {
    dispatch(setSel(state, { anchor: state.selection.main.anchor, head: state.doc.length }));
    return true;
  };
  var selectAll = ({ state, dispatch }) => {
    dispatch(state.update({ selection: { anchor: 0, head: state.doc.length }, userEvent: "select" }));
    return true;
  };
  var selectLine = ({ state, dispatch }) => {
    let ranges = selectedLineBlocks(state).map(({ from: from2, to }) => EditorSelection.range(from2, Math.min(to + 1, state.doc.length)));
    dispatch(state.update({ selection: EditorSelection.create(ranges), userEvent: "select" }));
    return true;
  };
  var selectParentSyntax = ({ state, dispatch }) => {
    let selection = updateSel(state.selection, (range) => {
      var _a2;
      let context = syntaxTree(state).resolveInner(range.head, 1);
      while (!(context.from < range.from && context.to >= range.to || context.to > range.to && context.from <= range.from || !((_a2 = context.parent) === null || _a2 === void 0 ? void 0 : _a2.parent)))
        context = context.parent;
      return EditorSelection.range(context.to, context.from);
    });
    dispatch(setSel(state, selection));
    return true;
  };
  var simplifySelection = ({ state, dispatch }) => {
    let cur2 = state.selection, selection = null;
    if (cur2.ranges.length > 1)
      selection = EditorSelection.create([cur2.main]);
    else if (!cur2.main.empty)
      selection = EditorSelection.create([EditorSelection.cursor(cur2.main.head)]);
    if (!selection)
      return false;
    dispatch(setSel(state, selection));
    return true;
  };
  function deleteBy({ state, dispatch }, by) {
    if (state.readOnly)
      return false;
    let event = "delete.selection";
    let changes = state.changeByRange((range) => {
      let { from: from2, to } = range;
      if (from2 == to) {
        let towards = by(from2);
        if (towards < from2)
          event = "delete.backward";
        else if (towards > from2)
          event = "delete.forward";
        from2 = Math.min(from2, towards);
        to = Math.max(to, towards);
      }
      return from2 == to ? { range } : { changes: { from: from2, to }, range: EditorSelection.cursor(from2) };
    });
    if (changes.changes.empty)
      return false;
    dispatch(state.update(changes, { scrollIntoView: true, userEvent: event }));
    return true;
  }
  function skipAtomic(target, pos, forward) {
    if (target instanceof EditorView)
      for (let ranges of target.pluginField(PluginField.atomicRanges))
        ranges.between(pos, pos, (from2, to) => {
          if (from2 < pos && to > pos)
            pos = forward ? to : from2;
        });
    return pos;
  }
  var deleteByChar = (target, forward) => deleteBy(target, (pos) => {
    let { state } = target, line = state.doc.lineAt(pos), before, targetPos;
    if (!forward && pos > line.from && pos < line.from + 200 && !/[^ \t]/.test(before = line.text.slice(0, pos - line.from))) {
      if (before[before.length - 1] == "	")
        return pos - 1;
      let col = countColumn(before, state.tabSize), drop = col % getIndentUnit(state) || getIndentUnit(state);
      for (let i = 0; i < drop && before[before.length - 1 - i] == " "; i++)
        pos--;
      targetPos = pos;
    } else {
      targetPos = findClusterBreak(line.text, pos - line.from, forward, forward) + line.from;
      if (targetPos == pos && line.number != (forward ? state.doc.lines : 1))
        targetPos += forward ? 1 : -1;
    }
    return skipAtomic(target, targetPos, forward);
  });
  var deleteCharBackward = (view) => deleteByChar(view, false);
  var deleteCharForward = (view) => deleteByChar(view, true);
  var deleteByGroup = (target, forward) => deleteBy(target, (start) => {
    let pos = start, { state } = target, line = state.doc.lineAt(pos);
    let categorize = state.charCategorizer(pos);
    for (let cat = null; ; ) {
      if (pos == (forward ? line.to : line.from)) {
        if (pos == start && line.number != (forward ? state.doc.lines : 1))
          pos += forward ? 1 : -1;
        break;
      }
      let next = findClusterBreak(line.text, pos - line.from, forward) + line.from;
      let nextChar2 = line.text.slice(Math.min(pos, next) - line.from, Math.max(pos, next) - line.from);
      let nextCat = categorize(nextChar2);
      if (cat != null && nextCat != cat)
        break;
      if (nextChar2 != " " || pos != start)
        cat = nextCat;
      pos = next;
    }
    return skipAtomic(target, pos, forward);
  });
  var deleteGroupBackward = (target) => deleteByGroup(target, false);
  var deleteGroupForward = (target) => deleteByGroup(target, true);
  var deleteToLineEnd = (view) => deleteBy(view, (pos) => {
    let lineEnd = view.lineBlockAt(pos).to;
    return skipAtomic(view, pos < lineEnd ? lineEnd : Math.min(view.state.doc.length, pos + 1), true);
  });
  var deleteToLineStart = (view) => deleteBy(view, (pos) => {
    let lineStart = view.lineBlockAt(pos).from;
    return skipAtomic(view, pos > lineStart ? lineStart : Math.max(0, pos - 1), false);
  });
  var splitLine = ({ state, dispatch }) => {
    if (state.readOnly)
      return false;
    let changes = state.changeByRange((range) => {
      return {
        changes: { from: range.from, to: range.to, insert: Text.of(["", ""]) },
        range: EditorSelection.cursor(range.from)
      };
    });
    dispatch(state.update(changes, { scrollIntoView: true, userEvent: "input" }));
    return true;
  };
  var transposeChars = ({ state, dispatch }) => {
    if (state.readOnly)
      return false;
    let changes = state.changeByRange((range) => {
      if (!range.empty || range.from == 0 || range.from == state.doc.length)
        return { range };
      let pos = range.from, line = state.doc.lineAt(pos);
      let from2 = pos == line.from ? pos - 1 : findClusterBreak(line.text, pos - line.from, false) + line.from;
      let to = pos == line.to ? pos + 1 : findClusterBreak(line.text, pos - line.from, true) + line.from;
      return {
        changes: { from: from2, to, insert: state.doc.slice(pos, to).append(state.doc.slice(from2, pos)) },
        range: EditorSelection.cursor(to)
      };
    });
    if (changes.changes.empty)
      return false;
    dispatch(state.update(changes, { scrollIntoView: true, userEvent: "move.character" }));
    return true;
  };
  function selectedLineBlocks(state) {
    let blocks = [], upto = -1;
    for (let range of state.selection.ranges) {
      let startLine = state.doc.lineAt(range.from), endLine = state.doc.lineAt(range.to);
      if (!range.empty && range.to == endLine.from)
        endLine = state.doc.lineAt(range.to - 1);
      if (upto >= startLine.number) {
        let prev = blocks[blocks.length - 1];
        prev.to = endLine.to;
        prev.ranges.push(range);
      } else {
        blocks.push({ from: startLine.from, to: endLine.to, ranges: [range] });
      }
      upto = endLine.number + 1;
    }
    return blocks;
  }
  function moveLine(state, dispatch, forward) {
    if (state.readOnly)
      return false;
    let changes = [], ranges = [];
    for (let block of selectedLineBlocks(state)) {
      if (forward ? block.to == state.doc.length : block.from == 0)
        continue;
      let nextLine = state.doc.lineAt(forward ? block.to + 1 : block.from - 1);
      let size = nextLine.length + 1;
      if (forward) {
        changes.push({ from: block.to, to: nextLine.to }, { from: block.from, insert: nextLine.text + state.lineBreak });
        for (let r of block.ranges)
          ranges.push(EditorSelection.range(Math.min(state.doc.length, r.anchor + size), Math.min(state.doc.length, r.head + size)));
      } else {
        changes.push({ from: nextLine.from, to: block.from }, { from: block.to, insert: state.lineBreak + nextLine.text });
        for (let r of block.ranges)
          ranges.push(EditorSelection.range(r.anchor - size, r.head - size));
      }
    }
    if (!changes.length)
      return false;
    dispatch(state.update({
      changes,
      scrollIntoView: true,
      selection: EditorSelection.create(ranges, state.selection.mainIndex),
      userEvent: "move.line"
    }));
    return true;
  }
  var moveLineUp = ({ state, dispatch }) => moveLine(state, dispatch, false);
  var moveLineDown = ({ state, dispatch }) => moveLine(state, dispatch, true);
  function copyLine(state, dispatch, forward) {
    if (state.readOnly)
      return false;
    let changes = [];
    for (let block of selectedLineBlocks(state)) {
      if (forward)
        changes.push({ from: block.from, insert: state.doc.slice(block.from, block.to) + state.lineBreak });
      else
        changes.push({ from: block.to, insert: state.lineBreak + state.doc.slice(block.from, block.to) });
    }
    dispatch(state.update({ changes, scrollIntoView: true, userEvent: "input.copyline" }));
    return true;
  }
  var copyLineUp = ({ state, dispatch }) => copyLine(state, dispatch, false);
  var copyLineDown = ({ state, dispatch }) => copyLine(state, dispatch, true);
  var deleteLine = (view) => {
    if (view.state.readOnly)
      return false;
    let { state } = view, changes = state.changes(selectedLineBlocks(state).map(({ from: from2, to }) => {
      if (from2 > 0)
        from2--;
      else if (to < state.doc.length)
        to++;
      return { from: from2, to };
    }));
    let selection = updateSel(state.selection, (range) => view.moveVertically(range, true)).map(changes);
    view.dispatch({ changes, selection, scrollIntoView: true, userEvent: "delete.line" });
    return true;
  };
  function isBetweenBrackets(state, pos) {
    if (/\(\)|\[\]|\{\}/.test(state.sliceDoc(pos - 1, pos + 1)))
      return { from: pos, to: pos };
    let context = syntaxTree(state).resolveInner(pos);
    let before = context.childBefore(pos), after = context.childAfter(pos), closedBy;
    if (before && after && before.to <= pos && after.from >= pos && (closedBy = before.type.prop(NodeProp.closedBy)) && closedBy.indexOf(after.name) > -1 && state.doc.lineAt(before.to).from == state.doc.lineAt(after.from).from)
      return { from: before.to, to: after.from };
    return null;
  }
  var insertNewlineAndIndent = /* @__PURE__ */ newlineAndIndent(false);
  var insertBlankLine = /* @__PURE__ */ newlineAndIndent(true);
  function newlineAndIndent(atEof) {
    return ({ state, dispatch }) => {
      if (state.readOnly)
        return false;
      let changes = state.changeByRange((range) => {
        let { from: from2, to } = range, line = state.doc.lineAt(from2);
        let explode = !atEof && from2 == to && isBetweenBrackets(state, from2);
        if (atEof)
          from2 = to = (to <= line.to ? line : state.doc.lineAt(to)).to;
        let cx = new IndentContext(state, { simulateBreak: from2, simulateDoubleBreak: !!explode });
        let indent = getIndentation(cx, from2);
        if (indent == null)
          indent = /^\s*/.exec(state.doc.lineAt(from2).text)[0].length;
        while (to < line.to && /\s/.test(line.text[to - line.from]))
          to++;
        if (explode)
          ({ from: from2, to } = explode);
        else if (from2 > line.from && from2 < line.from + 100 && !/\S/.test(line.text.slice(0, from2)))
          from2 = line.from;
        let insert2 = ["", indentString(state, indent)];
        if (explode)
          insert2.push(indentString(state, cx.lineIndent(line.from, -1)));
        return {
          changes: { from: from2, to, insert: Text.of(insert2) },
          range: EditorSelection.cursor(from2 + 1 + insert2[1].length)
        };
      });
      dispatch(state.update(changes, { scrollIntoView: true, userEvent: "input" }));
      return true;
    };
  }
  function changeBySelectedLine(state, f) {
    let atLine = -1;
    return state.changeByRange((range) => {
      let changes = [];
      for (let pos = range.from; pos <= range.to; ) {
        let line = state.doc.lineAt(pos);
        if (line.number > atLine && (range.empty || range.to > line.from)) {
          f(line, changes, range);
          atLine = line.number;
        }
        pos = line.to + 1;
      }
      let changeSet = state.changes(changes);
      return {
        changes,
        range: EditorSelection.range(changeSet.mapPos(range.anchor, 1), changeSet.mapPos(range.head, 1))
      };
    });
  }
  var indentSelection = ({ state, dispatch }) => {
    if (state.readOnly)
      return false;
    let updated = /* @__PURE__ */ Object.create(null);
    let context = new IndentContext(state, { overrideIndentation: (start) => {
      let found = updated[start];
      return found == null ? -1 : found;
    } });
    let changes = changeBySelectedLine(state, (line, changes2, range) => {
      let indent = getIndentation(context, line.from);
      if (indent == null)
        return;
      if (!/\S/.test(line.text))
        indent = 0;
      let cur2 = /^\s*/.exec(line.text)[0];
      let norm = indentString(state, indent);
      if (cur2 != norm || range.from < line.from + cur2.length) {
        updated[line.from] = indent;
        changes2.push({ from: line.from, to: line.from + cur2.length, insert: norm });
      }
    });
    if (!changes.changes.empty)
      dispatch(state.update(changes, { userEvent: "indent" }));
    return true;
  };
  var indentMore = ({ state, dispatch }) => {
    if (state.readOnly)
      return false;
    dispatch(state.update(changeBySelectedLine(state, (line, changes) => {
      changes.push({ from: line.from, insert: state.facet(indentUnit) });
    }), { userEvent: "input.indent" }));
    return true;
  };
  var indentLess = ({ state, dispatch }) => {
    if (state.readOnly)
      return false;
    dispatch(state.update(changeBySelectedLine(state, (line, changes) => {
      let space3 = /^\s*/.exec(line.text)[0];
      if (!space3)
        return;
      let col = countColumn(space3, state.tabSize), keep = 0;
      let insert2 = indentString(state, Math.max(0, col - getIndentUnit(state)));
      while (keep < space3.length && keep < insert2.length && space3.charCodeAt(keep) == insert2.charCodeAt(keep))
        keep++;
      changes.push({ from: line.from + keep, to: line.from + space3.length, insert: insert2.slice(keep) });
    }), { userEvent: "delete.dedent" }));
    return true;
  };
  var emacsStyleKeymap = [
    { key: "Ctrl-b", run: cursorCharLeft, shift: selectCharLeft, preventDefault: true },
    { key: "Ctrl-f", run: cursorCharRight, shift: selectCharRight },
    { key: "Ctrl-p", run: cursorLineUp, shift: selectLineUp },
    { key: "Ctrl-n", run: cursorLineDown, shift: selectLineDown },
    { key: "Ctrl-a", run: cursorLineStart, shift: selectLineStart },
    { key: "Ctrl-e", run: cursorLineEnd, shift: selectLineEnd },
    { key: "Ctrl-d", run: deleteCharForward },
    { key: "Ctrl-h", run: deleteCharBackward },
    { key: "Ctrl-k", run: deleteToLineEnd },
    { key: "Ctrl-Alt-h", run: deleteGroupBackward },
    { key: "Ctrl-o", run: splitLine },
    { key: "Ctrl-t", run: transposeChars },
    { key: "Ctrl-v", run: cursorPageDown }
  ];
  var standardKeymap = /* @__PURE__ */ [
    { key: "ArrowLeft", run: cursorCharLeft, shift: selectCharLeft, preventDefault: true },
    { key: "Mod-ArrowLeft", mac: "Alt-ArrowLeft", run: cursorGroupLeft, shift: selectGroupLeft },
    { mac: "Cmd-ArrowLeft", run: cursorLineBoundaryBackward, shift: selectLineBoundaryBackward },
    { key: "ArrowRight", run: cursorCharRight, shift: selectCharRight, preventDefault: true },
    { key: "Mod-ArrowRight", mac: "Alt-ArrowRight", run: cursorGroupRight, shift: selectGroupRight },
    { mac: "Cmd-ArrowRight", run: cursorLineBoundaryForward, shift: selectLineBoundaryForward },
    { key: "ArrowUp", run: cursorLineUp, shift: selectLineUp, preventDefault: true },
    { mac: "Cmd-ArrowUp", run: cursorDocStart, shift: selectDocStart },
    { mac: "Ctrl-ArrowUp", run: cursorPageUp, shift: selectPageUp },
    { key: "ArrowDown", run: cursorLineDown, shift: selectLineDown, preventDefault: true },
    { mac: "Cmd-ArrowDown", run: cursorDocEnd, shift: selectDocEnd },
    { mac: "Ctrl-ArrowDown", run: cursorPageDown, shift: selectPageDown },
    { key: "PageUp", run: cursorPageUp, shift: selectPageUp },
    { key: "PageDown", run: cursorPageDown, shift: selectPageDown },
    { key: "Home", run: cursorLineBoundaryBackward, shift: selectLineBoundaryBackward },
    { key: "Mod-Home", run: cursorDocStart, shift: selectDocStart },
    { key: "End", run: cursorLineBoundaryForward, shift: selectLineBoundaryForward },
    { key: "Mod-End", run: cursorDocEnd, shift: selectDocEnd },
    { key: "Enter", run: insertNewlineAndIndent },
    { key: "Mod-a", run: selectAll },
    { key: "Backspace", run: deleteCharBackward, shift: deleteCharBackward },
    { key: "Delete", run: deleteCharForward },
    { key: "Mod-Backspace", mac: "Alt-Backspace", run: deleteGroupBackward },
    { key: "Mod-Delete", mac: "Alt-Delete", run: deleteGroupForward },
    { mac: "Mod-Backspace", run: deleteToLineStart },
    { mac: "Mod-Delete", run: deleteToLineEnd }
  ].concat(/* @__PURE__ */ emacsStyleKeymap.map((b) => ({ mac: b.key, run: b.run, shift: b.shift })));
  var defaultKeymap = /* @__PURE__ */ [
    { key: "Alt-ArrowLeft", mac: "Ctrl-ArrowLeft", run: cursorSyntaxLeft, shift: selectSyntaxLeft },
    { key: "Alt-ArrowRight", mac: "Ctrl-ArrowRight", run: cursorSyntaxRight, shift: selectSyntaxRight },
    { key: "Alt-ArrowUp", run: moveLineUp },
    { key: "Shift-Alt-ArrowUp", run: copyLineUp },
    { key: "Alt-ArrowDown", run: moveLineDown },
    { key: "Shift-Alt-ArrowDown", run: copyLineDown },
    { key: "Escape", run: simplifySelection },
    { key: "Mod-Enter", run: insertBlankLine },
    { key: "Alt-l", mac: "Ctrl-l", run: selectLine },
    { key: "Mod-i", run: selectParentSyntax, preventDefault: true },
    { key: "Mod-[", run: indentLess },
    { key: "Mod-]", run: indentMore },
    { key: "Mod-Alt-\\", run: indentSelection },
    { key: "Shift-Mod-k", run: deleteLine },
    { key: "Shift-Mod-\\", run: cursorMatchingBracket }
  ].concat(standardKeymap);
  var indentWithTab = { key: "Tab", run: indentMore, shift: indentLess };

  // node_modules/@codemirror/closebrackets/dist/index.js
  var defaults2 = {
    brackets: ["(", "[", "{", "'", '"'],
    before: ")]}:;>"
  };
  var closeBracketEffect = /* @__PURE__ */ StateEffect.define({
    map(value, mapping) {
      let mapped = mapping.mapPos(value, -1, MapMode.TrackAfter);
      return mapped == null ? void 0 : mapped;
    }
  });
  var skipBracketEffect = /* @__PURE__ */ StateEffect.define({
    map(value, mapping) {
      return mapping.mapPos(value);
    }
  });
  var closedBracket = /* @__PURE__ */ new class extends RangeValue {
  }();
  closedBracket.startSide = 1;
  closedBracket.endSide = -1;
  var bracketState = /* @__PURE__ */ StateField.define({
    create() {
      return RangeSet.empty;
    },
    update(value, tr) {
      if (tr.selection) {
        let lineStart = tr.state.doc.lineAt(tr.selection.main.head).from;
        let prevLineStart = tr.startState.doc.lineAt(tr.startState.selection.main.head).from;
        if (lineStart != tr.changes.mapPos(prevLineStart, -1))
          value = RangeSet.empty;
      }
      value = value.map(tr.changes);
      for (let effect of tr.effects) {
        if (effect.is(closeBracketEffect))
          value = value.update({ add: [closedBracket.range(effect.value, effect.value + 1)] });
        else if (effect.is(skipBracketEffect))
          value = value.update({ filter: (from2) => from2 != effect.value });
      }
      return value;
    }
  });
  function closeBrackets() {
    return [inputHandler2, bracketState];
  }
  var definedClosing = "()[]{}<>";
  function closing(ch) {
    for (let i = 0; i < definedClosing.length; i += 2)
      if (definedClosing.charCodeAt(i) == ch)
        return definedClosing.charAt(i + 1);
    return fromCodePoint(ch < 128 ? ch : ch + 1);
  }
  function config(state, pos) {
    return state.languageDataAt("closeBrackets", pos)[0] || defaults2;
  }
  var android = typeof navigator == "object" && /* @__PURE__ */ /Android\b/.test(navigator.userAgent);
  var inputHandler2 = /* @__PURE__ */ EditorView.inputHandler.of((view, from2, to, insert2) => {
    if ((android ? view.composing : view.compositionStarted) || view.state.readOnly)
      return false;
    let sel = view.state.selection.main;
    if (insert2.length > 2 || insert2.length == 2 && codePointSize(codePointAt(insert2, 0)) == 1 || from2 != sel.from || to != sel.to)
      return false;
    let tr = insertBracket(view.state, insert2);
    if (!tr)
      return false;
    view.dispatch(tr);
    return true;
  });
  var deleteBracketPair = ({ state, dispatch }) => {
    if (state.readOnly)
      return false;
    let conf = config(state, state.selection.main.head);
    let tokens = conf.brackets || defaults2.brackets;
    let dont = null, changes = state.changeByRange((range) => {
      if (range.empty) {
        let before = prevChar(state.doc, range.head);
        for (let token of tokens) {
          if (token == before && nextChar(state.doc, range.head) == closing(codePointAt(token, 0)))
            return {
              changes: { from: range.head - token.length, to: range.head + token.length },
              range: EditorSelection.cursor(range.head - token.length),
              userEvent: "delete.backward"
            };
        }
      }
      return { range: dont = range };
    });
    if (!dont)
      dispatch(state.update(changes, { scrollIntoView: true }));
    return !dont;
  };
  var closeBracketsKeymap = [
    { key: "Backspace", run: deleteBracketPair }
  ];
  function insertBracket(state, bracket2) {
    let conf = config(state, state.selection.main.head);
    let tokens = conf.brackets || defaults2.brackets;
    for (let tok of tokens) {
      let closed = closing(codePointAt(tok, 0));
      if (bracket2 == tok)
        return closed == tok ? handleSame(state, tok, tokens.indexOf(tok + tok + tok) > -1) : handleOpen(state, tok, closed, conf.before || defaults2.before);
      if (bracket2 == closed && closedBracketAt(state, state.selection.main.from))
        return handleClose(state, tok, closed);
    }
    return null;
  }
  function closedBracketAt(state, pos) {
    let found = false;
    state.field(bracketState).between(0, state.doc.length, (from2) => {
      if (from2 == pos)
        found = true;
    });
    return found;
  }
  function nextChar(doc2, pos) {
    let next = doc2.sliceString(pos, pos + 2);
    return next.slice(0, codePointSize(codePointAt(next, 0)));
  }
  function prevChar(doc2, pos) {
    let prev = doc2.sliceString(pos - 2, pos);
    return codePointSize(codePointAt(prev, 0)) == prev.length ? prev : prev.slice(1);
  }
  function handleOpen(state, open, close, closeBefore) {
    let dont = null, changes = state.changeByRange((range) => {
      if (!range.empty)
        return {
          changes: [{ insert: open, from: range.from }, { insert: close, from: range.to }],
          effects: closeBracketEffect.of(range.to + open.length),
          range: EditorSelection.range(range.anchor + open.length, range.head + open.length)
        };
      let next = nextChar(state.doc, range.head);
      if (!next || /\s/.test(next) || closeBefore.indexOf(next) > -1)
        return {
          changes: { insert: open + close, from: range.head },
          effects: closeBracketEffect.of(range.head + open.length),
          range: EditorSelection.cursor(range.head + open.length)
        };
      return { range: dont = range };
    });
    return dont ? null : state.update(changes, {
      scrollIntoView: true,
      userEvent: "input.type"
    });
  }
  function handleClose(state, _open, close) {
    let dont = null, moved = state.selection.ranges.map((range) => {
      if (range.empty && nextChar(state.doc, range.head) == close)
        return EditorSelection.cursor(range.head + close.length);
      return dont = range;
    });
    return dont ? null : state.update({
      selection: EditorSelection.create(moved, state.selection.mainIndex),
      scrollIntoView: true,
      effects: state.selection.ranges.map(({ from: from2 }) => skipBracketEffect.of(from2))
    });
  }
  function handleSame(state, token, allowTriple) {
    let dont = null, changes = state.changeByRange((range) => {
      if (!range.empty)
        return {
          changes: [{ insert: token, from: range.from }, { insert: token, from: range.to }],
          effects: closeBracketEffect.of(range.to + token.length),
          range: EditorSelection.range(range.anchor + token.length, range.head + token.length)
        };
      let pos = range.head, next = nextChar(state.doc, pos);
      if (next == token) {
        if (nodeStart(state, pos)) {
          return {
            changes: { insert: token + token, from: pos },
            effects: closeBracketEffect.of(pos + token.length),
            range: EditorSelection.cursor(pos + token.length)
          };
        } else if (closedBracketAt(state, pos)) {
          let isTriple = allowTriple && state.sliceDoc(pos, pos + token.length * 3) == token + token + token;
          return {
            range: EditorSelection.cursor(pos + token.length * (isTriple ? 3 : 1)),
            effects: skipBracketEffect.of(pos)
          };
        }
      } else if (allowTriple && state.sliceDoc(pos - 2 * token.length, pos) == token + token && nodeStart(state, pos - 2 * token.length)) {
        return {
          changes: { insert: token + token + token + token, from: pos },
          effects: closeBracketEffect.of(pos + token.length),
          range: EditorSelection.cursor(pos + token.length)
        };
      } else if (state.charCategorizer(pos)(next) != CharCategory.Word) {
        let prev = state.sliceDoc(pos - 1, pos);
        if (prev != token && state.charCategorizer(pos)(prev) != CharCategory.Word && !probablyInString(state, pos, token))
          return {
            changes: { insert: token + token, from: pos },
            effects: closeBracketEffect.of(pos + token.length),
            range: EditorSelection.cursor(pos + token.length)
          };
      }
      return { range: dont = range };
    });
    return dont ? null : state.update(changes, {
      scrollIntoView: true,
      userEvent: "input.type"
    });
  }
  function nodeStart(state, pos) {
    let tree = syntaxTree(state).resolveInner(pos + 1);
    return tree.parent && tree.from == pos;
  }
  function probablyInString(state, pos, quoteToken) {
    let node = syntaxTree(state).resolveInner(pos, -1);
    for (let i = 0; i < 5; i++) {
      if (state.sliceDoc(node.from, node.from + quoteToken.length) == quoteToken)
        return true;
      let parent = node.to == pos && node.parent;
      if (!parent)
        break;
      node = parent;
    }
    return false;
  }

  // node_modules/@codemirror/panel/dist/index.js
  var panelConfig = /* @__PURE__ */ Facet.define({
    combine(configs) {
      let topContainer, bottomContainer;
      for (let c of configs) {
        topContainer = topContainer || c.topContainer;
        bottomContainer = bottomContainer || c.bottomContainer;
      }
      return { topContainer, bottomContainer };
    }
  });
  function getPanel(view, panel) {
    let plugin = view.plugin(panelPlugin);
    let index = plugin ? plugin.specs.indexOf(panel) : -1;
    return index > -1 ? plugin.panels[index] : null;
  }
  var panelPlugin = /* @__PURE__ */ ViewPlugin.fromClass(class {
    constructor(view) {
      this.input = view.state.facet(showPanel);
      this.specs = this.input.filter((s) => s);
      this.panels = this.specs.map((spec) => spec(view));
      let conf = view.state.facet(panelConfig);
      this.top = new PanelGroup(view, true, conf.topContainer);
      this.bottom = new PanelGroup(view, false, conf.bottomContainer);
      this.top.sync(this.panels.filter((p) => p.top));
      this.bottom.sync(this.panels.filter((p) => !p.top));
      for (let p of this.panels) {
        p.dom.classList.add("cm-panel");
        if (p.mount)
          p.mount();
      }
    }
    update(update) {
      let conf = update.state.facet(panelConfig);
      if (this.top.container != conf.topContainer) {
        this.top.sync([]);
        this.top = new PanelGroup(update.view, true, conf.topContainer);
      }
      if (this.bottom.container != conf.bottomContainer) {
        this.bottom.sync([]);
        this.bottom = new PanelGroup(update.view, false, conf.bottomContainer);
      }
      this.top.syncClasses();
      this.bottom.syncClasses();
      let input = update.state.facet(showPanel);
      if (input != this.input) {
        let specs = input.filter((x) => x);
        let panels = [], top2 = [], bottom = [], mount = [];
        for (let spec of specs) {
          let known = this.specs.indexOf(spec), panel;
          if (known < 0) {
            panel = spec(update.view);
            mount.push(panel);
          } else {
            panel = this.panels[known];
            if (panel.update)
              panel.update(update);
          }
          panels.push(panel);
          (panel.top ? top2 : bottom).push(panel);
        }
        this.specs = specs;
        this.panels = panels;
        this.top.sync(top2);
        this.bottom.sync(bottom);
        for (let p of mount) {
          p.dom.classList.add("cm-panel");
          if (p.mount)
            p.mount();
        }
      } else {
        for (let p of this.panels)
          if (p.update)
            p.update(update);
      }
    }
    destroy() {
      this.top.sync([]);
      this.bottom.sync([]);
    }
  }, {
    provide: /* @__PURE__ */ PluginField.scrollMargins.from((value) => ({ top: value.top.scrollMargin(), bottom: value.bottom.scrollMargin() }))
  });
  var PanelGroup = class {
    constructor(view, top2, container) {
      this.view = view;
      this.top = top2;
      this.container = container;
      this.dom = void 0;
      this.classes = "";
      this.panels = [];
      this.syncClasses();
    }
    sync(panels) {
      for (let p of this.panels)
        if (p.destroy && panels.indexOf(p) < 0)
          p.destroy();
      this.panels = panels;
      this.syncDOM();
    }
    syncDOM() {
      if (this.panels.length == 0) {
        if (this.dom) {
          this.dom.remove();
          this.dom = void 0;
        }
        return;
      }
      if (!this.dom) {
        this.dom = document.createElement("div");
        this.dom.className = this.top ? "cm-panels cm-panels-top" : "cm-panels cm-panels-bottom";
        this.dom.style[this.top ? "top" : "bottom"] = "0";
        let parent = this.container || this.view.dom;
        parent.insertBefore(this.dom, this.top ? parent.firstChild : null);
      }
      let curDOM = this.dom.firstChild;
      for (let panel of this.panels) {
        if (panel.dom.parentNode == this.dom) {
          while (curDOM != panel.dom)
            curDOM = rm2(curDOM);
          curDOM = curDOM.nextSibling;
        } else {
          this.dom.insertBefore(panel.dom, curDOM);
        }
      }
      while (curDOM)
        curDOM = rm2(curDOM);
    }
    scrollMargin() {
      return !this.dom || this.container ? 0 : Math.max(0, this.top ? this.dom.getBoundingClientRect().bottom - Math.max(0, this.view.scrollDOM.getBoundingClientRect().top) : Math.min(innerHeight, this.view.scrollDOM.getBoundingClientRect().bottom) - this.dom.getBoundingClientRect().top);
    }
    syncClasses() {
      if (!this.container || this.classes == this.view.themeClasses)
        return;
      for (let cls of this.classes.split(" "))
        if (cls)
          this.container.classList.remove(cls);
      for (let cls of (this.classes = this.view.themeClasses).split(" "))
        if (cls)
          this.container.classList.add(cls);
    }
  };
  function rm2(node) {
    let next = node.nextSibling;
    node.remove();
    return next;
  }
  var baseTheme5 = /* @__PURE__ */ EditorView.baseTheme({
    ".cm-panels": {
      boxSizing: "border-box",
      position: "sticky",
      left: 0,
      right: 0
    },
    "&light .cm-panels": {
      backgroundColor: "#f5f5f5",
      color: "black"
    },
    "&light .cm-panels-top": {
      borderBottom: "1px solid #ddd"
    },
    "&light .cm-panels-bottom": {
      borderTop: "1px solid #ddd"
    },
    "&dark .cm-panels": {
      backgroundColor: "#333338",
      color: "white"
    }
  });
  var showPanel = /* @__PURE__ */ Facet.define({
    enables: [panelPlugin, baseTheme5]
  });

  // node_modules/crelt/index.es.js
  function crelt() {
    var elt = arguments[0];
    if (typeof elt == "string")
      elt = document.createElement(elt);
    var i = 1, next = arguments[1];
    if (next && typeof next == "object" && next.nodeType == null && !Array.isArray(next)) {
      for (var name2 in next)
        if (Object.prototype.hasOwnProperty.call(next, name2)) {
          var value = next[name2];
          if (typeof value == "string")
            elt.setAttribute(name2, value);
          else if (value != null)
            elt[name2] = value;
        }
      i++;
    }
    for (; i < arguments.length; i++)
      add(elt, arguments[i]);
    return elt;
  }
  function add(elt, child) {
    if (typeof child == "string") {
      elt.appendChild(document.createTextNode(child));
    } else if (child == null) {
    } else if (child.nodeType != null) {
      elt.appendChild(child);
    } else if (Array.isArray(child)) {
      for (var i = 0; i < child.length; i++)
        add(elt, child[i]);
    } else {
      throw new RangeError("Unsupported child node: " + child);
    }
  }

  // node_modules/@codemirror/search/dist/index.js
  var basicNormalize = typeof String.prototype.normalize == "function" ? (x) => x.normalize("NFKD") : (x) => x;
  var SearchCursor = class {
    constructor(text, query, from2 = 0, to = text.length, normalize) {
      this.value = { from: 0, to: 0 };
      this.done = false;
      this.matches = [];
      this.buffer = "";
      this.bufferPos = 0;
      this.iter = text.iterRange(from2, to);
      this.bufferStart = from2;
      this.normalize = normalize ? (x) => normalize(basicNormalize(x)) : basicNormalize;
      this.query = this.normalize(query);
    }
    peek() {
      if (this.bufferPos == this.buffer.length) {
        this.bufferStart += this.buffer.length;
        this.iter.next();
        if (this.iter.done)
          return -1;
        this.bufferPos = 0;
        this.buffer = this.iter.value;
      }
      return codePointAt(this.buffer, this.bufferPos);
    }
    next() {
      while (this.matches.length)
        this.matches.pop();
      return this.nextOverlapping();
    }
    nextOverlapping() {
      for (; ; ) {
        let next = this.peek();
        if (next < 0) {
          this.done = true;
          return this;
        }
        let str = fromCodePoint(next), start = this.bufferStart + this.bufferPos;
        this.bufferPos += codePointSize(next);
        let norm = this.normalize(str);
        for (let i = 0, pos = start; ; i++) {
          let code = norm.charCodeAt(i);
          let match2 = this.match(code, pos);
          if (match2) {
            this.value = match2;
            return this;
          }
          if (i == norm.length - 1)
            break;
          if (pos == start && i < str.length && str.charCodeAt(i) == code)
            pos++;
        }
      }
    }
    match(code, pos) {
      let match2 = null;
      for (let i = 0; i < this.matches.length; i += 2) {
        let index = this.matches[i], keep = false;
        if (this.query.charCodeAt(index) == code) {
          if (index == this.query.length - 1) {
            match2 = { from: this.matches[i + 1], to: pos + 1 };
          } else {
            this.matches[i]++;
            keep = true;
          }
        }
        if (!keep) {
          this.matches.splice(i, 2);
          i -= 2;
        }
      }
      if (this.query.charCodeAt(0) == code) {
        if (this.query.length == 1)
          match2 = { from: pos, to: pos + 1 };
        else
          this.matches.push(1, pos);
      }
      return match2;
    }
  };
  if (typeof Symbol != "undefined")
    SearchCursor.prototype[Symbol.iterator] = function() {
      return this;
    };
  var empty = { from: -1, to: -1, match: /* @__PURE__ */ /.*/.exec("") };
  var baseFlags = "gm" + (/x/.unicode == null ? "" : "u");
  var RegExpCursor = class {
    constructor(text, query, options, from2 = 0, to = text.length) {
      this.to = to;
      this.curLine = "";
      this.done = false;
      this.value = empty;
      if (/\\[sWDnr]|\n|\r|\[\^/.test(query))
        return new MultilineRegExpCursor(text, query, options, from2, to);
      this.re = new RegExp(query, baseFlags + ((options === null || options === void 0 ? void 0 : options.ignoreCase) ? "i" : ""));
      this.iter = text.iter();
      let startLine = text.lineAt(from2);
      this.curLineStart = startLine.from;
      this.matchPos = from2;
      this.getLine(this.curLineStart);
    }
    getLine(skip) {
      this.iter.next(skip);
      if (this.iter.lineBreak) {
        this.curLine = "";
      } else {
        this.curLine = this.iter.value;
        if (this.curLineStart + this.curLine.length > this.to)
          this.curLine = this.curLine.slice(0, this.to - this.curLineStart);
        this.iter.next();
      }
    }
    nextLine() {
      this.curLineStart = this.curLineStart + this.curLine.length + 1;
      if (this.curLineStart > this.to)
        this.curLine = "";
      else
        this.getLine(0);
    }
    next() {
      for (let off = this.matchPos - this.curLineStart; ; ) {
        this.re.lastIndex = off;
        let match2 = this.matchPos <= this.to && this.re.exec(this.curLine);
        if (match2) {
          let from2 = this.curLineStart + match2.index, to = from2 + match2[0].length;
          this.matchPos = to + (from2 == to ? 1 : 0);
          if (from2 == this.curLine.length)
            this.nextLine();
          if (from2 < to || from2 > this.value.to) {
            this.value = { from: from2, to, match: match2 };
            return this;
          }
          off = this.matchPos - this.curLineStart;
        } else if (this.curLineStart + this.curLine.length < this.to) {
          this.nextLine();
          off = 0;
        } else {
          this.done = true;
          return this;
        }
      }
    }
  };
  var flattened = /* @__PURE__ */ new WeakMap();
  var FlattenedDoc = class {
    constructor(from2, text) {
      this.from = from2;
      this.text = text;
    }
    get to() {
      return this.from + this.text.length;
    }
    static get(doc2, from2, to) {
      let cached = flattened.get(doc2);
      if (!cached || cached.from >= to || cached.to <= from2) {
        let flat = new FlattenedDoc(from2, doc2.sliceString(from2, to));
        flattened.set(doc2, flat);
        return flat;
      }
      if (cached.from == from2 && cached.to == to)
        return cached;
      let { text, from: cachedFrom } = cached;
      if (cachedFrom > from2) {
        text = doc2.sliceString(from2, cachedFrom) + text;
        cachedFrom = from2;
      }
      if (cached.to < to)
        text += doc2.sliceString(cached.to, to);
      flattened.set(doc2, new FlattenedDoc(cachedFrom, text));
      return new FlattenedDoc(from2, text.slice(from2 - cachedFrom, to - cachedFrom));
    }
  };
  var MultilineRegExpCursor = class {
    constructor(text, query, options, from2, to) {
      this.text = text;
      this.to = to;
      this.done = false;
      this.value = empty;
      this.matchPos = from2;
      this.re = new RegExp(query, baseFlags + ((options === null || options === void 0 ? void 0 : options.ignoreCase) ? "i" : ""));
      this.flat = FlattenedDoc.get(text, from2, this.chunkEnd(from2 + 5e3));
    }
    chunkEnd(pos) {
      return pos >= this.to ? this.to : this.text.lineAt(pos).to;
    }
    next() {
      for (; ; ) {
        let off = this.re.lastIndex = this.matchPos - this.flat.from;
        let match2 = this.re.exec(this.flat.text);
        if (match2 && !match2[0] && match2.index == off) {
          this.re.lastIndex = off + 1;
          match2 = this.re.exec(this.flat.text);
        }
        if (match2 && this.flat.to < this.to && match2.index + match2[0].length > this.flat.text.length - 10)
          match2 = null;
        if (match2) {
          let from2 = this.flat.from + match2.index, to = from2 + match2[0].length;
          this.value = { from: from2, to, match: match2 };
          this.matchPos = to + (from2 == to ? 1 : 0);
          return this;
        } else {
          if (this.flat.to == this.to) {
            this.done = true;
            return this;
          }
          this.flat = FlattenedDoc.get(this.text, this.flat.from, this.chunkEnd(this.flat.from + this.flat.text.length * 2));
        }
      }
    }
  };
  if (typeof Symbol != "undefined") {
    RegExpCursor.prototype[Symbol.iterator] = MultilineRegExpCursor.prototype[Symbol.iterator] = function() {
      return this;
    };
  }
  function validRegExp(source) {
    try {
      new RegExp(source, baseFlags);
      return true;
    } catch (_a2) {
      return false;
    }
  }
  function createLineDialog(view) {
    let input = crelt("input", { class: "cm-textfield", name: "line" });
    let dom = crelt("form", {
      class: "cm-gotoLine",
      onkeydown: (event) => {
        if (event.keyCode == 27) {
          event.preventDefault();
          view.dispatch({ effects: dialogEffect.of(false) });
          view.focus();
        } else if (event.keyCode == 13) {
          event.preventDefault();
          go();
        }
      },
      onsubmit: (event) => {
        event.preventDefault();
        go();
      }
    }, crelt("label", view.state.phrase("Go to line"), ": ", input), " ", crelt("button", { class: "cm-button", type: "submit" }, view.state.phrase("go")));
    function go() {
      let match2 = /^([+-])?(\d+)?(:\d+)?(%)?$/.exec(input.value);
      if (!match2)
        return;
      let { state } = view, startLine = state.doc.lineAt(state.selection.main.head);
      let [, sign, ln, cl, percent2] = match2;
      let col = cl ? +cl.slice(1) : 0;
      let line = ln ? +ln : startLine.number;
      if (ln && percent2) {
        let pc = line / 100;
        if (sign)
          pc = pc * (sign == "-" ? -1 : 1) + startLine.number / state.doc.lines;
        line = Math.round(state.doc.lines * pc);
      } else if (ln && sign) {
        line = line * (sign == "-" ? -1 : 1) + startLine.number;
      }
      let docLine = state.doc.line(Math.max(1, Math.min(state.doc.lines, line)));
      view.dispatch({
        effects: dialogEffect.of(false),
        selection: EditorSelection.cursor(docLine.from + Math.max(0, Math.min(col, docLine.length))),
        scrollIntoView: true
      });
      view.focus();
    }
    return { dom, pos: -10 };
  }
  var dialogEffect = /* @__PURE__ */ StateEffect.define();
  var dialogField = /* @__PURE__ */ StateField.define({
    create() {
      return true;
    },
    update(value, tr) {
      for (let e of tr.effects)
        if (e.is(dialogEffect))
          value = e.value;
      return value;
    },
    provide: (f) => showPanel.from(f, (val) => val ? createLineDialog : null)
  });
  var gotoLine = (view) => {
    let panel = getPanel(view, createLineDialog);
    if (!panel) {
      let effects = [dialogEffect.of(true)];
      if (view.state.field(dialogField, false) == null)
        effects.push(StateEffect.appendConfig.of([dialogField, baseTheme$1]));
      view.dispatch({ effects });
      panel = getPanel(view, createLineDialog);
    }
    if (panel)
      panel.dom.querySelector("input").focus();
    return true;
  };
  var baseTheme$1 = /* @__PURE__ */ EditorView.baseTheme({
    ".cm-panel.cm-gotoLine": {
      padding: "2px 6px 4px",
      "& label": { fontSize: "80%" }
    }
  });
  var defaultHighlightOptions = {
    highlightWordAroundCursor: false,
    minSelectionLength: 1,
    maxMatches: 100,
    wholeWords: true
  };
  var highlightConfig = /* @__PURE__ */ Facet.define({
    combine(options) {
      return combineConfig(options, defaultHighlightOptions, {
        highlightWordAroundCursor: (a, b) => a || b,
        minSelectionLength: Math.min,
        maxMatches: Math.min
      });
    }
  });
  function highlightSelectionMatches(options) {
    let ext = [defaultTheme, matchHighlighter];
    if (options)
      ext.push(highlightConfig.of(options));
    return ext;
  }
  var matchDeco = /* @__PURE__ */ Decoration.mark({ class: "cm-selectionMatch" });
  var mainMatchDeco = /* @__PURE__ */ Decoration.mark({ class: "cm-selectionMatch cm-selectionMatch-main" });
  function insideWordBoundaries(check, state, from2, to) {
    return (from2 == 0 || check(state.sliceDoc(from2 - 1, from2)) != CharCategory.Word) && (to == state.doc.length || check(state.sliceDoc(to, to + 1)) != CharCategory.Word);
  }
  function insideWord(check, state, from2, to) {
    return check(state.sliceDoc(from2, from2 + 1)) == CharCategory.Word && check(state.sliceDoc(to - 1, to)) == CharCategory.Word;
  }
  var matchHighlighter = /* @__PURE__ */ ViewPlugin.fromClass(class {
    constructor(view) {
      this.decorations = this.getDeco(view);
    }
    update(update) {
      if (update.selectionSet || update.docChanged || update.viewportChanged)
        this.decorations = this.getDeco(update.view);
    }
    getDeco(view) {
      let conf = view.state.facet(highlightConfig);
      let { state } = view, sel = state.selection;
      if (sel.ranges.length > 1)
        return Decoration.none;
      let range = sel.main, query, check = null;
      if (range.empty) {
        if (!conf.highlightWordAroundCursor)
          return Decoration.none;
        let word = state.wordAt(range.head);
        if (!word)
          return Decoration.none;
        check = state.charCategorizer(range.head);
        query = state.sliceDoc(word.from, word.to);
      } else {
        let len = range.to - range.from;
        if (len < conf.minSelectionLength || len > 200)
          return Decoration.none;
        if (conf.wholeWords) {
          query = state.sliceDoc(range.from, range.to);
          check = state.charCategorizer(range.head);
          if (!(insideWordBoundaries(check, state, range.from, range.to) && insideWord(check, state, range.from, range.to)))
            return Decoration.none;
        } else {
          query = state.sliceDoc(range.from, range.to).trim();
          if (!query)
            return Decoration.none;
        }
      }
      let deco = [];
      for (let part of view.visibleRanges) {
        let cursor = new SearchCursor(state.doc, query, part.from, part.to);
        while (!cursor.next().done) {
          let { from: from2, to } = cursor.value;
          if (!check || insideWordBoundaries(check, state, from2, to)) {
            if (range.empty && from2 <= range.from && to >= range.to)
              deco.push(mainMatchDeco.range(from2, to));
            else if (from2 >= range.to || to <= range.from)
              deco.push(matchDeco.range(from2, to));
            if (deco.length > conf.maxMatches)
              return Decoration.none;
          }
        }
      }
      return Decoration.set(deco);
    }
  }, {
    decorations: (v) => v.decorations
  });
  var defaultTheme = /* @__PURE__ */ EditorView.baseTheme({
    ".cm-selectionMatch": { backgroundColor: "#99ff7780" },
    ".cm-searchMatch .cm-selectionMatch": { backgroundColor: "transparent" }
  });
  var selectWord = ({ state, dispatch }) => {
    let { selection } = state;
    let newSel = EditorSelection.create(selection.ranges.map((range) => state.wordAt(range.head) || EditorSelection.cursor(range.head)), selection.mainIndex);
    if (newSel.eq(selection))
      return false;
    dispatch(state.update({ selection: newSel }));
    return true;
  };
  function findNextOccurrence(state, query) {
    let { main, ranges } = state.selection;
    let word = state.wordAt(main.head), fullWord = word && word.from == main.from && word.to == main.to;
    for (let cycled = false, cursor = new SearchCursor(state.doc, query, ranges[ranges.length - 1].to); ; ) {
      cursor.next();
      if (cursor.done) {
        if (cycled)
          return null;
        cursor = new SearchCursor(state.doc, query, 0, Math.max(0, ranges[ranges.length - 1].from - 1));
        cycled = true;
      } else {
        if (cycled && ranges.some((r) => r.from == cursor.value.from))
          continue;
        if (fullWord) {
          let word2 = state.wordAt(cursor.value.from);
          if (!word2 || word2.from != cursor.value.from || word2.to != cursor.value.to)
            continue;
        }
        return cursor.value;
      }
    }
  }
  var selectNextOccurrence = ({ state, dispatch }) => {
    let { ranges } = state.selection;
    if (ranges.some((sel) => sel.from === sel.to))
      return selectWord({ state, dispatch });
    let searchedText = state.sliceDoc(ranges[0].from, ranges[0].to);
    if (state.selection.ranges.some((r) => state.sliceDoc(r.from, r.to) != searchedText))
      return false;
    let range = findNextOccurrence(state, searchedText);
    if (!range)
      return false;
    dispatch(state.update({
      selection: state.selection.addRange(EditorSelection.range(range.from, range.to), false),
      effects: EditorView.scrollIntoView(range.to)
    }));
    return true;
  };
  var searchConfigFacet = /* @__PURE__ */ Facet.define({
    combine(configs) {
      var _a2;
      return {
        top: configs.reduce((val, conf) => val !== null && val !== void 0 ? val : conf.top, void 0) || false,
        caseSensitive: configs.reduce((val, conf) => val !== null && val !== void 0 ? val : conf.caseSensitive || conf.matchCase, void 0) || false,
        createPanel: ((_a2 = configs.find((c) => c.createPanel)) === null || _a2 === void 0 ? void 0 : _a2.createPanel) || ((view) => new SearchPanel(view))
      };
    }
  });
  var SearchQuery = class {
    constructor(config2) {
      this.search = config2.search;
      this.caseSensitive = !!config2.caseSensitive;
      this.regexp = !!config2.regexp;
      this.replace = config2.replace || "";
      this.valid = !!this.search && (!this.regexp || validRegExp(this.search));
      this.unquoted = this.search.replace(/\\([nrt\\])/g, (_, ch) => ch == "n" ? "\n" : ch == "r" ? "\r" : ch == "t" ? "	" : "\\");
    }
    eq(other) {
      return this.search == other.search && this.replace == other.replace && this.caseSensitive == other.caseSensitive && this.regexp == other.regexp;
    }
    create() {
      return this.regexp ? new RegExpQuery(this) : new StringQuery(this);
    }
    getCursor(doc2, from2 = 0, to = doc2.length) {
      return this.regexp ? regexpCursor(this, doc2, from2, to) : stringCursor(this, doc2, from2, to);
    }
  };
  var QueryType2 = class {
    constructor(spec) {
      this.spec = spec;
    }
  };
  function stringCursor(spec, doc2, from2, to) {
    return new SearchCursor(doc2, spec.unquoted, from2, to, spec.caseSensitive ? void 0 : (x) => x.toLowerCase());
  }
  var StringQuery = class extends QueryType2 {
    constructor(spec) {
      super(spec);
    }
    nextMatch(doc2, curFrom, curTo) {
      let cursor = stringCursor(this.spec, doc2, curTo, doc2.length).nextOverlapping();
      if (cursor.done)
        cursor = stringCursor(this.spec, doc2, 0, curFrom).nextOverlapping();
      return cursor.done ? null : cursor.value;
    }
    prevMatchInRange(doc2, from2, to) {
      for (let pos = to; ; ) {
        let start = Math.max(from2, pos - 1e4 - this.spec.unquoted.length);
        let cursor = stringCursor(this.spec, doc2, start, pos), range = null;
        while (!cursor.nextOverlapping().done)
          range = cursor.value;
        if (range)
          return range;
        if (start == from2)
          return null;
        pos -= 1e4;
      }
    }
    prevMatch(doc2, curFrom, curTo) {
      return this.prevMatchInRange(doc2, 0, curFrom) || this.prevMatchInRange(doc2, curTo, doc2.length);
    }
    getReplacement(_result) {
      return this.spec.replace;
    }
    matchAll(doc2, limit) {
      let cursor = stringCursor(this.spec, doc2, 0, doc2.length), ranges = [];
      while (!cursor.next().done) {
        if (ranges.length >= limit)
          return null;
        ranges.push(cursor.value);
      }
      return ranges;
    }
    highlight(doc2, from2, to, add2) {
      let cursor = stringCursor(this.spec, doc2, Math.max(0, from2 - this.spec.unquoted.length), Math.min(to + this.spec.unquoted.length, doc2.length));
      while (!cursor.next().done)
        add2(cursor.value.from, cursor.value.to);
    }
  };
  function regexpCursor(spec, doc2, from2, to) {
    return new RegExpCursor(doc2, spec.search, spec.caseSensitive ? void 0 : { ignoreCase: true }, from2, to);
  }
  var RegExpQuery = class extends QueryType2 {
    nextMatch(doc2, curFrom, curTo) {
      let cursor = regexpCursor(this.spec, doc2, curTo, doc2.length).next();
      if (cursor.done)
        cursor = regexpCursor(this.spec, doc2, 0, curFrom).next();
      return cursor.done ? null : cursor.value;
    }
    prevMatchInRange(doc2, from2, to) {
      for (let size = 1; ; size++) {
        let start = Math.max(from2, to - size * 1e4);
        let cursor = regexpCursor(this.spec, doc2, start, to), range = null;
        while (!cursor.next().done)
          range = cursor.value;
        if (range && (start == from2 || range.from > start + 10))
          return range;
        if (start == from2)
          return null;
      }
    }
    prevMatch(doc2, curFrom, curTo) {
      return this.prevMatchInRange(doc2, 0, curFrom) || this.prevMatchInRange(doc2, curTo, doc2.length);
    }
    getReplacement(result) {
      return this.spec.replace.replace(/\$([$&\d+])/g, (m, i) => i == "$" ? "$" : i == "&" ? result.match[0] : i != "0" && +i < result.match.length ? result.match[i] : m);
    }
    matchAll(doc2, limit) {
      let cursor = regexpCursor(this.spec, doc2, 0, doc2.length), ranges = [];
      while (!cursor.next().done) {
        if (ranges.length >= limit)
          return null;
        ranges.push(cursor.value);
      }
      return ranges;
    }
    highlight(doc2, from2, to, add2) {
      let cursor = regexpCursor(this.spec, doc2, Math.max(0, from2 - 250), Math.min(to + 250, doc2.length));
      while (!cursor.next().done)
        add2(cursor.value.from, cursor.value.to);
    }
  };
  var setSearchQuery = /* @__PURE__ */ StateEffect.define();
  var togglePanel = /* @__PURE__ */ StateEffect.define();
  var searchState = /* @__PURE__ */ StateField.define({
    create(state) {
      return new SearchState(defaultQuery(state).create(), null);
    },
    update(value, tr) {
      for (let effect of tr.effects) {
        if (effect.is(setSearchQuery))
          value = new SearchState(effect.value.create(), value.panel);
        else if (effect.is(togglePanel))
          value = new SearchState(value.query, effect.value ? createSearchPanel : null);
      }
      return value;
    },
    provide: (f) => showPanel.from(f, (val) => val.panel)
  });
  var SearchState = class {
    constructor(query, panel) {
      this.query = query;
      this.panel = panel;
    }
  };
  var matchMark = /* @__PURE__ */ Decoration.mark({ class: "cm-searchMatch" });
  var selectedMatchMark = /* @__PURE__ */ Decoration.mark({ class: "cm-searchMatch cm-searchMatch-selected" });
  var searchHighlighter = /* @__PURE__ */ ViewPlugin.fromClass(class {
    constructor(view) {
      this.view = view;
      this.decorations = this.highlight(view.state.field(searchState));
    }
    update(update) {
      let state = update.state.field(searchState);
      if (state != update.startState.field(searchState) || update.docChanged || update.selectionSet || update.viewportChanged)
        this.decorations = this.highlight(state);
    }
    highlight({ query, panel }) {
      if (!panel || !query.spec.valid)
        return Decoration.none;
      let { view } = this;
      let builder = new RangeSetBuilder();
      for (let i = 0, ranges = view.visibleRanges, l = ranges.length; i < l; i++) {
        let { from: from2, to } = ranges[i];
        while (i < l - 1 && to > ranges[i + 1].from - 2 * 250)
          to = ranges[++i].to;
        query.highlight(view.state.doc, from2, to, (from3, to2) => {
          let selected = view.state.selection.ranges.some((r) => r.from == from3 && r.to == to2);
          builder.add(from3, to2, selected ? selectedMatchMark : matchMark);
        });
      }
      return builder.finish();
    }
  }, {
    decorations: (v) => v.decorations
  });
  function searchCommand(f) {
    return (view) => {
      let state = view.state.field(searchState, false);
      return state && state.query.spec.valid ? f(view, state) : openSearchPanel(view);
    };
  }
  var findNext = /* @__PURE__ */ searchCommand((view, { query }) => {
    let { from: from2, to } = view.state.selection.main;
    let next = query.nextMatch(view.state.doc, from2, to);
    if (!next || next.from == from2 && next.to == to)
      return false;
    view.dispatch({
      selection: { anchor: next.from, head: next.to },
      scrollIntoView: true,
      effects: announceMatch(view, next),
      userEvent: "select.search"
    });
    return true;
  });
  var findPrevious = /* @__PURE__ */ searchCommand((view, { query }) => {
    let { state } = view, { from: from2, to } = state.selection.main;
    let range = query.prevMatch(state.doc, from2, to);
    if (!range)
      return false;
    view.dispatch({
      selection: { anchor: range.from, head: range.to },
      scrollIntoView: true,
      effects: announceMatch(view, range),
      userEvent: "select.search"
    });
    return true;
  });
  var selectMatches = /* @__PURE__ */ searchCommand((view, { query }) => {
    let ranges = query.matchAll(view.state.doc, 1e3);
    if (!ranges || !ranges.length)
      return false;
    view.dispatch({
      selection: EditorSelection.create(ranges.map((r) => EditorSelection.range(r.from, r.to))),
      userEvent: "select.search.matches"
    });
    return true;
  });
  var selectSelectionMatches = ({ state, dispatch }) => {
    let sel = state.selection;
    if (sel.ranges.length > 1 || sel.main.empty)
      return false;
    let { from: from2, to } = sel.main;
    let ranges = [], main = 0;
    for (let cur2 = new SearchCursor(state.doc, state.sliceDoc(from2, to)); !cur2.next().done; ) {
      if (ranges.length > 1e3)
        return false;
      if (cur2.value.from == from2)
        main = ranges.length;
      ranges.push(EditorSelection.range(cur2.value.from, cur2.value.to));
    }
    dispatch(state.update({
      selection: EditorSelection.create(ranges, main),
      userEvent: "select.search.matches"
    }));
    return true;
  };
  var replaceNext = /* @__PURE__ */ searchCommand((view, { query }) => {
    let { state } = view, { from: from2, to } = state.selection.main;
    if (state.readOnly)
      return false;
    let next = query.nextMatch(state.doc, from2, from2);
    if (!next)
      return false;
    let changes = [], selection, replacement;
    if (next.from == from2 && next.to == to) {
      replacement = state.toText(query.getReplacement(next));
      changes.push({ from: next.from, to: next.to, insert: replacement });
      next = query.nextMatch(state.doc, next.from, next.to);
    }
    if (next) {
      let off = changes.length == 0 || changes[0].from >= next.to ? 0 : next.to - next.from - replacement.length;
      selection = { anchor: next.from - off, head: next.to - off };
    }
    view.dispatch({
      changes,
      selection,
      scrollIntoView: !!selection,
      effects: next ? announceMatch(view, next) : void 0,
      userEvent: "input.replace"
    });
    return true;
  });
  var replaceAll = /* @__PURE__ */ searchCommand((view, { query }) => {
    if (view.state.readOnly)
      return false;
    let changes = query.matchAll(view.state.doc, 1e9).map((match2) => {
      let { from: from2, to } = match2;
      return { from: from2, to, insert: query.getReplacement(match2) };
    });
    if (!changes.length)
      return false;
    view.dispatch({
      changes,
      userEvent: "input.replace.all"
    });
    return true;
  });
  function createSearchPanel(view) {
    return view.state.facet(searchConfigFacet).createPanel(view);
  }
  function defaultQuery(state, fallback) {
    var _a2;
    let sel = state.selection.main;
    let selText = sel.empty || sel.to > sel.from + 100 ? "" : state.sliceDoc(sel.from, sel.to);
    let caseSensitive = (_a2 = fallback === null || fallback === void 0 ? void 0 : fallback.caseSensitive) !== null && _a2 !== void 0 ? _a2 : state.facet(searchConfigFacet).caseSensitive;
    return fallback && !selText ? fallback : new SearchQuery({ search: selText.replace(/\n/g, "\\n"), caseSensitive });
  }
  var openSearchPanel = (view) => {
    let state = view.state.field(searchState, false);
    if (state && state.panel) {
      let panel = getPanel(view, createSearchPanel);
      if (!panel)
        return false;
      let searchInput = panel.dom.querySelector("[name=search]");
      if (searchInput != view.root.activeElement) {
        let query = defaultQuery(view.state, state.query.spec);
        if (query.valid)
          view.dispatch({ effects: setSearchQuery.of(query) });
        searchInput.focus();
        searchInput.select();
      }
    } else {
      view.dispatch({ effects: [
        togglePanel.of(true),
        state ? setSearchQuery.of(defaultQuery(view.state, state.query.spec)) : StateEffect.appendConfig.of(searchExtensions)
      ] });
    }
    return true;
  };
  var closeSearchPanel = (view) => {
    let state = view.state.field(searchState, false);
    if (!state || !state.panel)
      return false;
    let panel = getPanel(view, createSearchPanel);
    if (panel && panel.dom.contains(view.root.activeElement))
      view.focus();
    view.dispatch({ effects: togglePanel.of(false) });
    return true;
  };
  var searchKeymap = [
    { key: "Mod-f", run: openSearchPanel, scope: "editor search-panel" },
    { key: "F3", run: findNext, shift: findPrevious, scope: "editor search-panel", preventDefault: true },
    { key: "Mod-g", run: findNext, shift: findPrevious, scope: "editor search-panel", preventDefault: true },
    { key: "Escape", run: closeSearchPanel, scope: "editor search-panel" },
    { key: "Mod-Shift-l", run: selectSelectionMatches },
    { key: "Alt-g", run: gotoLine },
    { key: "Mod-d", run: selectNextOccurrence, preventDefault: true }
  ];
  var SearchPanel = class {
    constructor(view) {
      this.view = view;
      let query = this.query = view.state.field(searchState).query.spec;
      this.commit = this.commit.bind(this);
      this.searchField = crelt("input", {
        value: query.search,
        placeholder: phrase(view, "Find"),
        "aria-label": phrase(view, "Find"),
        class: "cm-textfield",
        name: "search",
        onchange: this.commit,
        onkeyup: this.commit
      });
      this.replaceField = crelt("input", {
        value: query.replace,
        placeholder: phrase(view, "Replace"),
        "aria-label": phrase(view, "Replace"),
        class: "cm-textfield",
        name: "replace",
        onchange: this.commit,
        onkeyup: this.commit
      });
      this.caseField = crelt("input", {
        type: "checkbox",
        name: "case",
        checked: query.caseSensitive,
        onchange: this.commit
      });
      this.reField = crelt("input", {
        type: "checkbox",
        name: "re",
        checked: query.regexp,
        onchange: this.commit
      });
      function button(name2, onclick, content2) {
        return crelt("button", { class: "cm-button", name: name2, onclick, type: "button" }, content2);
      }
      this.dom = crelt("div", { onkeydown: (e) => this.keydown(e), class: "cm-search" }, [
        this.searchField,
        button("next", () => findNext(view), [phrase(view, "next")]),
        button("prev", () => findPrevious(view), [phrase(view, "previous")]),
        button("select", () => selectMatches(view), [phrase(view, "all")]),
        crelt("label", null, [this.caseField, phrase(view, "match case")]),
        crelt("label", null, [this.reField, phrase(view, "regexp")]),
        ...view.state.readOnly ? [] : [
          crelt("br"),
          this.replaceField,
          button("replace", () => replaceNext(view), [phrase(view, "replace")]),
          button("replaceAll", () => replaceAll(view), [phrase(view, "replace all")]),
          crelt("button", {
            name: "close",
            onclick: () => closeSearchPanel(view),
            "aria-label": phrase(view, "close"),
            type: "button"
          }, ["\xD7"])
        ]
      ]);
    }
    commit() {
      let query = new SearchQuery({
        search: this.searchField.value,
        caseSensitive: this.caseField.checked,
        regexp: this.reField.checked,
        replace: this.replaceField.value
      });
      if (!query.eq(this.query)) {
        this.query = query;
        this.view.dispatch({ effects: setSearchQuery.of(query) });
      }
    }
    keydown(e) {
      if (runScopeHandlers(this.view, e, "search-panel")) {
        e.preventDefault();
      } else if (e.keyCode == 13 && e.target == this.searchField) {
        e.preventDefault();
        (e.shiftKey ? findPrevious : findNext)(this.view);
      } else if (e.keyCode == 13 && e.target == this.replaceField) {
        e.preventDefault();
        replaceNext(this.view);
      }
    }
    update(update) {
      for (let tr of update.transactions)
        for (let effect of tr.effects) {
          if (effect.is(setSearchQuery) && !effect.value.eq(this.query))
            this.setQuery(effect.value);
        }
    }
    setQuery(query) {
      this.query = query;
      this.searchField.value = query.search;
      this.replaceField.value = query.replace;
      this.caseField.checked = query.caseSensitive;
      this.reField.checked = query.regexp;
    }
    mount() {
      this.searchField.select();
    }
    get pos() {
      return 80;
    }
    get top() {
      return this.view.state.facet(searchConfigFacet).top;
    }
  };
  function phrase(view, phrase2) {
    return view.state.phrase(phrase2);
  }
  var AnnounceMargin = 30;
  var Break = /[\s\.,:;?!]/;
  function announceMatch(view, { from: from2, to }) {
    let lineStart = view.state.doc.lineAt(from2).from, lineEnd = view.state.doc.lineAt(to).to;
    let start = Math.max(lineStart, from2 - AnnounceMargin), end = Math.min(lineEnd, to + AnnounceMargin);
    let text = view.state.sliceDoc(start, end);
    if (start != lineStart) {
      for (let i = 0; i < AnnounceMargin; i++)
        if (!Break.test(text[i + 1]) && Break.test(text[i])) {
          text = text.slice(i);
          break;
        }
    }
    if (end != lineEnd) {
      for (let i = text.length - 1; i > text.length - AnnounceMargin; i--)
        if (!Break.test(text[i - 1]) && Break.test(text[i])) {
          text = text.slice(0, i);
          break;
        }
    }
    return EditorView.announce.of(`${view.state.phrase("current match")}. ${text} ${view.state.phrase("on line")} ${view.state.doc.lineAt(from2).number}`);
  }
  var baseTheme6 = /* @__PURE__ */ EditorView.baseTheme({
    ".cm-panel.cm-search": {
      padding: "2px 6px 4px",
      position: "relative",
      "& [name=close]": {
        position: "absolute",
        top: "0",
        right: "4px",
        backgroundColor: "inherit",
        border: "none",
        font: "inherit",
        padding: 0,
        margin: 0
      },
      "& input, & button, & label": {
        margin: ".2em .6em .2em 0"
      },
      "& input[type=checkbox]": {
        marginRight: ".2em"
      },
      "& label": {
        fontSize: "80%",
        whiteSpace: "pre"
      }
    },
    "&light .cm-searchMatch": { backgroundColor: "#ffff0054" },
    "&dark .cm-searchMatch": { backgroundColor: "#00ffff8a" },
    "&light .cm-searchMatch-selected": { backgroundColor: "#ff6a0054" },
    "&dark .cm-searchMatch-selected": { backgroundColor: "#ff00ff8a" }
  });
  var searchExtensions = [
    searchState,
    /* @__PURE__ */ Prec.lowest(searchHighlighter),
    baseTheme6
  ];

  // node_modules/@codemirror/tooltip/dist/index.js
  var ios2 = typeof navigator != "undefined" && !/* @__PURE__ */ /Edge\/(\d+)/.exec(navigator.userAgent) && /* @__PURE__ */ /Apple Computer/.test(navigator.vendor) && (/* @__PURE__ */ /Mobile\/\w+/.test(navigator.userAgent) || navigator.maxTouchPoints > 2);
  var Outside = "-10000px";
  var TooltipViewManager = class {
    constructor(view, facet, createTooltipView) {
      this.facet = facet;
      this.createTooltipView = createTooltipView;
      this.input = view.state.facet(facet);
      this.tooltips = this.input.filter((t2) => t2);
      this.tooltipViews = this.tooltips.map(createTooltipView);
    }
    update(update) {
      let input = update.state.facet(this.facet);
      let tooltips = input.filter((x) => x);
      if (input === this.input) {
        for (let t2 of this.tooltipViews)
          if (t2.update)
            t2.update(update);
        return false;
      }
      let tooltipViews = [];
      for (let i = 0; i < tooltips.length; i++) {
        let tip = tooltips[i], known = -1;
        if (!tip)
          continue;
        for (let i2 = 0; i2 < this.tooltips.length; i2++) {
          let other = this.tooltips[i2];
          if (other && other.create == tip.create)
            known = i2;
        }
        if (known < 0) {
          tooltipViews[i] = this.createTooltipView(tip);
        } else {
          let tooltipView = tooltipViews[i] = this.tooltipViews[known];
          if (tooltipView.update)
            tooltipView.update(update);
        }
      }
      for (let t2 of this.tooltipViews)
        if (tooltipViews.indexOf(t2) < 0)
          t2.dom.remove();
      this.input = input;
      this.tooltips = tooltips;
      this.tooltipViews = tooltipViews;
      return true;
    }
  };
  function windowSpace() {
    return { top: 0, left: 0, bottom: innerHeight, right: innerWidth };
  }
  var tooltipConfig = /* @__PURE__ */ Facet.define({
    combine: (values2) => {
      var _a2, _b, _c;
      return {
        position: ios2 ? "absolute" : ((_a2 = values2.find((conf) => conf.position)) === null || _a2 === void 0 ? void 0 : _a2.position) || "fixed",
        parent: ((_b = values2.find((conf) => conf.parent)) === null || _b === void 0 ? void 0 : _b.parent) || null,
        tooltipSpace: ((_c = values2.find((conf) => conf.tooltipSpace)) === null || _c === void 0 ? void 0 : _c.tooltipSpace) || windowSpace
      };
    }
  });
  var tooltipPlugin = /* @__PURE__ */ ViewPlugin.fromClass(class {
    constructor(view) {
      var _a2;
      this.view = view;
      this.inView = true;
      this.lastTransaction = 0;
      this.measureTimeout = -1;
      let config2 = view.state.facet(tooltipConfig);
      this.position = config2.position;
      this.parent = config2.parent;
      this.classes = view.themeClasses;
      this.createContainer();
      this.measureReq = { read: this.readMeasure.bind(this), write: this.writeMeasure.bind(this), key: this };
      this.manager = new TooltipViewManager(view, showTooltip, (t2) => this.createTooltip(t2));
      this.intersectionObserver = typeof IntersectionObserver == "function" ? new IntersectionObserver((entries) => {
        if (Date.now() > this.lastTransaction - 50 && entries.length > 0 && entries[entries.length - 1].intersectionRatio < 1)
          this.measureSoon();
      }, { threshold: [1] }) : null;
      this.observeIntersection();
      (_a2 = view.dom.ownerDocument.defaultView) === null || _a2 === void 0 ? void 0 : _a2.addEventListener("resize", this.measureSoon = this.measureSoon.bind(this));
      this.maybeMeasure();
    }
    createContainer() {
      if (this.parent) {
        this.container = document.createElement("div");
        this.container.style.position = "relative";
        this.container.className = this.view.themeClasses;
        this.parent.appendChild(this.container);
      } else {
        this.container = this.view.dom;
      }
    }
    observeIntersection() {
      if (this.intersectionObserver) {
        this.intersectionObserver.disconnect();
        for (let tooltip of this.manager.tooltipViews)
          this.intersectionObserver.observe(tooltip.dom);
      }
    }
    measureSoon() {
      if (this.measureTimeout < 0)
        this.measureTimeout = setTimeout(() => {
          this.measureTimeout = -1;
          this.maybeMeasure();
        }, 50);
    }
    update(update) {
      if (update.transactions.length)
        this.lastTransaction = Date.now();
      let updated = this.manager.update(update);
      if (updated)
        this.observeIntersection();
      let shouldMeasure = updated || update.geometryChanged;
      let newConfig = update.state.facet(tooltipConfig);
      if (newConfig.position != this.position) {
        this.position = newConfig.position;
        for (let t2 of this.manager.tooltipViews)
          t2.dom.style.position = this.position;
        shouldMeasure = true;
      }
      if (newConfig.parent != this.parent) {
        if (this.parent)
          this.container.remove();
        this.parent = newConfig.parent;
        this.createContainer();
        for (let t2 of this.manager.tooltipViews)
          this.container.appendChild(t2.dom);
        shouldMeasure = true;
      } else if (this.parent && this.view.themeClasses != this.classes) {
        this.classes = this.container.className = this.view.themeClasses;
      }
      if (shouldMeasure)
        this.maybeMeasure();
    }
    createTooltip(tooltip) {
      let tooltipView = tooltip.create(this.view);
      tooltipView.dom.classList.add("cm-tooltip");
      if (tooltip.arrow && !tooltipView.dom.querySelector(".cm-tooltip > .cm-tooltip-arrow")) {
        let arrow = document.createElement("div");
        arrow.className = "cm-tooltip-arrow";
        tooltipView.dom.appendChild(arrow);
      }
      tooltipView.dom.style.position = this.position;
      tooltipView.dom.style.top = Outside;
      this.container.appendChild(tooltipView.dom);
      if (tooltipView.mount)
        tooltipView.mount(this.view);
      return tooltipView;
    }
    destroy() {
      var _a2, _b;
      (_a2 = this.view.dom.ownerDocument.defaultView) === null || _a2 === void 0 ? void 0 : _a2.removeEventListener("resize", this.measureSoon);
      for (let { dom } of this.manager.tooltipViews)
        dom.remove();
      (_b = this.intersectionObserver) === null || _b === void 0 ? void 0 : _b.disconnect();
      clearTimeout(this.measureTimeout);
    }
    readMeasure() {
      let editor = this.view.dom.getBoundingClientRect();
      return {
        editor,
        parent: this.parent ? this.container.getBoundingClientRect() : editor,
        pos: this.manager.tooltips.map((t2, i) => {
          let tv = this.manager.tooltipViews[i];
          return tv.getCoords ? tv.getCoords(t2.pos) : this.view.coordsAtPos(t2.pos);
        }),
        size: this.manager.tooltipViews.map(({ dom }) => dom.getBoundingClientRect()),
        space: this.view.state.facet(tooltipConfig).tooltipSpace(this.view)
      };
    }
    writeMeasure(measured) {
      let { editor, space: space3 } = measured;
      let others = [];
      for (let i = 0; i < this.manager.tooltips.length; i++) {
        let tooltip = this.manager.tooltips[i], tView = this.manager.tooltipViews[i], { dom } = tView;
        let pos = measured.pos[i], size = measured.size[i];
        if (!pos || pos.bottom <= Math.max(editor.top, space3.top) || pos.top >= Math.min(editor.bottom, space3.bottom) || pos.right < Math.max(editor.left, space3.left) - 0.1 || pos.left > Math.min(editor.right, space3.right) + 0.1) {
          dom.style.top = Outside;
          continue;
        }
        let arrow = tooltip.arrow ? tView.dom.querySelector(".cm-tooltip-arrow") : null;
        let arrowHeight = arrow ? 7 : 0;
        let width = size.right - size.left, height = size.bottom - size.top;
        let offset = tView.offset || noOffset, ltr = this.view.textDirection == Direction.LTR;
        let left = size.width > space3.right - space3.left ? ltr ? space3.left : space3.right - size.width : ltr ? Math.min(pos.left - (arrow ? 14 : 0) + offset.x, space3.right - width) : Math.max(space3.left, pos.left - width + (arrow ? 14 : 0) - offset.x);
        let above = !!tooltip.above;
        if (!tooltip.strictSide && (above ? pos.top - (size.bottom - size.top) - offset.y < space3.top : pos.bottom + (size.bottom - size.top) + offset.y > space3.bottom) && above == space3.bottom - pos.bottom > pos.top - space3.top)
          above = !above;
        let top2 = above ? pos.top - height - arrowHeight - offset.y : pos.bottom + arrowHeight + offset.y;
        let right = left + width;
        if (tView.overlap !== true) {
          for (let r of others)
            if (r.left < right && r.right > left && r.top < top2 + height && r.bottom > top2)
              top2 = above ? r.top - height - 2 - arrowHeight : r.bottom + arrowHeight + 2;
        }
        if (this.position == "absolute") {
          dom.style.top = top2 - measured.parent.top + "px";
          dom.style.left = left - measured.parent.left + "px";
        } else {
          dom.style.top = top2 + "px";
          dom.style.left = left + "px";
        }
        if (arrow)
          arrow.style.left = `${pos.left + (ltr ? offset.x : -offset.x) - (left + 14 - 7)}px`;
        if (tView.overlap !== true)
          others.push({ left, top: top2, right, bottom: top2 + height });
        dom.classList.toggle("cm-tooltip-above", above);
        dom.classList.toggle("cm-tooltip-below", !above);
        if (tView.positioned)
          tView.positioned();
      }
    }
    maybeMeasure() {
      if (this.manager.tooltips.length) {
        if (this.view.inView)
          this.view.requestMeasure(this.measureReq);
        if (this.inView != this.view.inView) {
          this.inView = this.view.inView;
          if (!this.inView)
            for (let tv of this.manager.tooltipViews)
              tv.dom.style.top = Outside;
        }
      }
    }
  }, {
    eventHandlers: {
      scroll() {
        this.maybeMeasure();
      }
    }
  });
  var baseTheme7 = /* @__PURE__ */ EditorView.baseTheme({
    ".cm-tooltip": {
      zIndex: 100
    },
    "&light .cm-tooltip": {
      border: "1px solid #bbb",
      backgroundColor: "#f5f5f5"
    },
    "&light .cm-tooltip-section:not(:first-child)": {
      borderTop: "1px solid #bbb"
    },
    "&dark .cm-tooltip": {
      backgroundColor: "#333338",
      color: "white"
    },
    ".cm-tooltip-arrow": {
      height: `${7}px`,
      width: `${7 * 2}px`,
      position: "absolute",
      zIndex: -1,
      overflow: "hidden",
      "&:before, &:after": {
        content: "''",
        position: "absolute",
        width: 0,
        height: 0,
        borderLeft: `${7}px solid transparent`,
        borderRight: `${7}px solid transparent`
      },
      ".cm-tooltip-above &": {
        bottom: `-${7}px`,
        "&:before": {
          borderTop: `${7}px solid #bbb`
        },
        "&:after": {
          borderTop: `${7}px solid #f5f5f5`,
          bottom: "1px"
        }
      },
      ".cm-tooltip-below &": {
        top: `-${7}px`,
        "&:before": {
          borderBottom: `${7}px solid #bbb`
        },
        "&:after": {
          borderBottom: `${7}px solid #f5f5f5`,
          top: "1px"
        }
      }
    },
    "&dark .cm-tooltip .cm-tooltip-arrow": {
      "&:before": {
        borderTopColor: "#333338",
        borderBottomColor: "#333338"
      },
      "&:after": {
        borderTopColor: "transparent",
        borderBottomColor: "transparent"
      }
    }
  });
  var noOffset = { x: 0, y: 0 };
  var showTooltip = /* @__PURE__ */ Facet.define({
    enables: [tooltipPlugin, baseTheme7]
  });
  var showHoverTooltip = /* @__PURE__ */ Facet.define();
  var HoverTooltipHost = class {
    constructor(view) {
      this.view = view;
      this.mounted = false;
      this.dom = document.createElement("div");
      this.dom.classList.add("cm-tooltip-hover");
      this.manager = new TooltipViewManager(view, showHoverTooltip, (t2) => this.createHostedView(t2));
    }
    static create(view) {
      return new HoverTooltipHost(view);
    }
    createHostedView(tooltip) {
      let hostedView = tooltip.create(this.view);
      hostedView.dom.classList.add("cm-tooltip-section");
      this.dom.appendChild(hostedView.dom);
      if (this.mounted && hostedView.mount)
        hostedView.mount(this.view);
      return hostedView;
    }
    mount(view) {
      for (let hostedView of this.manager.tooltipViews) {
        if (hostedView.mount)
          hostedView.mount(view);
      }
      this.mounted = true;
    }
    positioned() {
      for (let hostedView of this.manager.tooltipViews) {
        if (hostedView.positioned)
          hostedView.positioned();
      }
    }
    update(update) {
      this.manager.update(update);
    }
  };
  var showHoverTooltipHost = /* @__PURE__ */ showTooltip.compute([showHoverTooltip], (state) => {
    let tooltips = state.facet(showHoverTooltip).filter((t2) => t2);
    if (tooltips.length === 0)
      return null;
    return {
      pos: Math.min(...tooltips.map((t2) => t2.pos)),
      end: Math.max(...tooltips.filter((t2) => t2.end != null).map((t2) => t2.end)),
      create: HoverTooltipHost.create,
      above: tooltips[0].above,
      arrow: tooltips.some((t2) => t2.arrow)
    };
  });
  var HoverPlugin = class {
    constructor(view, source, field, setHover, hoverTime) {
      this.view = view;
      this.source = source;
      this.field = field;
      this.setHover = setHover;
      this.hoverTime = hoverTime;
      this.hoverTimeout = -1;
      this.restartTimeout = -1;
      this.pending = null;
      this.lastMove = { x: 0, y: 0, target: view.dom, time: 0 };
      this.checkHover = this.checkHover.bind(this);
      view.dom.addEventListener("mouseleave", this.mouseleave = this.mouseleave.bind(this));
      view.dom.addEventListener("mousemove", this.mousemove = this.mousemove.bind(this));
    }
    update() {
      if (this.pending) {
        this.pending = null;
        clearTimeout(this.restartTimeout);
        this.restartTimeout = setTimeout(() => this.startHover(), 20);
      }
    }
    get active() {
      return this.view.state.field(this.field);
    }
    checkHover() {
      this.hoverTimeout = -1;
      if (this.active)
        return;
      let hovered = Date.now() - this.lastMove.time;
      if (hovered < this.hoverTime)
        this.hoverTimeout = setTimeout(this.checkHover, this.hoverTime - hovered);
      else
        this.startHover();
    }
    startHover() {
      var _a2;
      clearTimeout(this.restartTimeout);
      let { lastMove } = this;
      let pos = this.view.contentDOM.contains(lastMove.target) ? this.view.posAtCoords(lastMove) : null;
      if (pos == null)
        return;
      let posCoords = this.view.coordsAtPos(pos);
      if (posCoords == null || lastMove.y < posCoords.top || lastMove.y > posCoords.bottom || lastMove.x < posCoords.left - this.view.defaultCharacterWidth || lastMove.x > posCoords.right + this.view.defaultCharacterWidth)
        return;
      let bidi = this.view.bidiSpans(this.view.state.doc.lineAt(pos)).find((s) => s.from <= pos && s.to >= pos);
      let rtl = bidi && bidi.dir == Direction.RTL ? -1 : 1;
      let open = this.source(this.view, pos, lastMove.x < posCoords.left ? -rtl : rtl);
      if ((_a2 = open) === null || _a2 === void 0 ? void 0 : _a2.then) {
        let pending = this.pending = { pos };
        open.then((result) => {
          if (this.pending == pending) {
            this.pending = null;
            if (result)
              this.view.dispatch({ effects: this.setHover.of(result) });
          }
        }, (e) => logException(this.view.state, e, "hover tooltip"));
      } else if (open) {
        this.view.dispatch({ effects: this.setHover.of(open) });
      }
    }
    mousemove(event) {
      var _a2;
      this.lastMove = { x: event.clientX, y: event.clientY, target: event.target, time: Date.now() };
      if (this.hoverTimeout < 0)
        this.hoverTimeout = setTimeout(this.checkHover, this.hoverTime);
      let tooltip = this.active;
      if (tooltip && !isInTooltip(this.lastMove.target) || this.pending) {
        let { pos } = tooltip || this.pending, end = (_a2 = tooltip === null || tooltip === void 0 ? void 0 : tooltip.end) !== null && _a2 !== void 0 ? _a2 : pos;
        if (pos == end ? this.view.posAtCoords(this.lastMove) != pos : !isOverRange(this.view, pos, end, event.clientX, event.clientY, 6)) {
          this.view.dispatch({ effects: this.setHover.of(null) });
          this.pending = null;
        }
      }
    }
    mouseleave() {
      clearTimeout(this.hoverTimeout);
      this.hoverTimeout = -1;
      if (this.active)
        this.view.dispatch({ effects: this.setHover.of(null) });
    }
    destroy() {
      clearTimeout(this.hoverTimeout);
      this.view.dom.removeEventListener("mouseleave", this.mouseleave);
      this.view.dom.removeEventListener("mousemove", this.mousemove);
    }
  };
  function isInTooltip(elt) {
    for (let cur2 = elt; cur2; cur2 = cur2.parentNode)
      if (cur2.nodeType == 1 && cur2.classList.contains("cm-tooltip"))
        return true;
    return false;
  }
  function isOverRange(view, from2, to, x, y, margin) {
    let range = document.createRange();
    let fromDOM = view.domAtPos(from2), toDOM = view.domAtPos(to);
    range.setEnd(toDOM.node, toDOM.offset);
    range.setStart(fromDOM.node, fromDOM.offset);
    let rects = range.getClientRects();
    range.detach();
    for (let i = 0; i < rects.length; i++) {
      let rect = rects[i];
      let dist = Math.max(rect.top - y, y - rect.bottom, rect.left - x, x - rect.right);
      if (dist <= margin)
        return true;
    }
    return false;
  }
  function hoverTooltip(source, options = {}) {
    let setHover = StateEffect.define();
    let hoverState = StateField.define({
      create() {
        return null;
      },
      update(value, tr) {
        if (value && (options.hideOnChange && (tr.docChanged || tr.selection)))
          return null;
        for (let effect of tr.effects) {
          if (effect.is(setHover))
            return effect.value;
          if (effect.is(closeHoverTooltipEffect))
            return null;
        }
        if (value && tr.docChanged) {
          let newPos = tr.changes.mapPos(value.pos, -1, MapMode.TrackDel);
          if (newPos == null)
            return null;
          let copy = Object.assign(/* @__PURE__ */ Object.create(null), value);
          copy.pos = newPos;
          if (value.end != null)
            copy.end = tr.changes.mapPos(value.end);
          return copy;
        }
        return value;
      },
      provide: (f) => showHoverTooltip.from(f)
    });
    return [
      hoverState,
      ViewPlugin.define((view) => new HoverPlugin(view, source, hoverState, setHover, options.hoverTime || 300)),
      showHoverTooltipHost
    ];
  }
  function getTooltip(view, tooltip) {
    let plugin = view.plugin(tooltipPlugin);
    if (!plugin)
      return null;
    let found = plugin.manager.tooltips.indexOf(tooltip);
    return found < 0 ? null : plugin.manager.tooltipViews[found];
  }
  var closeHoverTooltipEffect = /* @__PURE__ */ StateEffect.define();

  // node_modules/@codemirror/autocomplete/dist/index.js
  var CompletionContext = class {
    constructor(state, pos, explicit) {
      this.state = state;
      this.pos = pos;
      this.explicit = explicit;
      this.abortListeners = [];
    }
    tokenBefore(types2) {
      let token = syntaxTree(this.state).resolveInner(this.pos, -1);
      while (token && types2.indexOf(token.name) < 0)
        token = token.parent;
      return token ? {
        from: token.from,
        to: this.pos,
        text: this.state.sliceDoc(token.from, this.pos),
        type: token.type
      } : null;
    }
    matchBefore(expr) {
      let line = this.state.doc.lineAt(this.pos);
      let start = Math.max(line.from, this.pos - 250);
      let str = line.text.slice(start - line.from, this.pos - line.from);
      let found = str.search(ensureAnchor(expr, false));
      return found < 0 ? null : { from: start + found, to: this.pos, text: str.slice(found) };
    }
    get aborted() {
      return this.abortListeners == null;
    }
    addEventListener(type, listener) {
      if (type == "abort" && this.abortListeners)
        this.abortListeners.push(listener);
    }
  };
  function toSet(chars) {
    let flat = Object.keys(chars).join("");
    let words = /\w/.test(flat);
    if (words)
      flat = flat.replace(/\w/g, "");
    return `[${words ? "\\w" : ""}${flat.replace(/[^\w\s]/g, "\\$&")}]`;
  }
  function prefixMatch(options) {
    let first = /* @__PURE__ */ Object.create(null), rest = /* @__PURE__ */ Object.create(null);
    for (let { label } of options) {
      first[label[0]] = true;
      for (let i = 1; i < label.length; i++)
        rest[label[i]] = true;
    }
    let source = toSet(first) + toSet(rest) + "*$";
    return [new RegExp("^" + source), new RegExp(source)];
  }
  function completeFromList(list2) {
    let options = list2.map((o) => typeof o == "string" ? { label: o } : o);
    let [span2, match2] = options.every((o) => /^\w+$/.test(o.label)) ? [/\w*$/, /\w+$/] : prefixMatch(options);
    return (context) => {
      let token = context.matchBefore(match2);
      return token || context.explicit ? { from: token ? token.from : context.pos, options, span: span2 } : null;
    };
  }
  function ifNotIn(nodes, source) {
    return (context) => {
      for (let pos = syntaxTree(context.state).resolveInner(context.pos, -1); pos; pos = pos.parent)
        if (nodes.indexOf(pos.name) > -1)
          return null;
      return source(context);
    };
  }
  var Option = class {
    constructor(completion, source, match2) {
      this.completion = completion;
      this.source = source;
      this.match = match2;
    }
  };
  function cur(state) {
    return state.selection.main.head;
  }
  function ensureAnchor(expr, start) {
    var _a2;
    let { source } = expr;
    let addStart = start && source[0] != "^", addEnd = source[source.length - 1] != "$";
    if (!addStart && !addEnd)
      return expr;
    return new RegExp(`${addStart ? "^" : ""}(?:${source})${addEnd ? "$" : ""}`, (_a2 = expr.flags) !== null && _a2 !== void 0 ? _a2 : expr.ignoreCase ? "i" : "");
  }
  var pickedCompletion = /* @__PURE__ */ Annotation.define();
  function applyCompletion(view, option) {
    let apply = option.completion.apply || option.completion.label;
    let result = option.source;
    if (typeof apply == "string") {
      view.dispatch({
        changes: { from: result.from, to: result.to, insert: apply },
        selection: { anchor: result.from + apply.length },
        userEvent: "input.complete",
        annotations: pickedCompletion.of(option.completion)
      });
    } else {
      apply(view, option.completion, result.from, result.to);
    }
  }
  var SourceCache = /* @__PURE__ */ new WeakMap();
  function asSource(source) {
    if (!Array.isArray(source))
      return source;
    let known = SourceCache.get(source);
    if (!known)
      SourceCache.set(source, known = completeFromList(source));
    return known;
  }
  var FuzzyMatcher = class {
    constructor(pattern) {
      this.pattern = pattern;
      this.chars = [];
      this.folded = [];
      this.any = [];
      this.precise = [];
      this.byWord = [];
      for (let p = 0; p < pattern.length; ) {
        let char = codePointAt(pattern, p), size = codePointSize(char);
        this.chars.push(char);
        let part = pattern.slice(p, p + size), upper = part.toUpperCase();
        this.folded.push(codePointAt(upper == part ? part.toLowerCase() : upper, 0));
        p += size;
      }
      this.astral = pattern.length != this.chars.length;
    }
    match(word) {
      if (this.pattern.length == 0)
        return [0];
      if (word.length < this.pattern.length)
        return null;
      let { chars, folded, any, precise, byWord } = this;
      if (chars.length == 1) {
        let first = codePointAt(word, 0);
        return first == chars[0] ? [0, 0, codePointSize(first)] : first == folded[0] ? [-200, 0, codePointSize(first)] : null;
      }
      let direct = word.indexOf(this.pattern);
      if (direct == 0)
        return [0, 0, this.pattern.length];
      let len = chars.length, anyTo = 0;
      if (direct < 0) {
        for (let i = 0, e = Math.min(word.length, 200); i < e && anyTo < len; ) {
          let next = codePointAt(word, i);
          if (next == chars[anyTo] || next == folded[anyTo])
            any[anyTo++] = i;
          i += codePointSize(next);
        }
        if (anyTo < len)
          return null;
      }
      let preciseTo = 0;
      let byWordTo = 0, byWordFolded = false;
      let adjacentTo = 0, adjacentStart = -1, adjacentEnd = -1;
      let hasLower = /[a-z]/.test(word), wordAdjacent = true;
      for (let i = 0, e = Math.min(word.length, 200), prevType = 0; i < e && byWordTo < len; ) {
        let next = codePointAt(word, i);
        if (direct < 0) {
          if (preciseTo < len && next == chars[preciseTo])
            precise[preciseTo++] = i;
          if (adjacentTo < len) {
            if (next == chars[adjacentTo] || next == folded[adjacentTo]) {
              if (adjacentTo == 0)
                adjacentStart = i;
              adjacentEnd = i + 1;
              adjacentTo++;
            } else {
              adjacentTo = 0;
            }
          }
        }
        let ch, type = next < 255 ? next >= 48 && next <= 57 || next >= 97 && next <= 122 ? 2 : next >= 65 && next <= 90 ? 1 : 0 : (ch = fromCodePoint(next)) != ch.toLowerCase() ? 1 : ch != ch.toUpperCase() ? 2 : 0;
        if (!i || type == 1 && hasLower || prevType == 0 && type != 0) {
          if (chars[byWordTo] == next || folded[byWordTo] == next && (byWordFolded = true))
            byWord[byWordTo++] = i;
          else if (byWord.length)
            wordAdjacent = false;
        }
        prevType = type;
        i += codePointSize(next);
      }
      if (byWordTo == len && byWord[0] == 0 && wordAdjacent)
        return this.result(-100 + (byWordFolded ? -200 : 0), byWord, word);
      if (adjacentTo == len && adjacentStart == 0)
        return [-200 - word.length, 0, adjacentEnd];
      if (direct > -1)
        return [-700 - word.length, direct, direct + this.pattern.length];
      if (adjacentTo == len)
        return [-200 + -700 - word.length, adjacentStart, adjacentEnd];
      if (byWordTo == len)
        return this.result(-100 + (byWordFolded ? -200 : 0) + -700 + (wordAdjacent ? 0 : -1100), byWord, word);
      return chars.length == 2 ? null : this.result((any[0] ? -700 : 0) + -200 + -1100, any, word);
    }
    result(score2, positions, word) {
      let result = [score2 - word.length], i = 1;
      for (let pos of positions) {
        let to = pos + (this.astral ? codePointSize(codePointAt(word, pos)) : 1);
        if (i > 1 && result[i - 1] == pos)
          result[i - 1] = to;
        else {
          result[i++] = pos;
          result[i++] = to;
        }
      }
      return result;
    }
  };
  var completionConfig = /* @__PURE__ */ Facet.define({
    combine(configs) {
      return combineConfig(configs, {
        activateOnTyping: true,
        override: null,
        maxRenderedOptions: 100,
        defaultKeymap: true,
        optionClass: () => "",
        aboveCursor: false,
        icons: true,
        addToOptions: []
      }, {
        defaultKeymap: (a, b) => a && b,
        icons: (a, b) => a && b,
        optionClass: (a, b) => (c) => joinClass(a(c), b(c)),
        addToOptions: (a, b) => a.concat(b)
      });
    }
  });
  function joinClass(a, b) {
    return a ? b ? a + " " + b : a : b;
  }
  function optionContent(config2) {
    let content2 = config2.addToOptions.slice();
    if (config2.icons)
      content2.push({
        render(completion) {
          let icon = document.createElement("div");
          icon.classList.add("cm-completionIcon");
          if (completion.type)
            icon.classList.add(...completion.type.split(/\s+/g).map((cls) => "cm-completionIcon-" + cls));
          icon.setAttribute("aria-hidden", "true");
          return icon;
        },
        position: 20
      });
    content2.push({
      render(completion, _s, match2) {
        let labelElt = document.createElement("span");
        labelElt.className = "cm-completionLabel";
        let { label } = completion, off = 0;
        for (let j = 1; j < match2.length; ) {
          let from2 = match2[j++], to = match2[j++];
          if (from2 > off)
            labelElt.appendChild(document.createTextNode(label.slice(off, from2)));
          let span2 = labelElt.appendChild(document.createElement("span"));
          span2.appendChild(document.createTextNode(label.slice(from2, to)));
          span2.className = "cm-completionMatchedText";
          off = to;
        }
        if (off < label.length)
          labelElt.appendChild(document.createTextNode(label.slice(off)));
        return labelElt;
      },
      position: 50
    }, {
      render(completion) {
        if (!completion.detail)
          return null;
        let detailElt = document.createElement("span");
        detailElt.className = "cm-completionDetail";
        detailElt.textContent = completion.detail;
        return detailElt;
      },
      position: 80
    });
    return content2.sort((a, b) => a.position - b.position).map((a) => a.render);
  }
  function rangeAroundSelected(total, selected, max) {
    if (total <= max)
      return { from: 0, to: total };
    if (selected <= total >> 1) {
      let off2 = Math.floor(selected / max);
      return { from: off2 * max, to: (off2 + 1) * max };
    }
    let off = Math.floor((total - selected) / max);
    return { from: total - (off + 1) * max, to: total - off * max };
  }
  var CompletionTooltip = class {
    constructor(view, stateField) {
      this.view = view;
      this.stateField = stateField;
      this.info = null;
      this.placeInfo = {
        read: () => this.measureInfo(),
        write: (pos) => this.positionInfo(pos),
        key: this
      };
      let cState = view.state.field(stateField);
      let { options, selected } = cState.open;
      let config2 = view.state.facet(completionConfig);
      this.optionContent = optionContent(config2);
      this.optionClass = config2.optionClass;
      this.range = rangeAroundSelected(options.length, selected, config2.maxRenderedOptions);
      this.dom = document.createElement("div");
      this.dom.className = "cm-tooltip-autocomplete";
      this.dom.addEventListener("mousedown", (e) => {
        for (let dom = e.target, match2; dom && dom != this.dom; dom = dom.parentNode) {
          if (dom.nodeName == "LI" && (match2 = /-(\d+)$/.exec(dom.id)) && +match2[1] < options.length) {
            applyCompletion(view, options[+match2[1]]);
            e.preventDefault();
            return;
          }
        }
      });
      this.list = this.dom.appendChild(this.createListBox(options, cState.id, this.range));
      this.list.addEventListener("scroll", () => {
        if (this.info)
          this.view.requestMeasure(this.placeInfo);
      });
    }
    mount() {
      this.updateSel();
    }
    update(update) {
      if (update.state.field(this.stateField) != update.startState.field(this.stateField))
        this.updateSel();
    }
    positioned() {
      if (this.info)
        this.view.requestMeasure(this.placeInfo);
    }
    updateSel() {
      let cState = this.view.state.field(this.stateField), open = cState.open;
      if (open.selected < this.range.from || open.selected >= this.range.to) {
        this.range = rangeAroundSelected(open.options.length, open.selected, this.view.state.facet(completionConfig).maxRenderedOptions);
        this.list.remove();
        this.list = this.dom.appendChild(this.createListBox(open.options, cState.id, this.range));
        this.list.addEventListener("scroll", () => {
          if (this.info)
            this.view.requestMeasure(this.placeInfo);
        });
      }
      if (this.updateSelectedOption(open.selected)) {
        if (this.info) {
          this.info.remove();
          this.info = null;
        }
        let { completion } = open.options[open.selected];
        let { info } = completion;
        if (!info)
          return;
        let infoResult = typeof info === "string" ? document.createTextNode(info) : info(completion);
        if (!infoResult)
          return;
        if ("then" in infoResult) {
          infoResult.then((node) => {
            if (node && this.view.state.field(this.stateField, false) == cState)
              this.addInfoPane(node);
          }).catch((e) => logException(this.view.state, e, "completion info"));
        } else {
          this.addInfoPane(infoResult);
        }
      }
    }
    addInfoPane(content2) {
      let dom = this.info = document.createElement("div");
      dom.className = "cm-tooltip cm-completionInfo";
      dom.appendChild(content2);
      this.dom.appendChild(dom);
      this.view.requestMeasure(this.placeInfo);
    }
    updateSelectedOption(selected) {
      let set = null;
      for (let opt = this.list.firstChild, i = this.range.from; opt; opt = opt.nextSibling, i++) {
        if (i == selected) {
          if (!opt.hasAttribute("aria-selected")) {
            opt.setAttribute("aria-selected", "true");
            set = opt;
          }
        } else {
          if (opt.hasAttribute("aria-selected"))
            opt.removeAttribute("aria-selected");
        }
      }
      if (set)
        scrollIntoView2(this.list, set);
      return set;
    }
    measureInfo() {
      let sel = this.dom.querySelector("[aria-selected]");
      if (!sel || !this.info)
        return null;
      let listRect = this.dom.getBoundingClientRect();
      let infoRect = this.info.getBoundingClientRect();
      let selRect = sel.getBoundingClientRect();
      if (selRect.top > Math.min(innerHeight, listRect.bottom) - 10 || selRect.bottom < Math.max(0, listRect.top) + 10)
        return null;
      let top2 = Math.max(0, Math.min(selRect.top, innerHeight - infoRect.height)) - listRect.top;
      let left = this.view.textDirection == Direction.RTL;
      let spaceLeft = listRect.left, spaceRight = innerWidth - listRect.right;
      if (left && spaceLeft < Math.min(infoRect.width, spaceRight))
        left = false;
      else if (!left && spaceRight < Math.min(infoRect.width, spaceLeft))
        left = true;
      return { top: top2, left };
    }
    positionInfo(pos) {
      if (this.info) {
        this.info.style.top = (pos ? pos.top : -1e6) + "px";
        if (pos) {
          this.info.classList.toggle("cm-completionInfo-left", pos.left);
          this.info.classList.toggle("cm-completionInfo-right", !pos.left);
        }
      }
    }
    createListBox(options, id2, range) {
      const ul = document.createElement("ul");
      ul.id = id2;
      ul.setAttribute("role", "listbox");
      ul.setAttribute("aria-expanded", "true");
      for (let i = range.from; i < range.to; i++) {
        let { completion, match: match2 } = options[i];
        const li = ul.appendChild(document.createElement("li"));
        li.id = id2 + "-" + i;
        li.setAttribute("role", "option");
        let cls = this.optionClass(completion);
        if (cls)
          li.className = cls;
        for (let source of this.optionContent) {
          let node = source(completion, this.view.state, match2);
          if (node)
            li.appendChild(node);
        }
      }
      if (range.from)
        ul.classList.add("cm-completionListIncompleteTop");
      if (range.to < options.length)
        ul.classList.add("cm-completionListIncompleteBottom");
      return ul;
    }
  };
  function completionTooltip(stateField) {
    return (view) => new CompletionTooltip(view, stateField);
  }
  function scrollIntoView2(container, element) {
    let parent = container.getBoundingClientRect();
    let self = element.getBoundingClientRect();
    if (self.top < parent.top)
      container.scrollTop -= parent.top - self.top;
    else if (self.bottom > parent.bottom)
      container.scrollTop += self.bottom - parent.bottom;
  }
  var MaxOptions = 300;
  function score(option) {
    return (option.boost || 0) * 100 + (option.apply ? 10 : 0) + (option.info ? 5 : 0) + (option.type ? 1 : 0);
  }
  function sortOptions(active, state) {
    let options = [], i = 0;
    for (let a of active)
      if (a.hasResult()) {
        if (a.result.filter === false) {
          for (let option of a.result.options)
            options.push(new Option(option, a, [1e9 - i++]));
        } else {
          let matcher = new FuzzyMatcher(state.sliceDoc(a.from, a.to)), match2;
          for (let option of a.result.options)
            if (match2 = matcher.match(option.label)) {
              if (option.boost != null)
                match2[0] += option.boost;
              options.push(new Option(option, a, match2));
            }
        }
      }
    let result = [], prev = null;
    for (let opt of options.sort(cmpOption)) {
      if (result.length == MaxOptions)
        break;
      if (!prev || prev.label != opt.completion.label || prev.detail != opt.completion.detail || prev.type != opt.completion.type || prev.apply != opt.completion.apply)
        result.push(opt);
      else if (score(opt.completion) > score(prev))
        result[result.length - 1] = opt;
      prev = opt.completion;
    }
    return result;
  }
  var CompletionDialog = class {
    constructor(options, attrs, tooltip, timestamp, selected) {
      this.options = options;
      this.attrs = attrs;
      this.tooltip = tooltip;
      this.timestamp = timestamp;
      this.selected = selected;
    }
    setSelected(selected, id2) {
      return selected == this.selected || selected >= this.options.length ? this : new CompletionDialog(this.options, makeAttrs(id2, selected), this.tooltip, this.timestamp, selected);
    }
    static build(active, state, id2, prev, conf) {
      let options = sortOptions(active, state);
      if (!options.length)
        return null;
      let selected = 0;
      if (prev && prev.selected) {
        let selectedValue = prev.options[prev.selected].completion;
        for (let i = 0; i < options.length; i++)
          if (options[i].completion == selectedValue) {
            selected = i;
            break;
          }
      }
      return new CompletionDialog(options, makeAttrs(id2, selected), {
        pos: active.reduce((a, b) => b.hasResult() ? Math.min(a, b.from) : a, 1e8),
        create: completionTooltip(completionState),
        above: conf.aboveCursor
      }, prev ? prev.timestamp : Date.now(), selected);
    }
    map(changes) {
      return new CompletionDialog(this.options, this.attrs, Object.assign(Object.assign({}, this.tooltip), { pos: changes.mapPos(this.tooltip.pos) }), this.timestamp, this.selected);
    }
  };
  var CompletionState = class {
    constructor(active, id2, open) {
      this.active = active;
      this.id = id2;
      this.open = open;
    }
    static start() {
      return new CompletionState(none4, "cm-ac-" + Math.floor(Math.random() * 2e6).toString(36), null);
    }
    update(tr) {
      let { state } = tr, conf = state.facet(completionConfig);
      let sources = conf.override || state.languageDataAt("autocomplete", cur(state)).map(asSource);
      let active = sources.map((source) => {
        let value = this.active.find((s) => s.source == source) || new ActiveSource(source, this.active.some((a) => a.state != 0) ? 1 : 0);
        return value.update(tr, conf);
      });
      if (active.length == this.active.length && active.every((a, i) => a == this.active[i]))
        active = this.active;
      let open = tr.selection || active.some((a) => a.hasResult() && tr.changes.touchesRange(a.from, a.to)) || !sameResults(active, this.active) ? CompletionDialog.build(active, state, this.id, this.open, conf) : this.open && tr.docChanged ? this.open.map(tr.changes) : this.open;
      if (!open && active.every((a) => a.state != 1) && active.some((a) => a.hasResult()))
        active = active.map((a) => a.hasResult() ? new ActiveSource(a.source, 0) : a);
      for (let effect of tr.effects)
        if (effect.is(setSelectedEffect))
          open = open && open.setSelected(effect.value, this.id);
      return active == this.active && open == this.open ? this : new CompletionState(active, this.id, open);
    }
    get tooltip() {
      return this.open ? this.open.tooltip : null;
    }
    get attrs() {
      return this.open ? this.open.attrs : baseAttrs;
    }
  };
  function sameResults(a, b) {
    if (a == b)
      return true;
    for (let iA = 0, iB = 0; ; ) {
      while (iA < a.length && !a[iA].hasResult)
        iA++;
      while (iB < b.length && !b[iB].hasResult)
        iB++;
      let endA = iA == a.length, endB = iB == b.length;
      if (endA || endB)
        return endA == endB;
      if (a[iA++].result != b[iB++].result)
        return false;
    }
  }
  var baseAttrs = {
    "aria-autocomplete": "list"
  };
  function makeAttrs(id2, selected) {
    return {
      "aria-autocomplete": "list",
      "aria-haspopup": "listbox",
      "aria-activedescendant": id2 + "-" + selected,
      "aria-controls": id2
    };
  }
  var none4 = [];
  function cmpOption(a, b) {
    let dScore = b.match[0] - a.match[0];
    if (dScore)
      return dScore;
    return a.completion.label.localeCompare(b.completion.label);
  }
  function getUserEvent(tr) {
    return tr.isUserEvent("input.type") ? "input" : tr.isUserEvent("delete.backward") ? "delete" : null;
  }
  var ActiveSource = class {
    constructor(source, state, explicitPos = -1) {
      this.source = source;
      this.state = state;
      this.explicitPos = explicitPos;
    }
    hasResult() {
      return false;
    }
    update(tr, conf) {
      let event = getUserEvent(tr), value = this;
      if (event)
        value = value.handleUserEvent(tr, event, conf);
      else if (tr.docChanged)
        value = value.handleChange(tr);
      else if (tr.selection && value.state != 0)
        value = new ActiveSource(value.source, 0);
      for (let effect of tr.effects) {
        if (effect.is(startCompletionEffect))
          value = new ActiveSource(value.source, 1, effect.value ? cur(tr.state) : -1);
        else if (effect.is(closeCompletionEffect))
          value = new ActiveSource(value.source, 0);
        else if (effect.is(setActiveEffect)) {
          for (let active of effect.value)
            if (active.source == value.source)
              value = active;
        }
      }
      return value;
    }
    handleUserEvent(tr, type, conf) {
      return type == "delete" || !conf.activateOnTyping ? this.map(tr.changes) : new ActiveSource(this.source, 1);
    }
    handleChange(tr) {
      return tr.changes.touchesRange(cur(tr.startState)) ? new ActiveSource(this.source, 0) : this.map(tr.changes);
    }
    map(changes) {
      return changes.empty || this.explicitPos < 0 ? this : new ActiveSource(this.source, this.state, changes.mapPos(this.explicitPos));
    }
  };
  var ActiveResult = class extends ActiveSource {
    constructor(source, explicitPos, result, from2, to, span2) {
      super(source, 2, explicitPos);
      this.result = result;
      this.from = from2;
      this.to = to;
      this.span = span2;
    }
    hasResult() {
      return true;
    }
    handleUserEvent(tr, type, conf) {
      let from2 = tr.changes.mapPos(this.from), to = tr.changes.mapPos(this.to, 1);
      let pos = cur(tr.state);
      if ((this.explicitPos < 0 ? pos <= from2 : pos < this.from) || pos > to || type == "delete" && cur(tr.startState) == this.from)
        return new ActiveSource(this.source, type == "input" && conf.activateOnTyping ? 1 : 0);
      let explicitPos = this.explicitPos < 0 ? -1 : tr.changes.mapPos(this.explicitPos);
      if (this.span && (from2 == to || this.span.test(tr.state.sliceDoc(from2, to))))
        return new ActiveResult(this.source, explicitPos, this.result, from2, to, this.span);
      return new ActiveSource(this.source, 1, explicitPos);
    }
    handleChange(tr) {
      return tr.changes.touchesRange(this.from, this.to) ? new ActiveSource(this.source, 0) : this.map(tr.changes);
    }
    map(mapping) {
      return mapping.empty ? this : new ActiveResult(this.source, this.explicitPos < 0 ? -1 : mapping.mapPos(this.explicitPos), this.result, mapping.mapPos(this.from), mapping.mapPos(this.to, 1), this.span);
    }
  };
  var startCompletionEffect = /* @__PURE__ */ StateEffect.define();
  var closeCompletionEffect = /* @__PURE__ */ StateEffect.define();
  var setActiveEffect = /* @__PURE__ */ StateEffect.define({
    map(sources, mapping) {
      return sources.map((s) => s.map(mapping));
    }
  });
  var setSelectedEffect = /* @__PURE__ */ StateEffect.define();
  var completionState = /* @__PURE__ */ StateField.define({
    create() {
      return CompletionState.start();
    },
    update(value, tr) {
      return value.update(tr);
    },
    provide: (f) => [
      showTooltip.from(f, (val) => val.tooltip),
      EditorView.contentAttributes.from(f, (state) => state.attrs)
    ]
  });
  var CompletionInteractMargin = 75;
  function moveCompletionSelection(forward, by = "option") {
    return (view) => {
      let cState = view.state.field(completionState, false);
      if (!cState || !cState.open || Date.now() - cState.open.timestamp < CompletionInteractMargin)
        return false;
      let step = 1, tooltip;
      if (by == "page" && (tooltip = getTooltip(view, cState.open.tooltip)))
        step = Math.max(2, Math.floor(tooltip.dom.offsetHeight / tooltip.dom.querySelector("li").offsetHeight) - 1);
      let selected = cState.open.selected + step * (forward ? 1 : -1), { length } = cState.open.options;
      if (selected < 0)
        selected = by == "page" ? 0 : length - 1;
      else if (selected >= length)
        selected = by == "page" ? length - 1 : 0;
      view.dispatch({ effects: setSelectedEffect.of(selected) });
      return true;
    };
  }
  var acceptCompletion = (view) => {
    let cState = view.state.field(completionState, false);
    if (view.state.readOnly || !cState || !cState.open || Date.now() - cState.open.timestamp < CompletionInteractMargin)
      return false;
    applyCompletion(view, cState.open.options[cState.open.selected]);
    return true;
  };
  var startCompletion = (view) => {
    let cState = view.state.field(completionState, false);
    if (!cState)
      return false;
    view.dispatch({ effects: startCompletionEffect.of(true) });
    return true;
  };
  var closeCompletion = (view) => {
    let cState = view.state.field(completionState, false);
    if (!cState || !cState.active.some((a) => a.state != 0))
      return false;
    view.dispatch({ effects: closeCompletionEffect.of(null) });
    return true;
  };
  var RunningQuery = class {
    constructor(active, context) {
      this.active = active;
      this.context = context;
      this.time = Date.now();
      this.updates = [];
      this.done = void 0;
    }
  };
  var DebounceTime = 50;
  var MaxUpdateCount = 50;
  var MinAbortTime = 1e3;
  var completionPlugin = /* @__PURE__ */ ViewPlugin.fromClass(class {
    constructor(view) {
      this.view = view;
      this.debounceUpdate = -1;
      this.running = [];
      this.debounceAccept = -1;
      this.composing = 0;
      for (let active of view.state.field(completionState).active)
        if (active.state == 1)
          this.startQuery(active);
    }
    update(update) {
      let cState = update.state.field(completionState);
      if (!update.selectionSet && !update.docChanged && update.startState.field(completionState) == cState)
        return;
      let doesReset = update.transactions.some((tr) => {
        return (tr.selection || tr.docChanged) && !getUserEvent(tr);
      });
      for (let i = 0; i < this.running.length; i++) {
        let query = this.running[i];
        if (doesReset || query.updates.length + update.transactions.length > MaxUpdateCount && Date.now() - query.time > MinAbortTime) {
          for (let handler of query.context.abortListeners) {
            try {
              handler();
            } catch (e) {
              logException(this.view.state, e);
            }
          }
          query.context.abortListeners = null;
          this.running.splice(i--, 1);
        } else {
          query.updates.push(...update.transactions);
        }
      }
      if (this.debounceUpdate > -1)
        clearTimeout(this.debounceUpdate);
      this.debounceUpdate = cState.active.some((a) => a.state == 1 && !this.running.some((q) => q.active.source == a.source)) ? setTimeout(() => this.startUpdate(), DebounceTime) : -1;
      if (this.composing != 0)
        for (let tr of update.transactions) {
          if (getUserEvent(tr) == "input")
            this.composing = 2;
          else if (this.composing == 2 && tr.selection)
            this.composing = 3;
        }
    }
    startUpdate() {
      this.debounceUpdate = -1;
      let { state } = this.view, cState = state.field(completionState);
      for (let active of cState.active) {
        if (active.state == 1 && !this.running.some((r) => r.active.source == active.source))
          this.startQuery(active);
      }
    }
    startQuery(active) {
      let { state } = this.view, pos = cur(state);
      let context = new CompletionContext(state, pos, active.explicitPos == pos);
      let pending = new RunningQuery(active, context);
      this.running.push(pending);
      Promise.resolve(active.source(context)).then((result) => {
        if (!pending.context.aborted) {
          pending.done = result || null;
          this.scheduleAccept();
        }
      }, (err) => {
        this.view.dispatch({ effects: closeCompletionEffect.of(null) });
        logException(this.view.state, err);
      });
    }
    scheduleAccept() {
      if (this.running.every((q) => q.done !== void 0))
        this.accept();
      else if (this.debounceAccept < 0)
        this.debounceAccept = setTimeout(() => this.accept(), DebounceTime);
    }
    accept() {
      var _a2;
      if (this.debounceAccept > -1)
        clearTimeout(this.debounceAccept);
      this.debounceAccept = -1;
      let updated = [];
      let conf = this.view.state.facet(completionConfig);
      for (let i = 0; i < this.running.length; i++) {
        let query = this.running[i];
        if (query.done === void 0)
          continue;
        this.running.splice(i--, 1);
        if (query.done) {
          let active = new ActiveResult(query.active.source, query.active.explicitPos, query.done, query.done.from, (_a2 = query.done.to) !== null && _a2 !== void 0 ? _a2 : cur(query.updates.length ? query.updates[0].startState : this.view.state), query.done.span && query.done.filter !== false ? ensureAnchor(query.done.span, true) : null);
          for (let tr of query.updates)
            active = active.update(tr, conf);
          if (active.hasResult()) {
            updated.push(active);
            continue;
          }
        }
        let current = this.view.state.field(completionState).active.find((a) => a.source == query.active.source);
        if (current && current.state == 1) {
          if (query.done == null) {
            let active = new ActiveSource(query.active.source, 0);
            for (let tr of query.updates)
              active = active.update(tr, conf);
            if (active.state != 1)
              updated.push(active);
          } else {
            this.startQuery(current);
          }
        }
      }
      if (updated.length)
        this.view.dispatch({ effects: setActiveEffect.of(updated) });
    }
  }, {
    eventHandlers: {
      compositionstart() {
        this.composing = 1;
      },
      compositionend() {
        if (this.composing == 3) {
          setTimeout(() => this.view.dispatch({ effects: startCompletionEffect.of(false) }), 20);
        }
        this.composing = 0;
      }
    }
  });
  var baseTheme8 = /* @__PURE__ */ EditorView.baseTheme({
    ".cm-tooltip.cm-tooltip-autocomplete": {
      "& > ul": {
        fontFamily: "monospace",
        whiteSpace: "nowrap",
        overflow: "hidden auto",
        maxWidth_fallback: "700px",
        maxWidth: "min(700px, 95vw)",
        minWidth: "250px",
        maxHeight: "10em",
        listStyle: "none",
        margin: 0,
        padding: 0,
        "& > li": {
          overflowX: "hidden",
          textOverflow: "ellipsis",
          cursor: "pointer",
          padding: "1px 3px",
          lineHeight: 1.2
        }
      }
    },
    "&light .cm-tooltip-autocomplete ul li[aria-selected]": {
      background: "#17c",
      color: "white"
    },
    "&dark .cm-tooltip-autocomplete ul li[aria-selected]": {
      background: "#347",
      color: "white"
    },
    ".cm-completionListIncompleteTop:before, .cm-completionListIncompleteBottom:after": {
      content: '"\xB7\xB7\xB7"',
      opacity: 0.5,
      display: "block",
      textAlign: "center"
    },
    ".cm-tooltip.cm-completionInfo": {
      position: "absolute",
      padding: "3px 9px",
      width: "max-content",
      maxWidth: "300px"
    },
    ".cm-completionInfo.cm-completionInfo-left": { right: "100%" },
    ".cm-completionInfo.cm-completionInfo-right": { left: "100%" },
    "&light .cm-snippetField": { backgroundColor: "#00000022" },
    "&dark .cm-snippetField": { backgroundColor: "#ffffff22" },
    ".cm-snippetFieldPosition": {
      verticalAlign: "text-top",
      width: 0,
      height: "1.15em",
      margin: "0 -0.7px -.7em",
      borderLeft: "1.4px dotted #888"
    },
    ".cm-completionMatchedText": {
      textDecoration: "underline"
    },
    ".cm-completionDetail": {
      marginLeft: "0.5em",
      fontStyle: "italic"
    },
    ".cm-completionIcon": {
      fontSize: "90%",
      width: ".8em",
      display: "inline-block",
      textAlign: "center",
      paddingRight: ".6em",
      opacity: "0.6"
    },
    ".cm-completionIcon-function, .cm-completionIcon-method": {
      "&:after": { content: "'\u0192'" }
    },
    ".cm-completionIcon-class": {
      "&:after": { content: "'\u25CB'" }
    },
    ".cm-completionIcon-interface": {
      "&:after": { content: "'\u25CC'" }
    },
    ".cm-completionIcon-variable": {
      "&:after": { content: "'\u{1D465}'" }
    },
    ".cm-completionIcon-constant": {
      "&:after": { content: "'\u{1D436}'" }
    },
    ".cm-completionIcon-type": {
      "&:after": { content: "'\u{1D461}'" }
    },
    ".cm-completionIcon-enum": {
      "&:after": { content: "'\u222A'" }
    },
    ".cm-completionIcon-property": {
      "&:after": { content: "'\u25A1'" }
    },
    ".cm-completionIcon-keyword": {
      "&:after": { content: "'\u{1F511}\uFE0E'" }
    },
    ".cm-completionIcon-namespace": {
      "&:after": { content: "'\u25A2'" }
    },
    ".cm-completionIcon-text": {
      "&:after": { content: "'abc'", fontSize: "50%", verticalAlign: "middle" }
    }
  });
  var FieldPos = class {
    constructor(field, line, from2, to) {
      this.field = field;
      this.line = line;
      this.from = from2;
      this.to = to;
    }
  };
  var FieldRange = class {
    constructor(field, from2, to) {
      this.field = field;
      this.from = from2;
      this.to = to;
    }
    map(changes) {
      let from2 = changes.mapPos(this.from, -1, MapMode.TrackDel);
      let to = changes.mapPos(this.to, 1, MapMode.TrackDel);
      return from2 == null || to == null ? null : new FieldRange(this.field, from2, to);
    }
  };
  var Snippet = class {
    constructor(lines, fieldPositions) {
      this.lines = lines;
      this.fieldPositions = fieldPositions;
    }
    instantiate(state, pos) {
      let text = [], lineStart = [pos];
      let lineObj = state.doc.lineAt(pos), baseIndent = /^\s*/.exec(lineObj.text)[0];
      for (let line of this.lines) {
        if (text.length) {
          let indent = baseIndent, tabs = /^\t*/.exec(line)[0].length;
          for (let i = 0; i < tabs; i++)
            indent += state.facet(indentUnit);
          lineStart.push(pos + indent.length - tabs);
          line = indent + line.slice(tabs);
        }
        text.push(line);
        pos += line.length + 1;
      }
      let ranges = this.fieldPositions.map((pos2) => new FieldRange(pos2.field, lineStart[pos2.line] + pos2.from, lineStart[pos2.line] + pos2.to));
      return { text, ranges };
    }
    static parse(template2) {
      let fields = [];
      let lines = [], positions = [], m;
      for (let line of template2.split(/\r\n?|\n/)) {
        while (m = /[#$]\{(?:(\d+)(?::([^}]*))?|([^}]*))\}/.exec(line)) {
          let seq = m[1] ? +m[1] : null, name2 = m[2] || m[3] || "", found = -1;
          for (let i = 0; i < fields.length; i++) {
            if (seq != null ? fields[i].seq == seq : name2 ? fields[i].name == name2 : false)
              found = i;
          }
          if (found < 0) {
            let i = 0;
            while (i < fields.length && (seq == null || fields[i].seq != null && fields[i].seq < seq))
              i++;
            fields.splice(i, 0, { seq, name: name2 });
            found = i;
            for (let pos of positions)
              if (pos.field >= found)
                pos.field++;
          }
          positions.push(new FieldPos(found, lines.length, m.index, m.index + name2.length));
          line = line.slice(0, m.index) + name2 + line.slice(m.index + m[0].length);
        }
        lines.push(line);
      }
      return new Snippet(lines, positions);
    }
  };
  var fieldMarker = /* @__PURE__ */ Decoration.widget({ widget: /* @__PURE__ */ new class extends WidgetType {
    toDOM() {
      let span2 = document.createElement("span");
      span2.className = "cm-snippetFieldPosition";
      return span2;
    }
    ignoreEvent() {
      return false;
    }
  }() });
  var fieldRange = /* @__PURE__ */ Decoration.mark({ class: "cm-snippetField" });
  var ActiveSnippet = class {
    constructor(ranges, active) {
      this.ranges = ranges;
      this.active = active;
      this.deco = Decoration.set(ranges.map((r) => (r.from == r.to ? fieldMarker : fieldRange).range(r.from, r.to)));
    }
    map(changes) {
      let ranges = [];
      for (let r of this.ranges) {
        let mapped = r.map(changes);
        if (!mapped)
          return null;
        ranges.push(mapped);
      }
      return new ActiveSnippet(ranges, this.active);
    }
    selectionInsideField(sel) {
      return sel.ranges.every((range) => this.ranges.some((r) => r.field == this.active && r.from <= range.from && r.to >= range.to));
    }
  };
  var setActive = /* @__PURE__ */ StateEffect.define({
    map(value, changes) {
      return value && value.map(changes);
    }
  });
  var moveToField = /* @__PURE__ */ StateEffect.define();
  var snippetState = /* @__PURE__ */ StateField.define({
    create() {
      return null;
    },
    update(value, tr) {
      for (let effect of tr.effects) {
        if (effect.is(setActive))
          return effect.value;
        if (effect.is(moveToField) && value)
          return new ActiveSnippet(value.ranges, effect.value);
      }
      if (value && tr.docChanged)
        value = value.map(tr.changes);
      if (value && tr.selection && !value.selectionInsideField(tr.selection))
        value = null;
      return value;
    },
    provide: (f) => EditorView.decorations.from(f, (val) => val ? val.deco : Decoration.none)
  });
  function fieldSelection(ranges, field) {
    return EditorSelection.create(ranges.filter((r) => r.field == field).map((r) => EditorSelection.range(r.from, r.to)));
  }
  function snippet(template2) {
    let snippet2 = Snippet.parse(template2);
    return (editor, _completion, from2, to) => {
      let { text, ranges } = snippet2.instantiate(editor.state, from2);
      let spec = { changes: { from: from2, to, insert: Text.of(text) } };
      if (ranges.length)
        spec.selection = fieldSelection(ranges, 0);
      if (ranges.length > 1) {
        let active = new ActiveSnippet(ranges, 0);
        let effects = spec.effects = [setActive.of(active)];
        if (editor.state.field(snippetState, false) === void 0)
          effects.push(StateEffect.appendConfig.of([snippetState, addSnippetKeymap, snippetPointerHandler, baseTheme8]));
      }
      editor.dispatch(editor.state.update(spec));
    };
  }
  function moveField(dir) {
    return ({ state, dispatch }) => {
      let active = state.field(snippetState, false);
      if (!active || dir < 0 && active.active == 0)
        return false;
      let next = active.active + dir, last = dir > 0 && !active.ranges.some((r) => r.field == next + dir);
      dispatch(state.update({
        selection: fieldSelection(active.ranges, next),
        effects: setActive.of(last ? null : new ActiveSnippet(active.ranges, next))
      }));
      return true;
    };
  }
  var clearSnippet = ({ state, dispatch }) => {
    let active = state.field(snippetState, false);
    if (!active)
      return false;
    dispatch(state.update({ effects: setActive.of(null) }));
    return true;
  };
  var nextSnippetField = /* @__PURE__ */ moveField(1);
  var prevSnippetField = /* @__PURE__ */ moveField(-1);
  var defaultSnippetKeymap = [
    { key: "Tab", run: nextSnippetField, shift: prevSnippetField },
    { key: "Escape", run: clearSnippet }
  ];
  var snippetKeymap = /* @__PURE__ */ Facet.define({
    combine(maps) {
      return maps.length ? maps[0] : defaultSnippetKeymap;
    }
  });
  var addSnippetKeymap = /* @__PURE__ */ Prec.highest(/* @__PURE__ */ keymap.compute([snippetKeymap], (state) => state.facet(snippetKeymap)));
  function snippetCompletion(template2, completion) {
    return Object.assign(Object.assign({}, completion), { apply: snippet(template2) });
  }
  var snippetPointerHandler = /* @__PURE__ */ EditorView.domEventHandlers({
    mousedown(event, view) {
      let active = view.state.field(snippetState, false), pos;
      if (!active || (pos = view.posAtCoords({ x: event.clientX, y: event.clientY })) == null)
        return false;
      let match2 = active.ranges.find((r) => r.from <= pos && r.to >= pos);
      if (!match2 || match2.field == active.active)
        return false;
      view.dispatch({
        selection: fieldSelection(active.ranges, match2.field),
        effects: setActive.of(active.ranges.some((r) => r.field > match2.field) ? new ActiveSnippet(active.ranges, match2.field) : null)
      });
      return true;
    }
  });
  function autocompletion(config2 = {}) {
    return [
      completionState,
      completionConfig.of(config2),
      completionPlugin,
      completionKeymapExt,
      baseTheme8
    ];
  }
  var completionKeymap = [
    { key: "Ctrl-Space", run: startCompletion },
    { key: "Escape", run: closeCompletion },
    { key: "ArrowDown", run: /* @__PURE__ */ moveCompletionSelection(true) },
    { key: "ArrowUp", run: /* @__PURE__ */ moveCompletionSelection(false) },
    { key: "PageDown", run: /* @__PURE__ */ moveCompletionSelection(true, "page") },
    { key: "PageUp", run: /* @__PURE__ */ moveCompletionSelection(false, "page") },
    { key: "Enter", run: acceptCompletion }
  ];
  var completionKeymapExt = /* @__PURE__ */ Prec.highest(/* @__PURE__ */ keymap.computeN([completionConfig], (state) => state.facet(completionConfig).defaultKeymap ? [completionKeymap] : []));

  // node_modules/@codemirror/comment/dist/index.js
  var toggleComment = (target) => {
    let config2 = getConfig(target.state);
    return config2.line ? toggleLineComment(target) : config2.block ? toggleBlockCommentByLine(target) : false;
  };
  function command(f, option) {
    return ({ state, dispatch }) => {
      if (state.readOnly)
        return false;
      let tr = f(option, state);
      if (!tr)
        return false;
      dispatch(state.update(tr));
      return true;
    };
  }
  var toggleLineComment = /* @__PURE__ */ command(changeLineComment, 0);
  var toggleBlockComment = /* @__PURE__ */ command(changeBlockComment, 0);
  var toggleBlockCommentByLine = /* @__PURE__ */ command((o, s) => changeBlockComment(o, s, selectedLineRanges(s)), 0);
  var commentKeymap = [
    { key: "Mod-/", run: toggleComment },
    { key: "Alt-A", run: toggleBlockComment }
  ];
  function getConfig(state, pos = state.selection.main.head) {
    let data = state.languageDataAt("commentTokens", pos);
    return data.length ? data[0] : {};
  }
  var SearchMargin = 50;
  function findBlockComment(state, { open, close }, from2, to) {
    let textBefore = state.sliceDoc(from2 - SearchMargin, from2);
    let textAfter = state.sliceDoc(to, to + SearchMargin);
    let spaceBefore = /\s*$/.exec(textBefore)[0].length, spaceAfter = /^\s*/.exec(textAfter)[0].length;
    let beforeOff = textBefore.length - spaceBefore;
    if (textBefore.slice(beforeOff - open.length, beforeOff) == open && textAfter.slice(spaceAfter, spaceAfter + close.length) == close) {
      return {
        open: { pos: from2 - spaceBefore, margin: spaceBefore && 1 },
        close: { pos: to + spaceAfter, margin: spaceAfter && 1 }
      };
    }
    let startText, endText;
    if (to - from2 <= 2 * SearchMargin) {
      startText = endText = state.sliceDoc(from2, to);
    } else {
      startText = state.sliceDoc(from2, from2 + SearchMargin);
      endText = state.sliceDoc(to - SearchMargin, to);
    }
    let startSpace = /^\s*/.exec(startText)[0].length, endSpace = /\s*$/.exec(endText)[0].length;
    let endOff = endText.length - endSpace - close.length;
    if (startText.slice(startSpace, startSpace + open.length) == open && endText.slice(endOff, endOff + close.length) == close) {
      return {
        open: {
          pos: from2 + startSpace + open.length,
          margin: /\s/.test(startText.charAt(startSpace + open.length)) ? 1 : 0
        },
        close: {
          pos: to - endSpace - close.length,
          margin: /\s/.test(endText.charAt(endOff - 1)) ? 1 : 0
        }
      };
    }
    return null;
  }
  function selectedLineRanges(state) {
    let ranges = [];
    for (let r of state.selection.ranges) {
      let fromLine = state.doc.lineAt(r.from);
      let toLine = r.to <= fromLine.to ? fromLine : state.doc.lineAt(r.to);
      let last = ranges.length - 1;
      if (last >= 0 && ranges[last].to > fromLine.from)
        ranges[last].to = toLine.to;
      else
        ranges.push({ from: fromLine.from, to: toLine.to });
    }
    return ranges;
  }
  function changeBlockComment(option, state, ranges = state.selection.ranges) {
    let tokens = ranges.map((r) => getConfig(state, r.from).block);
    if (!tokens.every((c) => c))
      return null;
    let comments = ranges.map((r, i) => findBlockComment(state, tokens[i], r.from, r.to));
    if (option != 2 && !comments.every((c) => c)) {
      return { changes: state.changes(ranges.map((range, i) => {
        if (comments[i])
          return [];
        return [{ from: range.from, insert: tokens[i].open + " " }, { from: range.to, insert: " " + tokens[i].close }];
      })) };
    } else if (option != 1 && comments.some((c) => c)) {
      let changes = [];
      for (let i = 0, comment2; i < comments.length; i++)
        if (comment2 = comments[i]) {
          let token = tokens[i], { open, close } = comment2;
          changes.push({ from: open.pos - token.open.length, to: open.pos + open.margin }, { from: close.pos - close.margin, to: close.pos + token.close.length });
        }
      return { changes };
    }
    return null;
  }
  function changeLineComment(option, state, ranges = state.selection.ranges) {
    let lines = [];
    let prevLine = -1;
    for (let { from: from2, to } of ranges) {
      let startI = lines.length, minIndent = 1e9;
      for (let pos = from2; pos <= to; ) {
        let line = state.doc.lineAt(pos);
        if (line.from > prevLine && (from2 == to || to > line.from)) {
          prevLine = line.from;
          let token = getConfig(state, pos).line;
          if (!token)
            continue;
          let indent = /^\s*/.exec(line.text)[0].length;
          let empty2 = indent == line.length;
          let comment2 = line.text.slice(indent, indent + token.length) == token ? indent : -1;
          if (indent < line.text.length && indent < minIndent)
            minIndent = indent;
          lines.push({ line, comment: comment2, token, indent, empty: empty2, single: false });
        }
        pos = line.to + 1;
      }
      if (minIndent < 1e9) {
        for (let i = startI; i < lines.length; i++)
          if (lines[i].indent < lines[i].line.text.length)
            lines[i].indent = minIndent;
      }
      if (lines.length == startI + 1)
        lines[startI].single = true;
    }
    if (option != 2 && lines.some((l) => l.comment < 0 && (!l.empty || l.single))) {
      let changes = [];
      for (let { line, token, indent, empty: empty2, single } of lines)
        if (single || !empty2)
          changes.push({ from: line.from + indent, insert: token + " " });
      let changeSet = state.changes(changes);
      return { changes: changeSet, selection: state.selection.map(changeSet, 1) };
    } else if (option != 1 && lines.some((l) => l.comment >= 0)) {
      let changes = [];
      for (let { line, comment: comment2, token } of lines)
        if (comment2 >= 0) {
          let from2 = line.from + comment2, to = from2 + token.length;
          if (line.text[to - line.from] == " ")
            to++;
          changes.push({ from: from2, to });
        }
      return { changes };
    }
    return null;
  }

  // node_modules/@codemirror/rectangular-selection/dist/index.js
  var MaxOff = 2e3;
  function rectangleFor(state, a, b) {
    let startLine = Math.min(a.line, b.line), endLine = Math.max(a.line, b.line);
    let ranges = [];
    if (a.off > MaxOff || b.off > MaxOff || a.col < 0 || b.col < 0) {
      let startOff = Math.min(a.off, b.off), endOff = Math.max(a.off, b.off);
      for (let i = startLine; i <= endLine; i++) {
        let line = state.doc.line(i);
        if (line.length <= endOff)
          ranges.push(EditorSelection.range(line.from + startOff, line.to + endOff));
      }
    } else {
      let startCol = Math.min(a.col, b.col), endCol = Math.max(a.col, b.col);
      for (let i = startLine; i <= endLine; i++) {
        let line = state.doc.line(i);
        let start = findColumn(line.text, startCol, state.tabSize, true);
        if (start > -1) {
          let end = findColumn(line.text, endCol, state.tabSize);
          ranges.push(EditorSelection.range(line.from + start, line.from + end));
        }
      }
    }
    return ranges;
  }
  function absoluteColumn(view, x) {
    let ref = view.coordsAtPos(view.viewport.from);
    return ref ? Math.round(Math.abs((ref.left - x) / view.defaultCharacterWidth)) : -1;
  }
  function getPos(view, event) {
    let offset = view.posAtCoords({ x: event.clientX, y: event.clientY }, false);
    let line = view.state.doc.lineAt(offset), off = offset - line.from;
    let col = off > MaxOff ? -1 : off == line.length ? absoluteColumn(view, event.clientX) : countColumn(line.text, view.state.tabSize, offset - line.from);
    return { line: line.number, col, off };
  }
  function rectangleSelectionStyle(view, event) {
    let start = getPos(view, event), startSel = view.state.selection;
    if (!start)
      return null;
    return {
      update(update) {
        if (update.docChanged) {
          let newStart = update.changes.mapPos(update.startState.doc.line(start.line).from);
          let newLine = update.state.doc.lineAt(newStart);
          start = { line: newLine.number, col: start.col, off: Math.min(start.off, newLine.length) };
          startSel = startSel.map(update.changes);
        }
      },
      get(event2, _extend, multiple) {
        let cur2 = getPos(view, event2);
        if (!cur2)
          return startSel;
        let ranges = rectangleFor(view.state, start, cur2);
        if (!ranges.length)
          return startSel;
        if (multiple)
          return EditorSelection.create(ranges.concat(startSel.ranges));
        else
          return EditorSelection.create(ranges);
      }
    };
  }
  function rectangularSelection(options) {
    let filter = (options === null || options === void 0 ? void 0 : options.eventFilter) || ((e) => e.altKey && e.button == 0);
    return EditorView.mouseSelectionStyle.of((view, event) => filter(event) ? rectangleSelectionStyle(view, event) : null);
  }
  var keys = {
    Alt: [18, (e) => e.altKey],
    Control: [17, (e) => e.ctrlKey],
    Shift: [16, (e) => e.shiftKey],
    Meta: [91, (e) => e.metaKey]
  };
  var showCrosshair = { style: "cursor: crosshair" };
  function crosshairCursor(options = {}) {
    let [code, getter] = keys[options.key || "Alt"];
    let plugin = ViewPlugin.fromClass(class {
      constructor(view) {
        this.view = view;
        this.isDown = false;
      }
      set(isDown) {
        if (this.isDown != isDown) {
          this.isDown = isDown;
          this.view.update([]);
        }
      }
    }, {
      eventHandlers: {
        keydown(e) {
          this.set(e.keyCode == code || getter(e));
        },
        keyup(e) {
          if (e.keyCode == code || !getter(e))
            this.set(false);
        }
      }
    });
    return [
      plugin,
      EditorView.contentAttributes.of((view) => {
        var _a2;
        return ((_a2 = view.plugin(plugin)) === null || _a2 === void 0 ? void 0 : _a2.isDown) ? showCrosshair : null;
      })
    ];
  }

  // node_modules/@codemirror/highlight/dist/index.js
  var nextTagID = 0;
  var Tag = class {
    constructor(set, base2, modified) {
      this.set = set;
      this.base = base2;
      this.modified = modified;
      this.id = nextTagID++;
    }
    static define(parent) {
      if (parent === null || parent === void 0 ? void 0 : parent.base)
        throw new Error("Can not derive from a modified tag");
      let tag = new Tag([], null, []);
      tag.set.push(tag);
      if (parent)
        for (let t2 of parent.set)
          tag.set.push(t2);
      return tag;
    }
    static defineModifier() {
      let mod = new Modifier();
      return (tag) => {
        if (tag.modified.indexOf(mod) > -1)
          return tag;
        return Modifier.get(tag.base || tag, tag.modified.concat(mod).sort((a, b) => a.id - b.id));
      };
    }
  };
  var nextModifierID = 0;
  var Modifier = class {
    constructor() {
      this.instances = [];
      this.id = nextModifierID++;
    }
    static get(base2, mods) {
      if (!mods.length)
        return base2;
      let exists = mods[0].instances.find((t2) => t2.base == base2 && sameArray2(mods, t2.modified));
      if (exists)
        return exists;
      let set = [], tag = new Tag(set, base2, mods);
      for (let m of mods)
        m.instances.push(tag);
      let configs = permute(mods);
      for (let parent of base2.set)
        for (let config2 of configs)
          set.push(Modifier.get(parent, config2));
      return tag;
    }
  };
  function sameArray2(a, b) {
    return a.length == b.length && a.every((x, i) => x == b[i]);
  }
  function permute(array2) {
    let result = [array2];
    for (let i = 0; i < array2.length; i++) {
      for (let a of permute(array2.slice(0, i).concat(array2.slice(i + 1))))
        result.push(a);
    }
    return result;
  }
  function styleTags(spec) {
    let byName = /* @__PURE__ */ Object.create(null);
    for (let prop in spec) {
      let tags3 = spec[prop];
      if (!Array.isArray(tags3))
        tags3 = [tags3];
      for (let part of prop.split(" "))
        if (part) {
          let pieces = [], mode = 2, rest = part;
          for (let pos = 0; ; ) {
            if (rest == "..." && pos > 0 && pos + 3 == part.length) {
              mode = 1;
              break;
            }
            let m = /^"(?:[^"\\]|\\.)*?"|[^\/!]+/.exec(rest);
            if (!m)
              throw new RangeError("Invalid path: " + part);
            pieces.push(m[0] == "*" ? null : m[0][0] == '"' ? JSON.parse(m[0]) : m[0]);
            pos += m[0].length;
            if (pos == part.length)
              break;
            let next = part[pos++];
            if (pos == part.length && next == "!") {
              mode = 0;
              break;
            }
            if (next != "/")
              throw new RangeError("Invalid path: " + part);
            rest = part.slice(pos);
          }
          let last = pieces.length - 1, inner = pieces[last];
          if (!inner)
            throw new RangeError("Invalid path: " + part);
          let rule = new Rule(tags3, mode, last > 0 ? pieces.slice(0, last) : null);
          byName[inner] = rule.sort(byName[inner]);
        }
    }
    return ruleNodeProp.add(byName);
  }
  var ruleNodeProp = /* @__PURE__ */ new NodeProp();
  var highlightStyle = /* @__PURE__ */ Facet.define({
    combine(stylings) {
      return stylings.length ? HighlightStyle.combinedMatch(stylings) : null;
    }
  });
  var fallbackHighlightStyle = /* @__PURE__ */ Facet.define({
    combine(values2) {
      return values2.length ? values2[0].match : null;
    }
  });
  function getHighlightStyle(state) {
    return state.facet(highlightStyle) || state.facet(fallbackHighlightStyle);
  }
  var Rule = class {
    constructor(tags3, mode, context, next) {
      this.tags = tags3;
      this.mode = mode;
      this.context = context;
      this.next = next;
    }
    sort(other) {
      if (!other || other.depth < this.depth) {
        this.next = other;
        return this;
      }
      other.next = this.sort(other.next);
      return other;
    }
    get depth() {
      return this.context ? this.context.length : 0;
    }
  };
  var HighlightStyle = class {
    constructor(spec, options) {
      this.map = /* @__PURE__ */ Object.create(null);
      let modSpec;
      function def(spec2) {
        let cls = StyleModule.newName();
        (modSpec || (modSpec = /* @__PURE__ */ Object.create(null)))["." + cls] = spec2;
        return cls;
      }
      this.all = typeof options.all == "string" ? options.all : options.all ? def(options.all) : null;
      for (let style of spec) {
        let cls = (style.class || def(Object.assign({}, style, { tag: null }))) + (this.all ? " " + this.all : "");
        let tags3 = style.tag;
        if (!Array.isArray(tags3))
          this.map[tags3.id] = cls;
        else
          for (let tag of tags3)
            this.map[tag.id] = cls;
      }
      this.module = modSpec ? new StyleModule(modSpec) : null;
      this.scope = options.scope || null;
      this.match = this.match.bind(this);
      let ext = [treeHighlighter];
      if (this.module)
        ext.push(EditorView.styleModule.of(this.module));
      this.extension = ext.concat(options.themeType == null ? highlightStyle.of(this) : highlightStyle.computeN([EditorView.darkTheme], (state) => {
        return state.facet(EditorView.darkTheme) == (options.themeType == "dark") ? [this] : [];
      }));
      this.fallback = ext.concat(fallbackHighlightStyle.of(this));
    }
    match(tag, scope) {
      if (this.scope && scope != this.scope)
        return null;
      for (let t2 of tag.set) {
        let match2 = this.map[t2.id];
        if (match2 !== void 0) {
          if (t2 != tag)
            this.map[tag.id] = match2;
          return match2;
        }
      }
      return this.map[tag.id] = this.all;
    }
    static combinedMatch(styles) {
      if (styles.length == 1)
        return styles[0].match;
      let cache = styles.some((s) => s.scope) ? void 0 : /* @__PURE__ */ Object.create(null);
      return (tag, scope) => {
        let cached = cache && cache[tag.id];
        if (cached !== void 0)
          return cached;
        let result = null;
        for (let style of styles) {
          let value = style.match(tag, scope);
          if (value)
            result = result ? result + " " + value : value;
        }
        if (cache)
          cache[tag.id] = result;
        return result;
      };
    }
    static define(specs, options) {
      return new HighlightStyle(specs, options || {});
    }
    static get(state, tag, scope) {
      let style = getHighlightStyle(state);
      return style && style(tag, scope || NodeType.none);
    }
  };
  var TreeHighlighter = class {
    constructor(view) {
      this.markCache = /* @__PURE__ */ Object.create(null);
      this.tree = syntaxTree(view.state);
      this.decorations = this.buildDeco(view, getHighlightStyle(view.state));
    }
    update(update) {
      let tree = syntaxTree(update.state), style = getHighlightStyle(update.state);
      let styleChange = style != update.startState.facet(highlightStyle);
      if (tree.length < update.view.viewport.to && !styleChange && tree.type == this.tree.type) {
        this.decorations = this.decorations.map(update.changes);
      } else if (tree != this.tree || update.viewportChanged || styleChange) {
        this.tree = tree;
        this.decorations = this.buildDeco(update.view, style);
      }
    }
    buildDeco(view, match2) {
      if (!match2 || !this.tree.length)
        return Decoration.none;
      let builder = new RangeSetBuilder();
      for (let { from: from2, to } of view.visibleRanges) {
        highlightTreeRange(this.tree, from2, to, match2, (from3, to2, style) => {
          builder.add(from3, to2, this.markCache[style] || (this.markCache[style] = Decoration.mark({ class: style })));
        });
      }
      return builder.finish();
    }
  };
  var treeHighlighter = /* @__PURE__ */ Prec.high(/* @__PURE__ */ ViewPlugin.fromClass(TreeHighlighter, {
    decorations: (v) => v.decorations
  }));
  var nodeStack = [""];
  var HighlightBuilder = class {
    constructor(at, style, span2) {
      this.at = at;
      this.style = style;
      this.span = span2;
      this.class = "";
    }
    startSpan(at, cls) {
      if (cls != this.class) {
        this.flush(at);
        if (at > this.at)
          this.at = at;
        this.class = cls;
      }
    }
    flush(to) {
      if (to > this.at && this.class)
        this.span(this.at, to, this.class);
    }
    highlightRange(cursor, from2, to, inheritedClass, depth, scope) {
      let { type, from: start, to: end } = cursor;
      if (start >= to || end <= from2)
        return;
      nodeStack[depth] = type.name;
      if (type.isTop)
        scope = type;
      let cls = inheritedClass;
      let rule = type.prop(ruleNodeProp), opaque = false;
      while (rule) {
        if (!rule.context || matchContext(rule.context, nodeStack, depth)) {
          for (let tag of rule.tags) {
            let st = this.style(tag, scope);
            if (st) {
              if (cls)
                cls += " ";
              cls += st;
              if (rule.mode == 1)
                inheritedClass += (inheritedClass ? " " : "") + st;
              else if (rule.mode == 0)
                opaque = true;
            }
          }
          break;
        }
        rule = rule.next;
      }
      this.startSpan(cursor.from, cls);
      if (opaque)
        return;
      let mounted = cursor.tree && cursor.tree.prop(NodeProp.mounted);
      if (mounted && mounted.overlay) {
        let inner = cursor.node.enter(mounted.overlay[0].from + start, 1);
        let hasChild2 = cursor.firstChild();
        for (let i = 0, pos = start; ; i++) {
          let next = i < mounted.overlay.length ? mounted.overlay[i] : null;
          let nextPos = next ? next.from + start : end;
          let rangeFrom = Math.max(from2, pos), rangeTo = Math.min(to, nextPos);
          if (rangeFrom < rangeTo && hasChild2) {
            while (cursor.from < rangeTo) {
              this.highlightRange(cursor, rangeFrom, rangeTo, inheritedClass, depth + 1, scope);
              this.startSpan(Math.min(to, cursor.to), cls);
              if (cursor.to >= nextPos || !cursor.nextSibling())
                break;
            }
          }
          if (!next || nextPos > to)
            break;
          pos = next.to + start;
          if (pos > from2) {
            this.highlightRange(inner.cursor, Math.max(from2, next.from + start), Math.min(to, pos), inheritedClass, depth, mounted.tree.type);
            this.startSpan(pos, cls);
          }
        }
        if (hasChild2)
          cursor.parent();
      } else if (cursor.firstChild()) {
        do {
          if (cursor.to <= from2)
            continue;
          if (cursor.from >= to)
            break;
          this.highlightRange(cursor, from2, to, inheritedClass, depth + 1, scope);
          this.startSpan(Math.min(to, cursor.to), cls);
        } while (cursor.nextSibling());
        cursor.parent();
      }
    }
  };
  function highlightTreeRange(tree, from2, to, style, span2) {
    let builder = new HighlightBuilder(from2, style, span2);
    builder.highlightRange(tree.cursor(), from2, to, "", 0, tree.type);
    builder.flush(to);
  }
  function matchContext(context, stack, depth) {
    if (context.length > depth - 1)
      return false;
    for (let d = depth - 1, i = context.length - 1; i >= 0; i--, d--) {
      let check = context[i];
      if (check && check != stack[d])
        return false;
    }
    return true;
  }
  var t = Tag.define;
  var comment = /* @__PURE__ */ t();
  var name = /* @__PURE__ */ t();
  var typeName = /* @__PURE__ */ t(name);
  var propertyName = /* @__PURE__ */ t(name);
  var literal = /* @__PURE__ */ t();
  var string = /* @__PURE__ */ t(literal);
  var number = /* @__PURE__ */ t(literal);
  var content = /* @__PURE__ */ t();
  var heading = /* @__PURE__ */ t(content);
  var keyword = /* @__PURE__ */ t();
  var operator = /* @__PURE__ */ t();
  var punctuation = /* @__PURE__ */ t();
  var bracket = /* @__PURE__ */ t(punctuation);
  var meta = /* @__PURE__ */ t();
  var tags = {
    comment,
    lineComment: /* @__PURE__ */ t(comment),
    blockComment: /* @__PURE__ */ t(comment),
    docComment: /* @__PURE__ */ t(comment),
    name,
    variableName: /* @__PURE__ */ t(name),
    typeName,
    tagName: /* @__PURE__ */ t(typeName),
    propertyName,
    attributeName: /* @__PURE__ */ t(propertyName),
    className: /* @__PURE__ */ t(name),
    labelName: /* @__PURE__ */ t(name),
    namespace: /* @__PURE__ */ t(name),
    macroName: /* @__PURE__ */ t(name),
    literal,
    string,
    docString: /* @__PURE__ */ t(string),
    character: /* @__PURE__ */ t(string),
    attributeValue: /* @__PURE__ */ t(string),
    number,
    integer: /* @__PURE__ */ t(number),
    float: /* @__PURE__ */ t(number),
    bool: /* @__PURE__ */ t(literal),
    regexp: /* @__PURE__ */ t(literal),
    escape: /* @__PURE__ */ t(literal),
    color: /* @__PURE__ */ t(literal),
    url: /* @__PURE__ */ t(literal),
    keyword,
    self: /* @__PURE__ */ t(keyword),
    null: /* @__PURE__ */ t(keyword),
    atom: /* @__PURE__ */ t(keyword),
    unit: /* @__PURE__ */ t(keyword),
    modifier: /* @__PURE__ */ t(keyword),
    operatorKeyword: /* @__PURE__ */ t(keyword),
    controlKeyword: /* @__PURE__ */ t(keyword),
    definitionKeyword: /* @__PURE__ */ t(keyword),
    moduleKeyword: /* @__PURE__ */ t(keyword),
    operator,
    derefOperator: /* @__PURE__ */ t(operator),
    arithmeticOperator: /* @__PURE__ */ t(operator),
    logicOperator: /* @__PURE__ */ t(operator),
    bitwiseOperator: /* @__PURE__ */ t(operator),
    compareOperator: /* @__PURE__ */ t(operator),
    updateOperator: /* @__PURE__ */ t(operator),
    definitionOperator: /* @__PURE__ */ t(operator),
    typeOperator: /* @__PURE__ */ t(operator),
    controlOperator: /* @__PURE__ */ t(operator),
    punctuation,
    separator: /* @__PURE__ */ t(punctuation),
    bracket,
    angleBracket: /* @__PURE__ */ t(bracket),
    squareBracket: /* @__PURE__ */ t(bracket),
    paren: /* @__PURE__ */ t(bracket),
    brace: /* @__PURE__ */ t(bracket),
    content,
    heading,
    heading1: /* @__PURE__ */ t(heading),
    heading2: /* @__PURE__ */ t(heading),
    heading3: /* @__PURE__ */ t(heading),
    heading4: /* @__PURE__ */ t(heading),
    heading5: /* @__PURE__ */ t(heading),
    heading6: /* @__PURE__ */ t(heading),
    contentSeparator: /* @__PURE__ */ t(content),
    list: /* @__PURE__ */ t(content),
    quote: /* @__PURE__ */ t(content),
    emphasis: /* @__PURE__ */ t(content),
    strong: /* @__PURE__ */ t(content),
    link: /* @__PURE__ */ t(content),
    monospace: /* @__PURE__ */ t(content),
    strikethrough: /* @__PURE__ */ t(content),
    inserted: /* @__PURE__ */ t(),
    deleted: /* @__PURE__ */ t(),
    changed: /* @__PURE__ */ t(),
    invalid: /* @__PURE__ */ t(),
    meta,
    documentMeta: /* @__PURE__ */ t(meta),
    annotation: /* @__PURE__ */ t(meta),
    processingInstruction: /* @__PURE__ */ t(meta),
    definition: /* @__PURE__ */ Tag.defineModifier(),
    constant: /* @__PURE__ */ Tag.defineModifier(),
    function: /* @__PURE__ */ Tag.defineModifier(),
    standard: /* @__PURE__ */ Tag.defineModifier(),
    local: /* @__PURE__ */ Tag.defineModifier(),
    special: /* @__PURE__ */ Tag.defineModifier()
  };
  var defaultHighlightStyle = /* @__PURE__ */ HighlightStyle.define([
    {
      tag: tags.link,
      textDecoration: "underline"
    },
    {
      tag: tags.heading,
      textDecoration: "underline",
      fontWeight: "bold"
    },
    {
      tag: tags.emphasis,
      fontStyle: "italic"
    },
    {
      tag: tags.strong,
      fontWeight: "bold"
    },
    {
      tag: tags.strikethrough,
      textDecoration: "line-through"
    },
    {
      tag: tags.keyword,
      color: "#708"
    },
    {
      tag: [tags.atom, tags.bool, tags.url, tags.contentSeparator, tags.labelName],
      color: "#219"
    },
    {
      tag: [tags.literal, tags.inserted],
      color: "#164"
    },
    {
      tag: [tags.string, tags.deleted],
      color: "#a11"
    },
    {
      tag: [tags.regexp, tags.escape, /* @__PURE__ */ tags.special(tags.string)],
      color: "#e40"
    },
    {
      tag: /* @__PURE__ */ tags.definition(tags.variableName),
      color: "#00f"
    },
    {
      tag: /* @__PURE__ */ tags.local(tags.variableName),
      color: "#30a"
    },
    {
      tag: [tags.typeName, tags.namespace],
      color: "#085"
    },
    {
      tag: tags.className,
      color: "#167"
    },
    {
      tag: [/* @__PURE__ */ tags.special(tags.variableName), tags.macroName],
      color: "#256"
    },
    {
      tag: /* @__PURE__ */ tags.definition(tags.propertyName),
      color: "#00c"
    },
    {
      tag: tags.comment,
      color: "#940"
    },
    {
      tag: tags.meta,
      color: "#7a757a"
    },
    {
      tag: tags.invalid,
      color: "#f00"
    }
  ]);
  var classHighlightStyle = /* @__PURE__ */ HighlightStyle.define([
    { tag: tags.link, class: "cmt-link" },
    { tag: tags.heading, class: "cmt-heading" },
    { tag: tags.emphasis, class: "cmt-emphasis" },
    { tag: tags.strong, class: "cmt-strong" },
    { tag: tags.keyword, class: "cmt-keyword" },
    { tag: tags.atom, class: "cmt-atom" },
    { tag: tags.bool, class: "cmt-bool" },
    { tag: tags.url, class: "cmt-url" },
    { tag: tags.labelName, class: "cmt-labelName" },
    { tag: tags.inserted, class: "cmt-inserted" },
    { tag: tags.deleted, class: "cmt-deleted" },
    { tag: tags.literal, class: "cmt-literal" },
    { tag: tags.string, class: "cmt-string" },
    { tag: tags.number, class: "cmt-number" },
    { tag: [tags.regexp, tags.escape, /* @__PURE__ */ tags.special(tags.string)], class: "cmt-string2" },
    { tag: tags.variableName, class: "cmt-variableName" },
    { tag: /* @__PURE__ */ tags.local(tags.variableName), class: "cmt-variableName cmt-local" },
    { tag: /* @__PURE__ */ tags.definition(tags.variableName), class: "cmt-variableName cmt-definition" },
    { tag: /* @__PURE__ */ tags.special(tags.variableName), class: "cmt-variableName2" },
    { tag: /* @__PURE__ */ tags.definition(tags.propertyName), class: "cmt-propertyName cmt-definition" },
    { tag: tags.typeName, class: "cmt-typeName" },
    { tag: tags.namespace, class: "cmt-namespace" },
    { tag: tags.className, class: "cmt-className" },
    { tag: tags.macroName, class: "cmt-macroName" },
    { tag: tags.propertyName, class: "cmt-propertyName" },
    { tag: tags.operator, class: "cmt-operator" },
    { tag: tags.comment, class: "cmt-comment" },
    { tag: tags.meta, class: "cmt-meta" },
    { tag: tags.invalid, class: "cmt-invalid" },
    { tag: tags.punctuation, class: "cmt-punctuation" }
  ]);

  // node_modules/@codemirror/lint/dist/index.js
  var SelectedDiagnostic = class {
    constructor(from2, to, diagnostic) {
      this.from = from2;
      this.to = to;
      this.diagnostic = diagnostic;
    }
  };
  var LintState = class {
    constructor(diagnostics, panel, selected) {
      this.diagnostics = diagnostics;
      this.panel = panel;
      this.selected = selected;
    }
    static init(diagnostics, panel, state) {
      let ranges = Decoration.set(diagnostics.map((d) => {
        return d.from == d.to || d.from == d.to - 1 && state.doc.lineAt(d.from).to == d.from ? Decoration.widget({
          widget: new DiagnosticWidget(d),
          diagnostic: d
        }).range(d.from) : Decoration.mark({
          attributes: { class: "cm-lintRange cm-lintRange-" + d.severity },
          diagnostic: d
        }).range(d.from, d.to);
      }), true);
      return new LintState(ranges, panel, findDiagnostic(ranges));
    }
  };
  function findDiagnostic(diagnostics, diagnostic = null, after = 0) {
    let found = null;
    diagnostics.between(after, 1e9, (from2, to, { spec }) => {
      if (diagnostic && spec.diagnostic != diagnostic)
        return;
      found = new SelectedDiagnostic(from2, to, spec.diagnostic);
      return false;
    });
    return found;
  }
  function maybeEnableLint(state, effects) {
    return state.field(lintState, false) ? effects : effects.concat(StateEffect.appendConfig.of([
      lintState,
      EditorView.decorations.compute([lintState], (state2) => {
        let { selected, panel } = state2.field(lintState);
        return !selected || !panel || selected.from == selected.to ? Decoration.none : Decoration.set([
          activeMark.range(selected.from, selected.to)
        ]);
      }),
      hoverTooltip(lintTooltip),
      baseTheme9
    ]));
  }
  var setDiagnosticsEffect = /* @__PURE__ */ StateEffect.define();
  var togglePanel2 = /* @__PURE__ */ StateEffect.define();
  var movePanelSelection = /* @__PURE__ */ StateEffect.define();
  var lintState = /* @__PURE__ */ StateField.define({
    create() {
      return new LintState(Decoration.none, null, null);
    },
    update(value, tr) {
      if (tr.docChanged) {
        let mapped = value.diagnostics.map(tr.changes), selected = null;
        if (value.selected) {
          let selPos = tr.changes.mapPos(value.selected.from, 1);
          selected = findDiagnostic(mapped, value.selected.diagnostic, selPos) || findDiagnostic(mapped, null, selPos);
        }
        value = new LintState(mapped, value.panel, selected);
      }
      for (let effect of tr.effects) {
        if (effect.is(setDiagnosticsEffect)) {
          value = LintState.init(effect.value, value.panel, tr.state);
        } else if (effect.is(togglePanel2)) {
          value = new LintState(value.diagnostics, effect.value ? LintPanel.open : null, value.selected);
        } else if (effect.is(movePanelSelection)) {
          value = new LintState(value.diagnostics, value.panel, effect.value);
        }
      }
      return value;
    },
    provide: (f) => [
      showPanel.from(f, (val) => val.panel),
      EditorView.decorations.from(f, (s) => s.diagnostics)
    ]
  });
  var activeMark = /* @__PURE__ */ Decoration.mark({ class: "cm-lintRange cm-lintRange-active" });
  function lintTooltip(view, pos, side) {
    let { diagnostics } = view.state.field(lintState);
    let found = [], stackStart = 2e8, stackEnd = 0;
    diagnostics.between(pos - (side < 0 ? 1 : 0), pos + (side > 0 ? 1 : 0), (from2, to, { spec }) => {
      if (pos >= from2 && pos <= to && (from2 == to || (pos > from2 || side > 0) && (pos < to || side < 0))) {
        found.push(spec.diagnostic);
        stackStart = Math.min(from2, stackStart);
        stackEnd = Math.max(to, stackEnd);
      }
    });
    if (!found.length)
      return null;
    return {
      pos: stackStart,
      end: stackEnd,
      above: view.state.doc.lineAt(stackStart).to < stackEnd,
      create() {
        return { dom: diagnosticsTooltip(view, found) };
      }
    };
  }
  function diagnosticsTooltip(view, diagnostics) {
    return crelt("ul", { class: "cm-tooltip-lint" }, diagnostics.map((d) => renderDiagnostic(view, d, false)));
  }
  var openLintPanel = (view) => {
    let field = view.state.field(lintState, false);
    if (!field || !field.panel)
      view.dispatch({ effects: maybeEnableLint(view.state, [togglePanel2.of(true)]) });
    let panel = getPanel(view, LintPanel.open);
    if (panel)
      panel.dom.querySelector(".cm-panel-lint ul").focus();
    return true;
  };
  var closeLintPanel = (view) => {
    let field = view.state.field(lintState, false);
    if (!field || !field.panel)
      return false;
    view.dispatch({ effects: togglePanel2.of(false) });
    return true;
  };
  var nextDiagnostic = (view) => {
    let field = view.state.field(lintState, false);
    if (!field)
      return false;
    let sel = view.state.selection.main, next = field.diagnostics.iter(sel.to + 1);
    if (!next.value) {
      next = field.diagnostics.iter(0);
      if (!next.value || next.from == sel.from && next.to == sel.to)
        return false;
    }
    view.dispatch({ selection: { anchor: next.from, head: next.to }, scrollIntoView: true });
    return true;
  };
  var lintKeymap = [
    { key: "Mod-Shift-m", run: openLintPanel },
    { key: "F8", run: nextDiagnostic }
  ];
  function assignKeys(actions) {
    let assigned = [];
    if (actions)
      actions:
        for (let { name: name2 } of actions) {
          for (let i = 0; i < name2.length; i++) {
            let ch = name2[i];
            if (/[a-zA-Z]/.test(ch) && !assigned.some((c) => c.toLowerCase() == ch.toLowerCase())) {
              assigned.push(ch);
              continue actions;
            }
          }
          assigned.push("");
        }
    return assigned;
  }
  function renderDiagnostic(view, diagnostic, inPanel) {
    var _a2;
    let keys2 = inPanel ? assignKeys(diagnostic.actions) : [];
    return crelt("li", { class: "cm-diagnostic cm-diagnostic-" + diagnostic.severity }, crelt("span", { class: "cm-diagnosticText" }, diagnostic.message), (_a2 = diagnostic.actions) === null || _a2 === void 0 ? void 0 : _a2.map((action, i) => {
      let click = (e) => {
        e.preventDefault();
        let found = findDiagnostic(view.state.field(lintState).diagnostics, diagnostic);
        if (found)
          action.apply(view, found.from, found.to);
      };
      let { name: name2 } = action, keyIndex = keys2[i] ? name2.indexOf(keys2[i]) : -1;
      let nameElt = keyIndex < 0 ? name2 : [
        name2.slice(0, keyIndex),
        crelt("u", name2.slice(keyIndex, keyIndex + 1)),
        name2.slice(keyIndex + 1)
      ];
      return crelt("button", {
        type: "button",
        class: "cm-diagnosticAction",
        onclick: click,
        onmousedown: click,
        "aria-label": ` Action: ${name2}${keyIndex < 0 ? "" : ` (access key "${keys2[i]})"`}.`
      }, nameElt);
    }), diagnostic.source && crelt("div", { class: "cm-diagnosticSource" }, diagnostic.source));
  }
  var DiagnosticWidget = class extends WidgetType {
    constructor(diagnostic) {
      super();
      this.diagnostic = diagnostic;
    }
    eq(other) {
      return other.diagnostic == this.diagnostic;
    }
    toDOM() {
      return crelt("span", { class: "cm-lintPoint cm-lintPoint-" + this.diagnostic.severity });
    }
  };
  var PanelItem = class {
    constructor(view, diagnostic) {
      this.diagnostic = diagnostic;
      this.id = "item_" + Math.floor(Math.random() * 4294967295).toString(16);
      this.dom = renderDiagnostic(view, diagnostic, true);
      this.dom.id = this.id;
      this.dom.setAttribute("role", "option");
    }
  };
  var LintPanel = class {
    constructor(view) {
      this.view = view;
      this.items = [];
      let onkeydown = (event) => {
        if (event.keyCode == 27) {
          closeLintPanel(this.view);
          this.view.focus();
        } else if (event.keyCode == 38 || event.keyCode == 33) {
          this.moveSelection((this.selectedIndex - 1 + this.items.length) % this.items.length);
        } else if (event.keyCode == 40 || event.keyCode == 34) {
          this.moveSelection((this.selectedIndex + 1) % this.items.length);
        } else if (event.keyCode == 36) {
          this.moveSelection(0);
        } else if (event.keyCode == 35) {
          this.moveSelection(this.items.length - 1);
        } else if (event.keyCode == 13) {
          this.view.focus();
        } else if (event.keyCode >= 65 && event.keyCode <= 90 && this.selectedIndex >= 0) {
          let { diagnostic } = this.items[this.selectedIndex], keys2 = assignKeys(diagnostic.actions);
          for (let i = 0; i < keys2.length; i++)
            if (keys2[i].toUpperCase().charCodeAt(0) == event.keyCode) {
              let found = findDiagnostic(this.view.state.field(lintState).diagnostics, diagnostic);
              if (found)
                diagnostic.actions[i].apply(view, found.from, found.to);
            }
        } else {
          return;
        }
        event.preventDefault();
      };
      let onclick = (event) => {
        for (let i = 0; i < this.items.length; i++) {
          if (this.items[i].dom.contains(event.target))
            this.moveSelection(i);
        }
      };
      this.list = crelt("ul", {
        tabIndex: 0,
        role: "listbox",
        "aria-label": this.view.state.phrase("Diagnostics"),
        onkeydown,
        onclick
      });
      this.dom = crelt("div", { class: "cm-panel-lint" }, this.list, crelt("button", {
        type: "button",
        name: "close",
        "aria-label": this.view.state.phrase("close"),
        onclick: () => closeLintPanel(this.view)
      }, "\xD7"));
      this.update();
    }
    get selectedIndex() {
      let selected = this.view.state.field(lintState).selected;
      if (!selected)
        return -1;
      for (let i = 0; i < this.items.length; i++)
        if (this.items[i].diagnostic == selected.diagnostic)
          return i;
      return -1;
    }
    update() {
      let { diagnostics, selected } = this.view.state.field(lintState);
      let i = 0, needsSync = false, newSelectedItem = null;
      diagnostics.between(0, this.view.state.doc.length, (_start, _end, { spec }) => {
        let found = -1, item;
        for (let j = i; j < this.items.length; j++)
          if (this.items[j].diagnostic == spec.diagnostic) {
            found = j;
            break;
          }
        if (found < 0) {
          item = new PanelItem(this.view, spec.diagnostic);
          this.items.splice(i, 0, item);
          needsSync = true;
        } else {
          item = this.items[found];
          if (found > i) {
            this.items.splice(i, found - i);
            needsSync = true;
          }
        }
        if (selected && item.diagnostic == selected.diagnostic) {
          if (!item.dom.hasAttribute("aria-selected")) {
            item.dom.setAttribute("aria-selected", "true");
            newSelectedItem = item;
          }
        } else if (item.dom.hasAttribute("aria-selected")) {
          item.dom.removeAttribute("aria-selected");
        }
        i++;
      });
      while (i < this.items.length && !(this.items.length == 1 && this.items[0].diagnostic.from < 0)) {
        needsSync = true;
        this.items.pop();
      }
      if (this.items.length == 0) {
        this.items.push(new PanelItem(this.view, {
          from: -1,
          to: -1,
          severity: "info",
          message: this.view.state.phrase("No diagnostics")
        }));
        needsSync = true;
      }
      if (newSelectedItem) {
        this.list.setAttribute("aria-activedescendant", newSelectedItem.id);
        this.view.requestMeasure({
          key: this,
          read: () => ({ sel: newSelectedItem.dom.getBoundingClientRect(), panel: this.list.getBoundingClientRect() }),
          write: ({ sel, panel }) => {
            if (sel.top < panel.top)
              this.list.scrollTop -= panel.top - sel.top;
            else if (sel.bottom > panel.bottom)
              this.list.scrollTop += sel.bottom - panel.bottom;
          }
        });
      } else if (this.selectedIndex < 0) {
        this.list.removeAttribute("aria-activedescendant");
      }
      if (needsSync)
        this.sync();
    }
    sync() {
      let domPos = this.list.firstChild;
      function rm3() {
        let prev = domPos;
        domPos = prev.nextSibling;
        prev.remove();
      }
      for (let item of this.items) {
        if (item.dom.parentNode == this.list) {
          while (domPos != item.dom)
            rm3();
          domPos = item.dom.nextSibling;
        } else {
          this.list.insertBefore(item.dom, domPos);
        }
      }
      while (domPos)
        rm3();
    }
    moveSelection(selectedIndex) {
      if (this.selectedIndex < 0)
        return;
      let field = this.view.state.field(lintState);
      let selection = findDiagnostic(field.diagnostics, this.items[selectedIndex].diagnostic);
      if (!selection)
        return;
      this.view.dispatch({
        selection: { anchor: selection.from, head: selection.to },
        scrollIntoView: true,
        effects: movePanelSelection.of(selection)
      });
    }
    static open(view) {
      return new LintPanel(view);
    }
  };
  function svg(content2, attrs = `viewBox="0 0 40 40"`) {
    return `url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" ${attrs}>${encodeURIComponent(content2)}</svg>')`;
  }
  function underline(color) {
    return svg(`<path d="m0 2.5 l2 -1.5 l1 0 l2 1.5 l1 0" stroke="${color}" fill="none" stroke-width=".7"/>`, `width="6" height="3"`);
  }
  var baseTheme9 = /* @__PURE__ */ EditorView.baseTheme({
    ".cm-diagnostic": {
      padding: "3px 6px 3px 8px",
      marginLeft: "-1px",
      display: "block",
      whiteSpace: "pre-wrap"
    },
    ".cm-diagnostic-error": { borderLeft: "5px solid #d11" },
    ".cm-diagnostic-warning": { borderLeft: "5px solid orange" },
    ".cm-diagnostic-info": { borderLeft: "5px solid #999" },
    ".cm-diagnosticAction": {
      font: "inherit",
      border: "none",
      padding: "2px 4px",
      backgroundColor: "#444",
      color: "white",
      borderRadius: "3px",
      marginLeft: "8px"
    },
    ".cm-diagnosticSource": {
      fontSize: "70%",
      opacity: 0.7
    },
    ".cm-lintRange": {
      backgroundPosition: "left bottom",
      backgroundRepeat: "repeat-x",
      paddingBottom: "0.7px"
    },
    ".cm-lintRange-error": { backgroundImage: /* @__PURE__ */ underline("#d11") },
    ".cm-lintRange-warning": { backgroundImage: /* @__PURE__ */ underline("orange") },
    ".cm-lintRange-info": { backgroundImage: /* @__PURE__ */ underline("#999") },
    ".cm-lintRange-active": { backgroundColor: "#ffdd9980" },
    ".cm-tooltip-lint": {
      padding: 0,
      margin: 0
    },
    ".cm-lintPoint": {
      position: "relative",
      "&:after": {
        content: '""',
        position: "absolute",
        bottom: 0,
        left: "-2px",
        borderLeft: "3px solid transparent",
        borderRight: "3px solid transparent",
        borderBottom: "4px solid #d11"
      }
    },
    ".cm-lintPoint-warning": {
      "&:after": { borderBottomColor: "orange" }
    },
    ".cm-lintPoint-info": {
      "&:after": { borderBottomColor: "#999" }
    },
    ".cm-panel.cm-panel-lint": {
      position: "relative",
      "& ul": {
        maxHeight: "100px",
        overflowY: "auto",
        "& [aria-selected]": {
          backgroundColor: "#ddd",
          "& u": { textDecoration: "underline" }
        },
        "&:focus [aria-selected]": {
          background_fallback: "#bdf",
          backgroundColor: "Highlight",
          color_fallback: "white",
          color: "HighlightText"
        },
        "& u": { textDecoration: "none" },
        padding: 0,
        margin: 0
      },
      "& [name=close]": {
        position: "absolute",
        top: "0",
        right: "2px",
        background: "inherit",
        border: "none",
        font: "inherit",
        padding: 0,
        margin: 0
      }
    }
  });

  // node_modules/@codemirror/basic-setup/dist/index.js
  var basicSetup = [
    /* @__PURE__ */ lineNumbers(),
    /* @__PURE__ */ highlightActiveLineGutter(),
    /* @__PURE__ */ highlightSpecialChars(),
    /* @__PURE__ */ history(),
    /* @__PURE__ */ foldGutter(),
    /* @__PURE__ */ drawSelection(),
    /* @__PURE__ */ dropCursor(),
    /* @__PURE__ */ EditorState.allowMultipleSelections.of(true),
    /* @__PURE__ */ indentOnInput(),
    defaultHighlightStyle.fallback,
    /* @__PURE__ */ bracketMatching(),
    /* @__PURE__ */ closeBrackets(),
    /* @__PURE__ */ autocompletion(),
    /* @__PURE__ */ rectangularSelection(),
    /* @__PURE__ */ crosshairCursor(),
    /* @__PURE__ */ highlightActiveLine(),
    /* @__PURE__ */ highlightSelectionMatches(),
    /* @__PURE__ */ keymap.of([
      ...closeBracketsKeymap,
      ...defaultKeymap,
      ...searchKeymap,
      ...historyKeymap,
      ...foldKeymap,
      ...commentKeymap,
      ...completionKeymap,
      ...lintKeymap
    ])
  ];

  // node_modules/@lezer/lr/dist/index.js
  var Stack = class {
    constructor(p, stack, state, reducePos, pos, score2, buffer, bufferBase, curContext, lookAhead = 0, parent) {
      this.p = p;
      this.stack = stack;
      this.state = state;
      this.reducePos = reducePos;
      this.pos = pos;
      this.score = score2;
      this.buffer = buffer;
      this.bufferBase = bufferBase;
      this.curContext = curContext;
      this.lookAhead = lookAhead;
      this.parent = parent;
    }
    toString() {
      return `[${this.stack.filter((_, i) => i % 3 == 0).concat(this.state)}]@${this.pos}${this.score ? "!" + this.score : ""}`;
    }
    static start(p, state, pos = 0) {
      let cx = p.parser.context;
      return new Stack(p, [], state, pos, pos, 0, [], 0, cx ? new StackContext(cx, cx.start) : null, 0, null);
    }
    get context() {
      return this.curContext ? this.curContext.context : null;
    }
    pushState(state, start) {
      this.stack.push(this.state, start, this.bufferBase + this.buffer.length);
      this.state = state;
    }
    reduce(action) {
      let depth = action >> 19, type = action & 65535;
      let { parser: parser6 } = this.p;
      let dPrec = parser6.dynamicPrecedence(type);
      if (dPrec)
        this.score += dPrec;
      if (depth == 0) {
        this.pushState(parser6.getGoto(this.state, type, true), this.reducePos);
        if (type < parser6.minRepeatTerm)
          this.storeNode(type, this.reducePos, this.reducePos, 4, true);
        this.reduceContext(type, this.reducePos);
        return;
      }
      let base2 = this.stack.length - (depth - 1) * 3 - (action & 262144 ? 6 : 0);
      let start = this.stack[base2 - 2];
      let bufferBase = this.stack[base2 - 1], count = this.bufferBase + this.buffer.length - bufferBase;
      if (type < parser6.minRepeatTerm || action & 131072) {
        let pos = parser6.stateFlag(this.state, 1) ? this.pos : this.reducePos;
        this.storeNode(type, start, pos, count + 4, true);
      }
      if (action & 262144) {
        this.state = this.stack[base2];
      } else {
        let baseStateID = this.stack[base2 - 3];
        this.state = parser6.getGoto(baseStateID, type, true);
      }
      while (this.stack.length > base2)
        this.stack.pop();
      this.reduceContext(type, start);
    }
    storeNode(term, start, end, size = 4, isReduce = false) {
      if (term == 0) {
        let cur2 = this, top2 = this.buffer.length;
        if (top2 == 0 && cur2.parent) {
          top2 = cur2.bufferBase - cur2.parent.bufferBase;
          cur2 = cur2.parent;
        }
        if (top2 > 0 && cur2.buffer[top2 - 4] == 0 && cur2.buffer[top2 - 1] > -1) {
          if (start == end)
            return;
          if (cur2.buffer[top2 - 2] >= start) {
            cur2.buffer[top2 - 2] = end;
            return;
          }
        }
      }
      if (!isReduce || this.pos == end) {
        this.buffer.push(term, start, end, size);
      } else {
        let index = this.buffer.length;
        if (index > 0 && this.buffer[index - 4] != 0)
          while (index > 0 && this.buffer[index - 2] > end) {
            this.buffer[index] = this.buffer[index - 4];
            this.buffer[index + 1] = this.buffer[index - 3];
            this.buffer[index + 2] = this.buffer[index - 2];
            this.buffer[index + 3] = this.buffer[index - 1];
            index -= 4;
            if (size > 4)
              size -= 4;
          }
        this.buffer[index] = term;
        this.buffer[index + 1] = start;
        this.buffer[index + 2] = end;
        this.buffer[index + 3] = size;
      }
    }
    shift(action, next, nextEnd) {
      let start = this.pos;
      if (action & 131072) {
        this.pushState(action & 65535, this.pos);
      } else if ((action & 262144) == 0) {
        let nextState = action, { parser: parser6 } = this.p;
        if (nextEnd > this.pos || next <= parser6.maxNode) {
          this.pos = nextEnd;
          if (!parser6.stateFlag(nextState, 1))
            this.reducePos = nextEnd;
        }
        this.pushState(nextState, start);
        this.shiftContext(next, start);
        if (next <= parser6.maxNode)
          this.buffer.push(next, start, nextEnd, 4);
      } else {
        this.pos = nextEnd;
        this.shiftContext(next, start);
        if (next <= this.p.parser.maxNode)
          this.buffer.push(next, start, nextEnd, 4);
      }
    }
    apply(action, next, nextEnd) {
      if (action & 65536)
        this.reduce(action);
      else
        this.shift(action, next, nextEnd);
    }
    useNode(value, next) {
      let index = this.p.reused.length - 1;
      if (index < 0 || this.p.reused[index] != value) {
        this.p.reused.push(value);
        index++;
      }
      let start = this.pos;
      this.reducePos = this.pos = start + value.length;
      this.pushState(next, start);
      this.buffer.push(index, start, this.reducePos, -1);
      if (this.curContext)
        this.updateContext(this.curContext.tracker.reuse(this.curContext.context, value, this, this.p.stream.reset(this.pos - value.length)));
    }
    split() {
      let parent = this;
      let off = parent.buffer.length;
      while (off > 0 && parent.buffer[off - 2] > parent.reducePos)
        off -= 4;
      let buffer = parent.buffer.slice(off), base2 = parent.bufferBase + off;
      while (parent && base2 == parent.bufferBase)
        parent = parent.parent;
      return new Stack(this.p, this.stack.slice(), this.state, this.reducePos, this.pos, this.score, buffer, base2, this.curContext, this.lookAhead, parent);
    }
    recoverByDelete(next, nextEnd) {
      let isNode = next <= this.p.parser.maxNode;
      if (isNode)
        this.storeNode(next, this.pos, nextEnd, 4);
      this.storeNode(0, this.pos, nextEnd, isNode ? 8 : 4);
      this.pos = this.reducePos = nextEnd;
      this.score -= 190;
    }
    canShift(term) {
      for (let sim = new SimulatedStack(this); ; ) {
        let action = this.p.parser.stateSlot(sim.state, 4) || this.p.parser.hasAction(sim.state, term);
        if ((action & 65536) == 0)
          return true;
        if (action == 0)
          return false;
        sim.reduce(action);
      }
    }
    recoverByInsert(next) {
      if (this.stack.length >= 300)
        return [];
      let nextStates = this.p.parser.nextStates(this.state);
      if (nextStates.length > 4 << 1 || this.stack.length >= 120) {
        let best = [];
        for (let i = 0, s; i < nextStates.length; i += 2) {
          if ((s = nextStates[i + 1]) != this.state && this.p.parser.hasAction(s, next))
            best.push(nextStates[i], s);
        }
        if (this.stack.length < 120)
          for (let i = 0; best.length < 4 << 1 && i < nextStates.length; i += 2) {
            let s = nextStates[i + 1];
            if (!best.some((v, i2) => i2 & 1 && v == s))
              best.push(nextStates[i], s);
          }
        nextStates = best;
      }
      let result = [];
      for (let i = 0; i < nextStates.length && result.length < 4; i += 2) {
        let s = nextStates[i + 1];
        if (s == this.state)
          continue;
        let stack = this.split();
        stack.storeNode(0, stack.pos, stack.pos, 4, true);
        stack.pushState(s, this.pos);
        stack.shiftContext(nextStates[i], this.pos);
        stack.score -= 200;
        result.push(stack);
      }
      return result;
    }
    forceReduce() {
      let reduce = this.p.parser.stateSlot(this.state, 5);
      if ((reduce & 65536) == 0)
        return false;
      let { parser: parser6 } = this.p;
      if (!parser6.validAction(this.state, reduce)) {
        let depth = reduce >> 19, term = reduce & 65535;
        let target = this.stack.length - depth * 3;
        if (target < 0 || parser6.getGoto(this.stack[target], term, false) < 0)
          return false;
        this.storeNode(0, this.reducePos, this.reducePos, 4, true);
        this.score -= 100;
      }
      this.reduce(reduce);
      return true;
    }
    forceAll() {
      while (!this.p.parser.stateFlag(this.state, 2)) {
        if (!this.forceReduce()) {
          this.storeNode(0, this.pos, this.pos, 4, true);
          break;
        }
      }
      return this;
    }
    get deadEnd() {
      if (this.stack.length != 3)
        return false;
      let { parser: parser6 } = this.p;
      return parser6.data[parser6.stateSlot(this.state, 1)] == 65535 && !parser6.stateSlot(this.state, 4);
    }
    restart() {
      this.state = this.stack[0];
      this.stack.length = 0;
    }
    sameState(other) {
      if (this.state != other.state || this.stack.length != other.stack.length)
        return false;
      for (let i = 0; i < this.stack.length; i += 3)
        if (this.stack[i] != other.stack[i])
          return false;
      return true;
    }
    get parser() {
      return this.p.parser;
    }
    dialectEnabled(dialectID) {
      return this.p.parser.dialect.flags[dialectID];
    }
    shiftContext(term, start) {
      if (this.curContext)
        this.updateContext(this.curContext.tracker.shift(this.curContext.context, term, this, this.p.stream.reset(start)));
    }
    reduceContext(term, start) {
      if (this.curContext)
        this.updateContext(this.curContext.tracker.reduce(this.curContext.context, term, this, this.p.stream.reset(start)));
    }
    emitContext() {
      let last = this.buffer.length - 1;
      if (last < 0 || this.buffer[last] != -3)
        this.buffer.push(this.curContext.hash, this.reducePos, this.reducePos, -3);
    }
    emitLookAhead() {
      let last = this.buffer.length - 1;
      if (last < 0 || this.buffer[last] != -4)
        this.buffer.push(this.lookAhead, this.reducePos, this.reducePos, -4);
    }
    updateContext(context) {
      if (context != this.curContext.context) {
        let newCx = new StackContext(this.curContext.tracker, context);
        if (newCx.hash != this.curContext.hash)
          this.emitContext();
        this.curContext = newCx;
      }
    }
    setLookAhead(lookAhead) {
      if (lookAhead > this.lookAhead) {
        this.emitLookAhead();
        this.lookAhead = lookAhead;
      }
    }
    close() {
      if (this.curContext && this.curContext.tracker.strict)
        this.emitContext();
      if (this.lookAhead > 0)
        this.emitLookAhead();
    }
  };
  var StackContext = class {
    constructor(tracker, context) {
      this.tracker = tracker;
      this.context = context;
      this.hash = tracker.strict ? tracker.hash(context) : 0;
    }
  };
  var Recover;
  (function(Recover2) {
    Recover2[Recover2["Insert"] = 200] = "Insert";
    Recover2[Recover2["Delete"] = 190] = "Delete";
    Recover2[Recover2["Reduce"] = 100] = "Reduce";
    Recover2[Recover2["MaxNext"] = 4] = "MaxNext";
    Recover2[Recover2["MaxInsertStackDepth"] = 300] = "MaxInsertStackDepth";
    Recover2[Recover2["DampenInsertStackDepth"] = 120] = "DampenInsertStackDepth";
  })(Recover || (Recover = {}));
  var SimulatedStack = class {
    constructor(start) {
      this.start = start;
      this.state = start.state;
      this.stack = start.stack;
      this.base = this.stack.length;
    }
    reduce(action) {
      let term = action & 65535, depth = action >> 19;
      if (depth == 0) {
        if (this.stack == this.start.stack)
          this.stack = this.stack.slice();
        this.stack.push(this.state, 0, 0);
        this.base += 3;
      } else {
        this.base -= (depth - 1) * 3;
      }
      let goto2 = this.start.p.parser.getGoto(this.stack[this.base - 3], term, true);
      this.state = goto2;
    }
  };
  var StackBufferCursor = class {
    constructor(stack, pos, index) {
      this.stack = stack;
      this.pos = pos;
      this.index = index;
      this.buffer = stack.buffer;
      if (this.index == 0)
        this.maybeNext();
    }
    static create(stack, pos = stack.bufferBase + stack.buffer.length) {
      return new StackBufferCursor(stack, pos, pos - stack.bufferBase);
    }
    maybeNext() {
      let next = this.stack.parent;
      if (next != null) {
        this.index = this.stack.bufferBase - next.bufferBase;
        this.stack = next;
        this.buffer = next.buffer;
      }
    }
    get id() {
      return this.buffer[this.index - 4];
    }
    get start() {
      return this.buffer[this.index - 3];
    }
    get end() {
      return this.buffer[this.index - 2];
    }
    get size() {
      return this.buffer[this.index - 1];
    }
    next() {
      this.index -= 4;
      this.pos -= 4;
      if (this.index == 0)
        this.maybeNext();
    }
    fork() {
      return new StackBufferCursor(this.stack, this.pos, this.index);
    }
  };
  var CachedToken = class {
    constructor() {
      this.start = -1;
      this.value = -1;
      this.end = -1;
      this.extended = -1;
      this.lookAhead = 0;
      this.mask = 0;
      this.context = 0;
    }
  };
  var nullToken = new CachedToken();
  var InputStream = class {
    constructor(input, ranges) {
      this.input = input;
      this.ranges = ranges;
      this.chunk = "";
      this.chunkOff = 0;
      this.chunk2 = "";
      this.chunk2Pos = 0;
      this.next = -1;
      this.token = nullToken;
      this.rangeIndex = 0;
      this.pos = this.chunkPos = ranges[0].from;
      this.range = ranges[0];
      this.end = ranges[ranges.length - 1].to;
      this.readNext();
    }
    resolveOffset(offset, assoc) {
      let range = this.range, index = this.rangeIndex;
      let pos = this.pos + offset;
      while (pos < range.from) {
        if (!index)
          return null;
        let next = this.ranges[--index];
        pos -= range.from - next.to;
        range = next;
      }
      while (assoc < 0 ? pos > range.to : pos >= range.to) {
        if (index == this.ranges.length - 1)
          return null;
        let next = this.ranges[++index];
        pos += next.from - range.to;
        range = next;
      }
      return pos;
    }
    peek(offset) {
      let idx = this.chunkOff + offset, pos, result;
      if (idx >= 0 && idx < this.chunk.length) {
        pos = this.pos + offset;
        result = this.chunk.charCodeAt(idx);
      } else {
        let resolved = this.resolveOffset(offset, 1);
        if (resolved == null)
          return -1;
        pos = resolved;
        if (pos >= this.chunk2Pos && pos < this.chunk2Pos + this.chunk2.length) {
          result = this.chunk2.charCodeAt(pos - this.chunk2Pos);
        } else {
          let i = this.rangeIndex, range = this.range;
          while (range.to <= pos)
            range = this.ranges[++i];
          this.chunk2 = this.input.chunk(this.chunk2Pos = pos);
          if (pos + this.chunk2.length > range.to)
            this.chunk2 = this.chunk2.slice(0, range.to - pos);
          result = this.chunk2.charCodeAt(0);
        }
      }
      if (pos >= this.token.lookAhead)
        this.token.lookAhead = pos + 1;
      return result;
    }
    acceptToken(token, endOffset = 0) {
      let end = endOffset ? this.resolveOffset(endOffset, -1) : this.pos;
      if (end == null || end < this.token.start)
        throw new RangeError("Token end out of bounds");
      this.token.value = token;
      this.token.end = end;
    }
    getChunk() {
      if (this.pos >= this.chunk2Pos && this.pos < this.chunk2Pos + this.chunk2.length) {
        let { chunk, chunkPos } = this;
        this.chunk = this.chunk2;
        this.chunkPos = this.chunk2Pos;
        this.chunk2 = chunk;
        this.chunk2Pos = chunkPos;
        this.chunkOff = this.pos - this.chunkPos;
      } else {
        this.chunk2 = this.chunk;
        this.chunk2Pos = this.chunkPos;
        let nextChunk = this.input.chunk(this.pos);
        let end = this.pos + nextChunk.length;
        this.chunk = end > this.range.to ? nextChunk.slice(0, this.range.to - this.pos) : nextChunk;
        this.chunkPos = this.pos;
        this.chunkOff = 0;
      }
    }
    readNext() {
      if (this.chunkOff >= this.chunk.length) {
        this.getChunk();
        if (this.chunkOff == this.chunk.length)
          return this.next = -1;
      }
      return this.next = this.chunk.charCodeAt(this.chunkOff);
    }
    advance(n = 1) {
      this.chunkOff += n;
      while (this.pos + n >= this.range.to) {
        if (this.rangeIndex == this.ranges.length - 1)
          return this.setDone();
        n -= this.range.to - this.pos;
        this.range = this.ranges[++this.rangeIndex];
        this.pos = this.range.from;
      }
      this.pos += n;
      if (this.pos >= this.token.lookAhead)
        this.token.lookAhead = this.pos + 1;
      return this.readNext();
    }
    setDone() {
      this.pos = this.chunkPos = this.end;
      this.range = this.ranges[this.rangeIndex = this.ranges.length - 1];
      this.chunk = "";
      return this.next = -1;
    }
    reset(pos, token) {
      if (token) {
        this.token = token;
        token.start = pos;
        token.lookAhead = pos + 1;
        token.value = token.extended = -1;
      } else {
        this.token = nullToken;
      }
      if (this.pos != pos) {
        this.pos = pos;
        if (pos == this.end) {
          this.setDone();
          return this;
        }
        while (pos < this.range.from)
          this.range = this.ranges[--this.rangeIndex];
        while (pos >= this.range.to)
          this.range = this.ranges[++this.rangeIndex];
        if (pos >= this.chunkPos && pos < this.chunkPos + this.chunk.length) {
          this.chunkOff = pos - this.chunkPos;
        } else {
          this.chunk = "";
          this.chunkOff = 0;
        }
        this.readNext();
      }
      return this;
    }
    read(from2, to) {
      if (from2 >= this.chunkPos && to <= this.chunkPos + this.chunk.length)
        return this.chunk.slice(from2 - this.chunkPos, to - this.chunkPos);
      if (from2 >= this.chunk2Pos && to <= this.chunk2Pos + this.chunk2.length)
        return this.chunk2.slice(from2 - this.chunk2Pos, to - this.chunk2Pos);
      if (from2 >= this.range.from && to <= this.range.to)
        return this.input.read(from2, to);
      let result = "";
      for (let r of this.ranges) {
        if (r.from >= to)
          break;
        if (r.to > from2)
          result += this.input.read(Math.max(r.from, from2), Math.min(r.to, to));
      }
      return result;
    }
  };
  var TokenGroup = class {
    constructor(data, id2) {
      this.data = data;
      this.id = id2;
    }
    token(input, stack) {
      readToken(this.data, input, stack, this.id);
    }
  };
  TokenGroup.prototype.contextual = TokenGroup.prototype.fallback = TokenGroup.prototype.extend = false;
  var ExternalTokenizer = class {
    constructor(token, options = {}) {
      this.token = token;
      this.contextual = !!options.contextual;
      this.fallback = !!options.fallback;
      this.extend = !!options.extend;
    }
  };
  function readToken(data, input, stack, group) {
    let state = 0, groupMask = 1 << group, { parser: parser6 } = stack.p, { dialect } = parser6;
    scan:
      for (; ; ) {
        if ((groupMask & data[state]) == 0)
          break;
        let accEnd = data[state + 1];
        for (let i = state + 3; i < accEnd; i += 2)
          if ((data[i + 1] & groupMask) > 0) {
            let term = data[i];
            if (dialect.allows(term) && (input.token.value == -1 || input.token.value == term || parser6.overrides(term, input.token.value))) {
              input.acceptToken(term);
              break;
            }
          }
        for (let next = input.next, low = 0, high = data[state + 2]; low < high; ) {
          let mid = low + high >> 1;
          let index = accEnd + mid + (mid << 1);
          let from2 = data[index], to = data[index + 1];
          if (next < from2)
            high = mid;
          else if (next >= to)
            low = mid + 1;
          else {
            state = data[index + 2];
            input.advance();
            continue scan;
          }
        }
        break;
      }
  }
  function decodeArray(input, Type = Uint16Array) {
    if (typeof input != "string")
      return input;
    let array2 = null;
    for (let pos = 0, out = 0; pos < input.length; ) {
      let value = 0;
      for (; ; ) {
        let next = input.charCodeAt(pos++), stop = false;
        if (next == 126) {
          value = 65535;
          break;
        }
        if (next >= 92)
          next--;
        if (next >= 34)
          next--;
        let digit = next - 32;
        if (digit >= 46) {
          digit -= 46;
          stop = true;
        }
        value += digit;
        if (stop)
          break;
        value *= 46;
      }
      if (array2)
        array2[out++] = value;
      else
        array2 = new Type(value);
    }
    return array2;
  }
  var verbose = typeof process != "undefined" && /\bparse\b/.test(process.env.LOG);
  var stackIDs = null;
  var Safety;
  (function(Safety2) {
    Safety2[Safety2["Margin"] = 25] = "Margin";
  })(Safety || (Safety = {}));
  function cutAt(tree, pos, side) {
    let cursor = tree.fullCursor();
    cursor.moveTo(pos);
    for (; ; ) {
      if (!(side < 0 ? cursor.childBefore(pos) : cursor.childAfter(pos)))
        for (; ; ) {
          if ((side < 0 ? cursor.to < pos : cursor.from > pos) && !cursor.type.isError)
            return side < 0 ? Math.max(0, Math.min(cursor.to - 1, pos - 25)) : Math.min(tree.length, Math.max(cursor.from + 1, pos + 25));
          if (side < 0 ? cursor.prevSibling() : cursor.nextSibling())
            break;
          if (!cursor.parent())
            return side < 0 ? 0 : tree.length;
        }
    }
  }
  var FragmentCursor2 = class {
    constructor(fragments, nodeSet) {
      this.fragments = fragments;
      this.nodeSet = nodeSet;
      this.i = 0;
      this.fragment = null;
      this.safeFrom = -1;
      this.safeTo = -1;
      this.trees = [];
      this.start = [];
      this.index = [];
      this.nextFragment();
    }
    nextFragment() {
      let fr = this.fragment = this.i == this.fragments.length ? null : this.fragments[this.i++];
      if (fr) {
        this.safeFrom = fr.openStart ? cutAt(fr.tree, fr.from + fr.offset, 1) - fr.offset : fr.from;
        this.safeTo = fr.openEnd ? cutAt(fr.tree, fr.to + fr.offset, -1) - fr.offset : fr.to;
        while (this.trees.length) {
          this.trees.pop();
          this.start.pop();
          this.index.pop();
        }
        this.trees.push(fr.tree);
        this.start.push(-fr.offset);
        this.index.push(0);
        this.nextStart = this.safeFrom;
      } else {
        this.nextStart = 1e9;
      }
    }
    nodeAt(pos) {
      if (pos < this.nextStart)
        return null;
      while (this.fragment && this.safeTo <= pos)
        this.nextFragment();
      if (!this.fragment)
        return null;
      for (; ; ) {
        let last = this.trees.length - 1;
        if (last < 0) {
          this.nextFragment();
          return null;
        }
        let top2 = this.trees[last], index = this.index[last];
        if (index == top2.children.length) {
          this.trees.pop();
          this.start.pop();
          this.index.pop();
          continue;
        }
        let next = top2.children[index];
        let start = this.start[last] + top2.positions[index];
        if (start > pos) {
          this.nextStart = start;
          return null;
        }
        if (next instanceof Tree) {
          if (start == pos) {
            if (start < this.safeFrom)
              return null;
            let end = start + next.length;
            if (end <= this.safeTo) {
              let lookAhead = next.prop(NodeProp.lookAhead);
              if (!lookAhead || end + lookAhead < this.fragment.to)
                return next;
            }
          }
          this.index[last]++;
          if (start + next.length >= Math.max(this.safeFrom, pos)) {
            this.trees.push(next);
            this.start.push(start);
            this.index.push(0);
          }
        } else {
          this.index[last]++;
          this.nextStart = start + next.length;
        }
      }
    }
  };
  var TokenCache = class {
    constructor(parser6, stream) {
      this.stream = stream;
      this.tokens = [];
      this.mainToken = null;
      this.actions = [];
      this.tokens = parser6.tokenizers.map((_) => new CachedToken());
    }
    getActions(stack) {
      let actionIndex = 0;
      let main = null;
      let { parser: parser6 } = stack.p, { tokenizers } = parser6;
      let mask = parser6.stateSlot(stack.state, 3);
      let context = stack.curContext ? stack.curContext.hash : 0;
      let lookAhead = 0;
      for (let i = 0; i < tokenizers.length; i++) {
        if ((1 << i & mask) == 0)
          continue;
        let tokenizer = tokenizers[i], token = this.tokens[i];
        if (main && !tokenizer.fallback)
          continue;
        if (tokenizer.contextual || token.start != stack.pos || token.mask != mask || token.context != context) {
          this.updateCachedToken(token, tokenizer, stack);
          token.mask = mask;
          token.context = context;
        }
        if (token.lookAhead > token.end + 25)
          lookAhead = Math.max(token.lookAhead, lookAhead);
        if (token.value != 0) {
          let startIndex = actionIndex;
          if (token.extended > -1)
            actionIndex = this.addActions(stack, token.extended, token.end, actionIndex);
          actionIndex = this.addActions(stack, token.value, token.end, actionIndex);
          if (!tokenizer.extend) {
            main = token;
            if (actionIndex > startIndex)
              break;
          }
        }
      }
      while (this.actions.length > actionIndex)
        this.actions.pop();
      if (lookAhead)
        stack.setLookAhead(lookAhead);
      if (!main && stack.pos == this.stream.end) {
        main = new CachedToken();
        main.value = stack.p.parser.eofTerm;
        main.start = main.end = stack.pos;
        actionIndex = this.addActions(stack, main.value, main.end, actionIndex);
      }
      this.mainToken = main;
      return this.actions;
    }
    getMainToken(stack) {
      if (this.mainToken)
        return this.mainToken;
      let main = new CachedToken(), { pos, p } = stack;
      main.start = pos;
      main.end = Math.min(pos + 1, p.stream.end);
      main.value = pos == p.stream.end ? p.parser.eofTerm : 0;
      return main;
    }
    updateCachedToken(token, tokenizer, stack) {
      tokenizer.token(this.stream.reset(stack.pos, token), stack);
      if (token.value > -1) {
        let { parser: parser6 } = stack.p;
        for (let i = 0; i < parser6.specialized.length; i++)
          if (parser6.specialized[i] == token.value) {
            let result = parser6.specializers[i](this.stream.read(token.start, token.end), stack);
            if (result >= 0 && stack.p.parser.dialect.allows(result >> 1)) {
              if ((result & 1) == 0)
                token.value = result >> 1;
              else
                token.extended = result >> 1;
              break;
            }
          }
      } else {
        token.value = 0;
        token.end = Math.min(stack.p.stream.end, stack.pos + 1);
      }
    }
    putAction(action, token, end, index) {
      for (let i = 0; i < index; i += 3)
        if (this.actions[i] == action)
          return index;
      this.actions[index++] = action;
      this.actions[index++] = token;
      this.actions[index++] = end;
      return index;
    }
    addActions(stack, token, end, index) {
      let { state } = stack, { parser: parser6 } = stack.p, { data } = parser6;
      for (let set = 0; set < 2; set++) {
        for (let i = parser6.stateSlot(state, set ? 2 : 1); ; i += 3) {
          if (data[i] == 65535) {
            if (data[i + 1] == 1) {
              i = pair(data, i + 2);
            } else {
              if (index == 0 && data[i + 1] == 2)
                index = this.putAction(pair(data, i + 2), token, end, index);
              break;
            }
          }
          if (data[i] == token)
            index = this.putAction(pair(data, i + 1), token, end, index);
        }
      }
      return index;
    }
  };
  var Rec;
  (function(Rec2) {
    Rec2[Rec2["Distance"] = 5] = "Distance";
    Rec2[Rec2["MaxRemainingPerStep"] = 3] = "MaxRemainingPerStep";
    Rec2[Rec2["MinBufferLengthPrune"] = 500] = "MinBufferLengthPrune";
    Rec2[Rec2["ForceReduceLimit"] = 10] = "ForceReduceLimit";
    Rec2[Rec2["CutDepth"] = 15e3] = "CutDepth";
    Rec2[Rec2["CutTo"] = 9e3] = "CutTo";
  })(Rec || (Rec = {}));
  var Parse = class {
    constructor(parser6, input, fragments, ranges) {
      this.parser = parser6;
      this.input = input;
      this.ranges = ranges;
      this.recovering = 0;
      this.nextStackID = 9812;
      this.minStackPos = 0;
      this.reused = [];
      this.stoppedAt = null;
      this.stream = new InputStream(input, ranges);
      this.tokens = new TokenCache(parser6, this.stream);
      this.topTerm = parser6.top[1];
      let { from: from2 } = ranges[0];
      this.stacks = [Stack.start(this, parser6.top[0], from2)];
      this.fragments = fragments.length && this.stream.end - from2 > parser6.bufferLength * 4 ? new FragmentCursor2(fragments, parser6.nodeSet) : null;
    }
    get parsedPos() {
      return this.minStackPos;
    }
    advance() {
      let stacks = this.stacks, pos = this.minStackPos;
      let newStacks = this.stacks = [];
      let stopped, stoppedTokens;
      for (let i = 0; i < stacks.length; i++) {
        let stack = stacks[i];
        for (; ; ) {
          this.tokens.mainToken = null;
          if (stack.pos > pos) {
            newStacks.push(stack);
          } else if (this.advanceStack(stack, newStacks, stacks)) {
            continue;
          } else {
            if (!stopped) {
              stopped = [];
              stoppedTokens = [];
            }
            stopped.push(stack);
            let tok = this.tokens.getMainToken(stack);
            stoppedTokens.push(tok.value, tok.end);
          }
          break;
        }
      }
      if (!newStacks.length) {
        let finished = stopped && findFinished(stopped);
        if (finished)
          return this.stackToTree(finished);
        if (this.parser.strict) {
          if (verbose && stopped)
            console.log("Stuck with token " + (this.tokens.mainToken ? this.parser.getName(this.tokens.mainToken.value) : "none"));
          throw new SyntaxError("No parse at " + pos);
        }
        if (!this.recovering)
          this.recovering = 5;
      }
      if (this.recovering && stopped) {
        let finished = this.stoppedAt != null && stopped[0].pos > this.stoppedAt ? stopped[0] : this.runRecovery(stopped, stoppedTokens, newStacks);
        if (finished)
          return this.stackToTree(finished.forceAll());
      }
      if (this.recovering) {
        let maxRemaining = this.recovering == 1 ? 1 : this.recovering * 3;
        if (newStacks.length > maxRemaining) {
          newStacks.sort((a, b) => b.score - a.score);
          while (newStacks.length > maxRemaining)
            newStacks.pop();
        }
        if (newStacks.some((s) => s.reducePos > pos))
          this.recovering--;
      } else if (newStacks.length > 1) {
        outer:
          for (let i = 0; i < newStacks.length - 1; i++) {
            let stack = newStacks[i];
            for (let j = i + 1; j < newStacks.length; j++) {
              let other = newStacks[j];
              if (stack.sameState(other) || stack.buffer.length > 500 && other.buffer.length > 500) {
                if ((stack.score - other.score || stack.buffer.length - other.buffer.length) > 0) {
                  newStacks.splice(j--, 1);
                } else {
                  newStacks.splice(i--, 1);
                  continue outer;
                }
              }
            }
          }
      }
      this.minStackPos = newStacks[0].pos;
      for (let i = 1; i < newStacks.length; i++)
        if (newStacks[i].pos < this.minStackPos)
          this.minStackPos = newStacks[i].pos;
      return null;
    }
    stopAt(pos) {
      if (this.stoppedAt != null && this.stoppedAt < pos)
        throw new RangeError("Can't move stoppedAt forward");
      this.stoppedAt = pos;
    }
    advanceStack(stack, stacks, split) {
      let start = stack.pos, { parser: parser6 } = this;
      let base2 = verbose ? this.stackID(stack) + " -> " : "";
      if (this.stoppedAt != null && start > this.stoppedAt)
        return stack.forceReduce() ? stack : null;
      if (this.fragments) {
        let strictCx = stack.curContext && stack.curContext.tracker.strict, cxHash = strictCx ? stack.curContext.hash : 0;
        for (let cached = this.fragments.nodeAt(start); cached; ) {
          let match2 = this.parser.nodeSet.types[cached.type.id] == cached.type ? parser6.getGoto(stack.state, cached.type.id) : -1;
          if (match2 > -1 && cached.length && (!strictCx || (cached.prop(NodeProp.contextHash) || 0) == cxHash)) {
            stack.useNode(cached, match2);
            if (verbose)
              console.log(base2 + this.stackID(stack) + ` (via reuse of ${parser6.getName(cached.type.id)})`);
            return true;
          }
          if (!(cached instanceof Tree) || cached.children.length == 0 || cached.positions[0] > 0)
            break;
          let inner = cached.children[0];
          if (inner instanceof Tree && cached.positions[0] == 0)
            cached = inner;
          else
            break;
        }
      }
      let defaultReduce = parser6.stateSlot(stack.state, 4);
      if (defaultReduce > 0) {
        stack.reduce(defaultReduce);
        if (verbose)
          console.log(base2 + this.stackID(stack) + ` (via always-reduce ${parser6.getName(defaultReduce & 65535)})`);
        return true;
      }
      if (stack.stack.length >= 15e3) {
        while (stack.stack.length > 9e3 && stack.forceReduce()) {
        }
      }
      let actions = this.tokens.getActions(stack);
      for (let i = 0; i < actions.length; ) {
        let action = actions[i++], term = actions[i++], end = actions[i++];
        let last = i == actions.length || !split;
        let localStack = last ? stack : stack.split();
        localStack.apply(action, term, end);
        if (verbose)
          console.log(base2 + this.stackID(localStack) + ` (via ${(action & 65536) == 0 ? "shift" : `reduce of ${parser6.getName(action & 65535)}`} for ${parser6.getName(term)} @ ${start}${localStack == stack ? "" : ", split"})`);
        if (last)
          return true;
        else if (localStack.pos > start)
          stacks.push(localStack);
        else
          split.push(localStack);
      }
      return false;
    }
    advanceFully(stack, newStacks) {
      let pos = stack.pos;
      for (; ; ) {
        if (!this.advanceStack(stack, null, null))
          return false;
        if (stack.pos > pos) {
          pushStackDedup(stack, newStacks);
          return true;
        }
      }
    }
    runRecovery(stacks, tokens, newStacks) {
      let finished = null, restarted = false;
      for (let i = 0; i < stacks.length; i++) {
        let stack = stacks[i], token = tokens[i << 1], tokenEnd = tokens[(i << 1) + 1];
        let base2 = verbose ? this.stackID(stack) + " -> " : "";
        if (stack.deadEnd) {
          if (restarted)
            continue;
          restarted = true;
          stack.restart();
          if (verbose)
            console.log(base2 + this.stackID(stack) + " (restarted)");
          let done = this.advanceFully(stack, newStacks);
          if (done)
            continue;
        }
        let force = stack.split(), forceBase = base2;
        for (let j = 0; force.forceReduce() && j < 10; j++) {
          if (verbose)
            console.log(forceBase + this.stackID(force) + " (via force-reduce)");
          let done = this.advanceFully(force, newStacks);
          if (done)
            break;
          if (verbose)
            forceBase = this.stackID(force) + " -> ";
        }
        for (let insert2 of stack.recoverByInsert(token)) {
          if (verbose)
            console.log(base2 + this.stackID(insert2) + " (via recover-insert)");
          this.advanceFully(insert2, newStacks);
        }
        if (this.stream.end > stack.pos) {
          if (tokenEnd == stack.pos) {
            tokenEnd++;
            token = 0;
          }
          stack.recoverByDelete(token, tokenEnd);
          if (verbose)
            console.log(base2 + this.stackID(stack) + ` (via recover-delete ${this.parser.getName(token)})`);
          pushStackDedup(stack, newStacks);
        } else if (!finished || finished.score < stack.score) {
          finished = stack;
        }
      }
      return finished;
    }
    stackToTree(stack) {
      stack.close();
      return Tree.build({
        buffer: StackBufferCursor.create(stack),
        nodeSet: this.parser.nodeSet,
        topID: this.topTerm,
        maxBufferLength: this.parser.bufferLength,
        reused: this.reused,
        start: this.ranges[0].from,
        length: stack.pos - this.ranges[0].from,
        minRepeatType: this.parser.minRepeatTerm
      });
    }
    stackID(stack) {
      let id2 = (stackIDs || (stackIDs = /* @__PURE__ */ new WeakMap())).get(stack);
      if (!id2)
        stackIDs.set(stack, id2 = String.fromCodePoint(this.nextStackID++));
      return id2 + stack;
    }
  };
  function pushStackDedup(stack, newStacks) {
    for (let i = 0; i < newStacks.length; i++) {
      let other = newStacks[i];
      if (other.pos == stack.pos && other.sameState(stack)) {
        if (newStacks[i].score < stack.score)
          newStacks[i] = stack;
        return;
      }
    }
    newStacks.push(stack);
  }
  var Dialect = class {
    constructor(source, flags, disabled) {
      this.source = source;
      this.flags = flags;
      this.disabled = disabled;
    }
    allows(term) {
      return !this.disabled || this.disabled[term] == 0;
    }
  };
  var id = (x) => x;
  var ContextTracker = class {
    constructor(spec) {
      this.start = spec.start;
      this.shift = spec.shift || id;
      this.reduce = spec.reduce || id;
      this.reuse = spec.reuse || id;
      this.hash = spec.hash || (() => 0);
      this.strict = spec.strict !== false;
    }
  };
  var LRParser = class extends Parser {
    constructor(spec) {
      super();
      this.wrappers = [];
      if (spec.version != 13)
        throw new RangeError(`Parser version (${spec.version}) doesn't match runtime version (${13})`);
      let nodeNames = spec.nodeNames.split(" ");
      this.minRepeatTerm = nodeNames.length;
      for (let i = 0; i < spec.repeatNodeCount; i++)
        nodeNames.push("");
      let topTerms = Object.keys(spec.topRules).map((r) => spec.topRules[r][1]);
      let nodeProps = [];
      for (let i = 0; i < nodeNames.length; i++)
        nodeProps.push([]);
      function setProp(nodeID, prop, value) {
        nodeProps[nodeID].push([prop, prop.deserialize(String(value))]);
      }
      if (spec.nodeProps)
        for (let propSpec of spec.nodeProps) {
          let prop = propSpec[0];
          for (let i = 1; i < propSpec.length; ) {
            let next = propSpec[i++];
            if (next >= 0) {
              setProp(next, prop, propSpec[i++]);
            } else {
              let value = propSpec[i + -next];
              for (let j = -next; j > 0; j--)
                setProp(propSpec[i++], prop, value);
              i++;
            }
          }
        }
      this.nodeSet = new NodeSet(nodeNames.map((name2, i) => NodeType.define({
        name: i >= this.minRepeatTerm ? void 0 : name2,
        id: i,
        props: nodeProps[i],
        top: topTerms.indexOf(i) > -1,
        error: i == 0,
        skipped: spec.skippedNodes && spec.skippedNodes.indexOf(i) > -1
      })));
      this.strict = false;
      this.bufferLength = DefaultBufferLength;
      let tokenArray = decodeArray(spec.tokenData);
      this.context = spec.context;
      this.specialized = new Uint16Array(spec.specialized ? spec.specialized.length : 0);
      this.specializers = [];
      if (spec.specialized)
        for (let i = 0; i < spec.specialized.length; i++) {
          this.specialized[i] = spec.specialized[i].term;
          this.specializers[i] = spec.specialized[i].get;
        }
      this.states = decodeArray(spec.states, Uint32Array);
      this.data = decodeArray(spec.stateData);
      this.goto = decodeArray(spec.goto);
      this.maxTerm = spec.maxTerm;
      this.tokenizers = spec.tokenizers.map((value) => typeof value == "number" ? new TokenGroup(tokenArray, value) : value);
      this.topRules = spec.topRules;
      this.dialects = spec.dialects || {};
      this.dynamicPrecedences = spec.dynamicPrecedences || null;
      this.tokenPrecTable = spec.tokenPrec;
      this.termNames = spec.termNames || null;
      this.maxNode = this.nodeSet.types.length - 1;
      this.dialect = this.parseDialect();
      this.top = this.topRules[Object.keys(this.topRules)[0]];
    }
    createParse(input, fragments, ranges) {
      let parse = new Parse(this, input, fragments, ranges);
      for (let w of this.wrappers)
        parse = w(parse, input, fragments, ranges);
      return parse;
    }
    getGoto(state, term, loose = false) {
      let table = this.goto;
      if (term >= table[0])
        return -1;
      for (let pos = table[term + 1]; ; ) {
        let groupTag = table[pos++], last = groupTag & 1;
        let target = table[pos++];
        if (last && loose)
          return target;
        for (let end = pos + (groupTag >> 1); pos < end; pos++)
          if (table[pos] == state)
            return target;
        if (last)
          return -1;
      }
    }
    hasAction(state, terminal) {
      let data = this.data;
      for (let set = 0; set < 2; set++) {
        for (let i = this.stateSlot(state, set ? 2 : 1), next; ; i += 3) {
          if ((next = data[i]) == 65535) {
            if (data[i + 1] == 1)
              next = data[i = pair(data, i + 2)];
            else if (data[i + 1] == 2)
              return pair(data, i + 2);
            else
              break;
          }
          if (next == terminal || next == 0)
            return pair(data, i + 1);
        }
      }
      return 0;
    }
    stateSlot(state, slot) {
      return this.states[state * 6 + slot];
    }
    stateFlag(state, flag) {
      return (this.stateSlot(state, 0) & flag) > 0;
    }
    validAction(state, action) {
      if (action == this.stateSlot(state, 4))
        return true;
      for (let i = this.stateSlot(state, 1); ; i += 3) {
        if (this.data[i] == 65535) {
          if (this.data[i + 1] == 1)
            i = pair(this.data, i + 2);
          else
            return false;
        }
        if (action == pair(this.data, i + 1))
          return true;
      }
    }
    nextStates(state) {
      let result = [];
      for (let i = this.stateSlot(state, 1); ; i += 3) {
        if (this.data[i] == 65535) {
          if (this.data[i + 1] == 1)
            i = pair(this.data, i + 2);
          else
            break;
        }
        if ((this.data[i + 2] & 65536 >> 16) == 0) {
          let value = this.data[i + 1];
          if (!result.some((v, i2) => i2 & 1 && v == value))
            result.push(this.data[i], value);
        }
      }
      return result;
    }
    overrides(token, prev) {
      let iPrev = findOffset(this.data, this.tokenPrecTable, prev);
      return iPrev < 0 || findOffset(this.data, this.tokenPrecTable, token) < iPrev;
    }
    configure(config2) {
      let copy = Object.assign(Object.create(LRParser.prototype), this);
      if (config2.props)
        copy.nodeSet = this.nodeSet.extend(...config2.props);
      if (config2.top) {
        let info = this.topRules[config2.top];
        if (!info)
          throw new RangeError(`Invalid top rule name ${config2.top}`);
        copy.top = info;
      }
      if (config2.tokenizers)
        copy.tokenizers = this.tokenizers.map((t2) => {
          let found = config2.tokenizers.find((r) => r.from == t2);
          return found ? found.to : t2;
        });
      if (config2.contextTracker)
        copy.context = config2.contextTracker;
      if (config2.dialect)
        copy.dialect = this.parseDialect(config2.dialect);
      if (config2.strict != null)
        copy.strict = config2.strict;
      if (config2.wrap)
        copy.wrappers = copy.wrappers.concat(config2.wrap);
      if (config2.bufferLength != null)
        copy.bufferLength = config2.bufferLength;
      return copy;
    }
    getName(term) {
      return this.termNames ? this.termNames[term] : String(term <= this.maxNode && this.nodeSet.types[term].name || term);
    }
    get eofTerm() {
      return this.maxNode + 1;
    }
    get topNode() {
      return this.nodeSet.types[this.top[1]];
    }
    dynamicPrecedence(term) {
      let prec2 = this.dynamicPrecedences;
      return prec2 == null ? 0 : prec2[term] || 0;
    }
    parseDialect(dialect) {
      let values2 = Object.keys(this.dialects), flags = values2.map(() => false);
      if (dialect)
        for (let part of dialect.split(" ")) {
          let id2 = values2.indexOf(part);
          if (id2 >= 0)
            flags[id2] = true;
        }
      let disabled = null;
      for (let i = 0; i < values2.length; i++)
        if (!flags[i]) {
          for (let j = this.dialects[values2[i]], id2; (id2 = this.data[j++]) != 65535; )
            (disabled || (disabled = new Uint8Array(this.maxTerm + 1)))[id2] = 1;
        }
      return new Dialect(dialect, flags, disabled);
    }
    static deserialize(spec) {
      return new LRParser(spec);
    }
  };
  function pair(data, off) {
    return data[off] | data[off + 1] << 16;
  }
  function findOffset(data, start, term) {
    for (let i = start, next; (next = data[i]) != 65535; i++)
      if (next == term)
        return i - start;
    return -1;
  }
  function findFinished(stacks) {
    let best = null;
    for (let stack of stacks) {
      let stopped = stack.p.stoppedAt;
      if ((stack.pos == stack.p.stream.end || stopped != null && stack.pos > stopped) && stack.p.parser.stateFlag(stack.state, 2) && (!best || best.score < stack.score))
        best = stack;
    }
    return best;
  }

  // node_modules/@lezer/javascript/dist/index.es.js
  var noSemi = 279;
  var incdec = 1;
  var incdecPrefix = 2;
  var templateContent = 280;
  var InterpolationStart = 3;
  var templateEnd = 281;
  var insertSemi = 282;
  var TSExtends = 4;
  var spaces = 284;
  var newline = 285;
  var LineComment = 5;
  var BlockComment = 6;
  var Dialect_ts = 1;
  var space = [
    9,
    10,
    11,
    12,
    13,
    32,
    133,
    160,
    5760,
    8192,
    8193,
    8194,
    8195,
    8196,
    8197,
    8198,
    8199,
    8200,
    8201,
    8202,
    8232,
    8233,
    8239,
    8287,
    12288
  ];
  var braceR = 125;
  var braceL = 123;
  var semicolon = 59;
  var slash = 47;
  var star = 42;
  var plus = 43;
  var minus = 45;
  var dollar = 36;
  var backtick = 96;
  var backslash = 92;
  var trackNewline = new ContextTracker({
    start: false,
    shift(context, term) {
      return term == LineComment || term == BlockComment || term == spaces ? context : term == newline;
    },
    strict: false
  });
  var insertSemicolon = new ExternalTokenizer((input, stack) => {
    let { next } = input;
    if ((next == braceR || next == -1 || stack.context) && stack.canShift(insertSemi))
      input.acceptToken(insertSemi);
  }, { contextual: true, fallback: true });
  var noSemicolon = new ExternalTokenizer((input, stack) => {
    let { next } = input, after;
    if (space.indexOf(next) > -1)
      return;
    if (next == slash && ((after = input.peek(1)) == slash || after == star))
      return;
    if (next != braceR && next != semicolon && next != -1 && !stack.context && stack.canShift(noSemi))
      input.acceptToken(noSemi);
  }, { contextual: true });
  var incdecToken = new ExternalTokenizer((input, stack) => {
    let { next } = input;
    if (next == plus || next == minus) {
      input.advance();
      if (next == input.next) {
        input.advance();
        let mayPostfix = !stack.context && stack.canShift(incdec);
        input.acceptToken(mayPostfix ? incdec : incdecPrefix);
      }
    }
  }, { contextual: true });
  var template = new ExternalTokenizer((input) => {
    for (let afterDollar = false, i = 0; ; i++) {
      let { next } = input;
      if (next < 0) {
        if (i)
          input.acceptToken(templateContent);
        break;
      } else if (next == backtick) {
        if (i)
          input.acceptToken(templateContent);
        else
          input.acceptToken(templateEnd, 1);
        break;
      } else if (next == braceL && afterDollar) {
        if (i == 1)
          input.acceptToken(InterpolationStart, 1);
        else
          input.acceptToken(templateContent, -1);
        break;
      } else if (next == 10 && i) {
        input.advance();
        input.acceptToken(templateContent);
        break;
      } else if (next == backslash) {
        input.advance();
      }
      afterDollar = next == dollar;
      input.advance();
    }
  });
  function tsExtends(value, stack) {
    return value == "extends" && stack.dialectEnabled(Dialect_ts) ? TSExtends : -1;
  }
  var spec_identifier = { __proto__: null, export: 18, as: 23, from: 29, default: 32, async: 37, function: 38, this: 48, true: 56, false: 56, void: 66, typeof: 70, null: 86, super: 88, new: 122, await: 139, yield: 141, delete: 142, class: 152, extends: 154, public: 197, private: 197, protected: 197, readonly: 199, instanceof: 220, in: 222, const: 224, import: 256, keyof: 307, unique: 311, infer: 317, is: 351, abstract: 371, implements: 373, type: 375, let: 378, var: 380, interface: 387, enum: 391, namespace: 397, module: 399, declare: 403, global: 407, for: 428, of: 437, while: 440, with: 444, do: 448, if: 452, else: 454, switch: 458, case: 464, try: 470, catch: 472, finally: 474, return: 478, throw: 482, break: 486, continue: 490, debugger: 494 };
  var spec_word = { __proto__: null, async: 109, get: 111, set: 113, public: 161, private: 161, protected: 161, static: 163, abstract: 165, override: 167, readonly: 173, new: 355 };
  var spec_LessThan = { __proto__: null, "<": 129 };
  var parser = LRParser.deserialize({
    version: 13,
    states: "$1jO`QYOOO'QQ!LdO'#ChO'XOSO'#DVO)dQYO'#D]O)tQYO'#DhO){QYO'#DrO-xQYO'#DxOOQO'#E]'#E]O.]QWO'#E[O.bQWO'#E[OOQ!LS'#Ef'#EfO0aQ!LdO'#IrO2wQ!LdO'#IsO3eQWO'#EzO3jQpO'#FaOOQ!LS'#FS'#FSO3rO!bO'#FSO4QQWO'#FhO5_QWO'#FgOOQ!LS'#Is'#IsOOQ!LQ'#Ir'#IrOOQQ'#J['#J[O5dQWO'#HnO5iQ!LYO'#HoOOQQ'#If'#IfOOQQ'#Hp'#HpQ`QYOOO){QYO'#DjO5qQWO'#G[O5vQ#tO'#CmO6UQWO'#EZO6aQWO'#EgO6fQ#tO'#FRO7QQWO'#G[O7VQWO'#G`O7bQWO'#G`O7pQWO'#GcO7pQWO'#GdO7pQWO'#GfO5qQWO'#GiO8aQWO'#GlO9oQWO'#CdO:PQWO'#GyO:XQWO'#HPO:XQWO'#HRO`QYO'#HTO:XQWO'#HVO:XQWO'#HYO:^QWO'#H`O:cQ!LZO'#HdO){QYO'#HfO:nQ!LZO'#HhO:yQ!LZO'#HjO5iQ!LYO'#HlO){QYO'#DWOOOS'#Hr'#HrO;UOSO,59qOOQ!LS,59q,59qO=gQbO'#ChO=qQYO'#HsO>UQWO'#ItO@TQbO'#ItO'dQYO'#ItO@[QWO,59wO@rQ&jO'#DbOAkQWO'#E]OAxQWO'#JPOBTQWO'#JOOBTQWO'#JOOB]QWO,5:yOBbQWO'#I}OBiQWO'#DyO5vQ#tO'#EZOBwQWO'#EZOCSQ`O'#FROOQ!LS,5:S,5:SOC[QYO,5:SOEYQ!LdO,5:^OEvQWO,5:dOFaQ!LYO'#I|O7VQWO'#I{OFhQWO'#I{OFpQWO,5:xOFuQWO'#I{OGTQYO,5:vOITQWO'#EWOJ_QWO,5:vOKnQWO'#DlOKuQYO'#DqOLPQ&jO,5;PO){QYO,5;POOQQ'#Er'#ErOOQQ'#Et'#EtO){QYO,5;RO){QYO,5;RO){QYO,5;RO){QYO,5;RO){QYO,5;RO){QYO,5;RO){QYO,5;RO){QYO,5;RO){QYO,5;RO){QYO,5;RO){QYO,5;ROOQQ'#Ex'#ExOLXQYO,5;cOOQ!LS,5;h,5;hOOQ!LS,5;i,5;iONXQWO,5;iOOQ!LS,5;j,5;jO){QYO'#H}ON^Q!LYO,5<TONxQWO,5;RO){QYO,5;fO! bQpO'#JTO! PQpO'#JTO! iQpO'#JTO! zQpO,5;qOOOO,5;{,5;{O!!YQYO'#FcOOOO'#H|'#H|O3rO!bO,5;nO!!aQpO'#FeOOQ!LS,5;n,5;nO!!}Q,UO'#CrOOQ!LS'#Cu'#CuO!#bQWO'#CuO!#gOSO'#CyO!$TQ#tO,5<QO!$[QWO,5<SO!%hQWO'#FrO!%uQWO'#FsO!%zQWO'#FwO!&|Q&jO'#F{O!'oQ,UO'#IoOOQ!LS'#Io'#IoO!'yQWO'#InO!(XQWO'#ImOOQ!LS'#Cs'#CsOOQ!LS'#C|'#C|O!(aQWO'#DOOJdQWO'#FjOJdQWO'#FlO!(fQWO'#FnO!(kQWO'#FoO!(pQWO'#FuOJdQWO'#FzO!(uQWO'#E^O!)^QWO,5<RO`QYO,5>YOOQQ'#Ii'#IiOOQQ,5>Z,5>ZOOQQ-E;n-E;nO!+YQ!LdO,5:UOOQ!LQ'#Cp'#CpO!+yQ#tO,5<vOOQO'#Cf'#CfO!,[QWO'#CqO!,dQ!LYO'#IjO5_QWO'#IjO:^QWO,59XO!,rQpO,59XO!,zQ#tO,59XO5vQ#tO,59XO!-VQWO,5:vO!-_QWO'#GxO!-mQWO'#J`O){QYO,5;kO!-uQ&jO,5;mO!-zQWO,5=cO!.PQWO,5=cO!.UQWO,5=cO5iQ!LYO,5=cO5qQWO,5<vO!.dQWO'#E_O!.xQ&jO'#E`OOQ!LQ'#I}'#I}O!/ZQ!LYO'#J]O5iQ!LYO,5<zO7pQWO,5=QOOQO'#Cr'#CrO!/fQpO,5<}O!/nQ#tO,5=OO!/yQWO,5=QO!0OQ`O,5=TO:^QWO'#GnO5qQWO'#GpO!0WQWO'#GpO5vQ#tO'#GsO!0]QWO'#GsOOQQ,5=W,5=WO!0bQWO'#GtO!0jQWO'#CmO!0oQWO,59OO!0yQWO,59OO!2{QYO,59OOOQQ,59O,59OO!3YQ!LYO,59OO){QYO,59OO!3eQYO'#G{OOQQ'#G|'#G|OOQQ'#G}'#G}O`QYO,5=eO!3uQWO,5=eO){QYO'#DxO`QYO,5=kO`QYO,5=mO!3zQWO,5=oO`QYO,5=qO!4PQWO,5=tO!4UQYO,5=zOOQQ,5>O,5>OO){QYO,5>OO5iQ!LYO,5>QOOQQ,5>S,5>SO!8VQWO,5>SOOQQ,5>U,5>UO!8VQWO,5>UOOQQ,5>W,5>WO!8[Q`O,59rOOOS-E;p-E;pOOQ!LS1G/]1G/]O!8aQbO,5>_O'dQYO,5>_OOQO,5>d,5>dO!8kQYO'#HsOOQO-E;q-E;qO!8xQWO,5?`O!9QQbO,5?`O!9XQWO,5?jOOQ!LS1G/c1G/cO!9aQpO'#DTOOQO'#Iv'#IvO){QYO'#IvO!:OQpO'#IvO!:mQpO'#DcO!;OQ&jO'#DcO!=ZQYO'#DcO!=bQWO'#IuO!=jQWO,59|O!=oQWO'#EaO!=}QWO'#JQO!>VQWO,5:zO!>mQ&jO'#DcO){QYO,5?kO!>wQWO'#HxOOQO-E;v-E;vO!9XQWO,5?jOOQ!LQ1G0e1G0eO!@TQ&jO'#D|OOQ!LS,5:e,5:eO){QYO,5:eOITQWO,5:eO!@[QWO,5:eO:^QWO,5:uO!,rQpO,5:uO!,zQ#tO,5:uO5vQ#tO,5:uOOQ!LS1G/n1G/nOOQ!LS1G0O1G0OOOQ!LQ'#EV'#EVO){QYO,5?hO!@gQ!LYO,5?hO!@xQ!LYO,5?hO!APQWO,5?gO!AXQWO'#HzO!APQWO,5?gOOQ!LQ1G0d1G0dO7VQWO,5?gOOQ!LS1G0b1G0bO!AsQ!LdO1G0bO!BdQ!LbO,5:rOOQ!LS'#Fq'#FqO!CQQ!LdO'#IoOGTQYO1G0bO!EPQ#tO'#IwO!EZQWO,5:WO!E`QbO'#IxO){QYO'#IxO!EjQWO,5:]OOQ!LS'#DT'#DTOOQ!LS1G0k1G0kO!EoQWO1G0kO!HQQ!LdO1G0mO!HXQ!LdO1G0mO!JlQ!LdO1G0mO!JsQ!LdO1G0mO!LzQ!LdO1G0mO!M_Q!LdO1G0mO#!OQ!LdO1G0mO#!VQ!LdO1G0mO#$jQ!LdO1G0mO#$qQ!LdO1G0mO#&fQ!LdO1G0mO#)`Q7^O'#ChO#+ZQ7^O1G0}O#-UQ7^O'#IsOOQ!LS1G1T1G1TO#-iQ!LdO,5>iOOQ!LQ-E;{-E;{O#.YQ!LdO1G0mOOQ!LS1G0m1G0mO#0[Q!LdO1G1QO#0{QpO,5;sO#1QQpO,5;tO#1VQpO'#F[O#1kQWO'#FZOOQO'#JU'#JUOOQO'#H{'#H{O#1pQpO1G1]OOQ!LS1G1]1G1]OOOO1G1f1G1fO#2OQ7^O'#IrO#2YQWO,5;}OLXQYO,5;}OOOO-E;z-E;zOOQ!LS1G1Y1G1YOOQ!LS,5<P,5<PO#2_QpO,5<POOQ!LS,59a,59aOITQWO'#C{OOOS'#Hq'#HqO#2dOSO,59eOOQ!LS,59e,59eO){QYO1G1lO!(kQWO'#IPO#2oQWO,5<eOOQ!LS,5<b,5<bOOQO'#GV'#GVOJdQWO,5<pOOQO'#GX'#GXOJdQWO,5<rOJdQWO,5<tOOQO1G1n1G1nO#2zQ`O'#CpO#3_Q`O,5<^O#3fQWO'#JXO5qQWO'#JXO#3tQWO,5<`OJdQWO,5<_O#3yQ`O'#FqO#4WQ`O'#JYO#4bQWO'#JYOITQWO'#JYO#4gQWO,5<cOOQ!LQ'#Dg'#DgO#4lQWO'#FtO#4wQpO'#F|O!&wQ&jO'#F|O!&wQ&jO'#GOO#5YQWO'#GPO!(pQWO'#GSOOQO'#IR'#IRO#5_Q&jO,5<gOOQ!LS,5<g,5<gO#5fQ&jO'#F|O#5tQ&jO'#F}O#5|Q&jO'#F}OOQ!LS,5<u,5<uOJdQWO,5?YOJdQWO,5?YO#6RQWO'#ISO#6^QWO,5?XOOQ!LS'#Ch'#ChO#7QQ#tO,59jOOQ!LS,59j,59jO#7sQ#tO,5<UO#8fQ#tO,5<WO#8pQWO,5<YOOQ!LS,5<Z,5<ZO#8uQWO,5<aO#8zQ#tO,5<fOGTQYO1G1mO#9[QWO1G1mOOQQ1G3t1G3tOOQ!LS1G/p1G/pONXQWO1G/pOOQQ1G2b1G2bOITQWO1G2bO){QYO1G2bOITQWO1G2bO#9aQWO1G2bO#9oQWO,59]O#:xQWO'#EWOOQ!LQ,5?U,5?UO#;SQ!LYO,5?UOOQQ1G.s1G.sO:^QWO1G.sO!,rQpO1G.sO!,zQ#tO1G.sO#;bQWO1G0bO#;gQWO'#ChO#;rQWO'#JaO#;zQWO,5=dO#<PQWO'#JaO#<UQWO'#JaO#<^QWO'#I[O#<lQWO,5?zO#<tQbO1G1VOOQ!LS1G1X1G1XO5qQWO1G2}O#<{QWO1G2}O#=QQWO1G2}O#=VQWO1G2}OOQQ1G2}1G2}O#=[Q#tO1G2bO7VQWO'#JOO7VQWO'#EaO7VQWO'#IUO#=mQ!LYO,5?wOOQQ1G2f1G2fO!/yQWO1G2lOITQWO1G2iO#=xQWO1G2iOOQQ1G2j1G2jOITQWO1G2jO#=}QWO1G2jO#>VQ&jO'#GhOOQQ1G2l1G2lO!&wQ&jO'#IWO!0OQ`O1G2oOOQQ1G2o1G2oOOQQ,5=Y,5=YO#>_Q#tO,5=[O5qQWO,5=[O#5YQWO,5=_O5_QWO,5=_O!,rQpO,5=_O!,zQ#tO,5=_O5vQ#tO,5=_O#>pQWO'#J_O#>{QWO,5=`OOQQ1G.j1G.jO#?QQ!LYO1G.jO#?]QWO1G.jO#?bQWO1G.jO5iQ!LYO1G.jO#?jQbO,5?|O#?tQWO,5?|O#@PQYO,5=gO#@WQWO,5=gO7VQWO,5?|OOQQ1G3P1G3PO`QYO1G3POOQQ1G3V1G3VOOQQ1G3X1G3XO:XQWO1G3ZO#@]QYO1G3]O#DWQYO'#H[OOQQ1G3`1G3`O:^QWO1G3fO#DeQWO1G3fO5iQ!LYO1G3jOOQQ1G3l1G3lOOQ!LQ'#Fx'#FxO5iQ!LYO1G3nO5iQ!LYO1G3pOOOS1G/^1G/^O#DmQ`O,5<TO#DuQbO1G3yOOQO1G4O1G4OO){QYO,5>_O#EPQWO1G4zO#EXQWO1G5UO#EaQWO,5?bOLXQYO,5:{O7VQWO,5:{O:^QWO,59}OLXQYO,59}O!,rQpO,59}O#EfQ7^O,59}OOQO,5:{,5:{O#EpQ&jO'#HtO#FWQWO,5?aOOQ!LS1G/h1G/hO#F`Q&jO'#HyO#FtQWO,5?lOOQ!LQ1G0f1G0fO!;OQ&jO,59}O#F|QbO1G5VO7VQWO,5>dOOQ!LQ'#ES'#ESO#GWQ!LrO'#ETO!?{Q&jO'#D}OOQO'#Hw'#HwO#GrQ&jO,5:hOOQ!LS,5:h,5:hO#GyQ&jO'#D}O#H[Q&jO'#D}O#HcQ&jO'#EYO#HfQ&jO'#ETO#HsQ&jO'#ETO!?{Q&jO'#ETO#IWQWO1G0PO#I]Q`O1G0POOQ!LS1G0P1G0PO){QYO1G0POITQWO1G0POOQ!LS1G0a1G0aO:^QWO1G0aO!,rQpO1G0aO!,zQ#tO1G0aO#IdQ!LdO1G5SO){QYO1G5SO#ItQ!LYO1G5SO#JVQWO1G5RO7VQWO,5>fOOQO,5>f,5>fO#J_QWO,5>fOOQO-E;x-E;xO#JVQWO1G5RO#JmQ!LdO,59jO#LlQ!LdO,5<UO#NnQ!LdO,5<WO$!pQ!LdO,5<fOOQ!LS7+%|7+%|O$$xQ!LdO7+%|O$%iQWO'#HuO$%sQWO,5?cOOQ!LS1G/r1G/rO$%{QYO'#HvO$&YQWO,5?dO$&bQbO,5?dOOQ!LS1G/w1G/wOOQ!LS7+&V7+&VO$&lQ7^O,5:^O){QYO7+&iO$&vQ7^O,5:UOOQO1G1_1G1_OOQO1G1`1G1`O$'TQMhO,5;vOLXQYO,5;uOOQO-E;y-E;yOOQ!LS7+&w7+&wOOOO7+'Q7+'QOOOO1G1i1G1iO$'`QWO1G1iOOQ!LS1G1k1G1kO$'eQ`O,59gOOOS-E;o-E;oOOQ!LS1G/P1G/PO$'lQ!LdO7+'WOOQ!LS,5>k,5>kO$(]QWO,5>kOOQ!LS1G2P1G2PP$(bQWO'#IPPOQ!LS-E;}-E;}O$)RQ#tO1G2[O$)tQ#tO1G2^O$*OQ#tO1G2`OOQ!LS1G1x1G1xO$*VQWO'#IOO$*eQWO,5?sO$*eQWO,5?sO$*mQWO,5?sO$*xQWO,5?sOOQO1G1z1G1zO$+WQ#tO1G1yO$+hQWO'#IQO$+xQWO,5?tOITQWO,5?tO$,QQ`O,5?tOOQ!LS1G1}1G1}O5iQ!LYO,5<hO5iQ!LYO,5<iO$,[QWO,5<iO#5TQWO,5<iO!,rQpO,5<hO$,aQWO,5<jO5iQ!LYO,5<kO$,[QWO,5<nOOQO-E<P-E<POOQ!LS1G2R1G2RO!&wQ&jO,5<hO$,iQWO,5<iO!&wQ&jO,5<jO!&wQ&jO,5<iO$,tQ#tO1G4tO$-OQ#tO1G4tOOQO,5>n,5>nOOQO-E<Q-E<QO!-uQ&jO,59lO){QYO,59lO$-]QWO1G1tOJdQWO1G1{O$-bQ!LdO7+'XOOQ!LS7+'X7+'XOGTQYO7+'XOOQ!LS7+%[7+%[O$.RQ`O'#JZO#IWQWO7+'|O$.]QWO7+'|O$.eQ`O7+'|OOQQ7+'|7+'|OITQWO7+'|O){QYO7+'|OITQWO7+'|OOQO1G.w1G.wO$.oQ!LbO'#ChO$/PQ!LbO,5<lO$/nQWO,5<lOOQ!LQ1G4p1G4pOOQQ7+$_7+$_O:^QWO7+$_O!,rQpO7+$_OGTQYO7+%|O$/sQWO'#IZO$0UQWO,5?{OOQO1G3O1G3OO5qQWO,5?{O$0UQWO,5?{O$0^QWO,5?{OOQO,5>v,5>vOOQO-E<Y-E<YOOQ!LS7+&q7+&qO$0cQWO7+(iO5iQ!LYO7+(iO5qQWO7+(iO$0hQWO7+(iO$0mQWO7+'|OOQ!LQ,5>p,5>pOOQ!LQ-E<S-E<SOOQQ7+(W7+(WO$0{Q!LbO7+(TOITQWO7+(TO$1VQ`O7+(UOOQQ7+(U7+(UOITQWO7+(UO$1^QWO'#J^O$1iQWO,5=SOOQO,5>r,5>rOOQO-E<U-E<UOOQQ7+(Z7+(ZO$2cQ&jO'#GqOOQQ1G2v1G2vOITQWO1G2vO){QYO1G2vOITQWO1G2vO$2jQWO1G2vO$2xQ#tO1G2vO5iQ!LYO1G2yO#5YQWO1G2yO5_QWO1G2yO!,rQpO1G2yO!,zQ#tO1G2yO$3ZQWO'#IYO$3fQWO,5?yO$3nQ&jO,5?yOOQ!LQ1G2z1G2zOOQQ7+$U7+$UO$3vQWO7+$UO5iQ!LYO7+$UO$3{QWO7+$UO){QYO1G5hO){QYO1G5iO$4QQYO1G3RO$4XQWO1G3RO$4^QYO1G3RO$4eQ!LYO1G5hOOQQ7+(k7+(kO5iQ!LYO7+(uO`QYO7+(wOOQQ'#Jd'#JdOOQQ'#I]'#I]O$4oQYO,5=vOOQQ,5=v,5=vO){QYO'#H]O$4|QWO'#H_OOQQ7+)Q7+)QO$5RQYO7+)QO7VQWO7+)QOOQQ7+)U7+)UOOQQ7+)Y7+)YOOQQ7+)[7+)[OOQO1G4|1G4|O$9PQ7^O1G0gO$9ZQWO1G0gOOQO1G/i1G/iO$9fQ7^O1G/iO:^QWO1G/iOLXQYO'#DcOOQO,5>`,5>`OOQO-E;r-E;rOOQO,5>e,5>eOOQO-E;w-E;wO!,rQpO1G/iO:^QWO,5:iOOQO,5:o,5:oO){QYO,5:oO$9pQ!LYO,5:oO$9{Q!LYO,5:oO!,rQpO,5:iOOQO-E;u-E;uOOQ!LS1G0S1G0SO!?{Q&jO,5:iO$:ZQ&jO,5:iO$:lQ!LrO,5:oO$;WQ&jO,5:iO!?{Q&jO,5:oOOQO,5:t,5:tO$;_Q&jO,5:oO$;lQ!LYO,5:oOOQ!LS7+%k7+%kO#IWQWO7+%kO#I]Q`O7+%kOOQ!LS7+%{7+%{O:^QWO7+%{O!,rQpO7+%{O$<QQ!LdO7+*nO){QYO7+*nOOQO1G4Q1G4QO7VQWO1G4QO$<bQWO7+*mO$<jQ!LdO1G2[O$>lQ!LdO1G2^O$@nQ!LdO1G1yO$BvQ#tO,5>aOOQO-E;s-E;sO$CQQbO,5>bO){QYO,5>bOOQO-E;t-E;tO$C[QWO1G5OO$CdQ7^O1G0bO$EkQ7^O1G0mO$ErQ7^O1G0mO$GsQ7^O1G0mO$GzQ7^O1G0mO$IoQ7^O1G0mO$JSQ7^O1G0mO$LaQ7^O1G0mO$LhQ7^O1G0mO$NiQ7^O1G0mO$NpQ7^O1G0mO%!eQ7^O1G0mO%!xQ!LdO<<JTO%#iQ7^O1G0mO%%XQ7^O'#IoO%'UQ7^O1G1QOLXQYO'#F^OOQO'#JV'#JVOOQO1G1b1G1bO%'cQWO1G1aO%'hQ7^O,5>iOOOO7+'T7+'TOOOS1G/R1G/ROOQ!LS1G4V1G4VOJdQWO7+'zO%'rQWO,5>jO5qQWO,5>jOOQO-E;|-E;|O%(QQWO1G5_O%(QQWO1G5_O%(YQWO1G5_O%(eQ`O,5>lO%(oQWO,5>lOITQWO,5>lOOQO-E<O-E<OO%(tQ`O1G5`O%)OQWO1G5`OOQO1G2S1G2SOOQO1G2T1G2TO5iQ!LYO1G2TO$,[QWO1G2TO5iQ!LYO1G2SO%)WQWO1G2UOITQWO1G2UOOQO1G2V1G2VO5iQ!LYO1G2YO!,rQpO1G2SO#5TQWO1G2TO%)]QWO1G2UO%)eQWO1G2TOJdQWO7+*`OOQ!LS1G/W1G/WO%)pQWO1G/WOOQ!LS7+'`7+'`O%)uQ#tO7+'gO%*VQ!LdO<<JsOOQ!LS<<Js<<JsOITQWO'#ITO%*vQWO,5?uOOQQ<<Kh<<KhOITQWO<<KhO#IWQWO<<KhO%+OQWO<<KhO%+WQ`O<<KhOITQWO1G2WOOQQ<<Gy<<GyO:^QWO<<GyO%+bQ!LdO<<IhOOQ!LS<<Ih<<IhOOQO,5>u,5>uO%,RQWO,5>uO%,WQWO,5>uOOQO-E<X-E<XO%,`QWO1G5gO%,`QWO1G5gO5qQWO1G5gO%,hQWO<<LTOOQQ<<LT<<LTO%,mQWO<<LTO5iQ!LYO<<LTO){QYO<<KhOITQWO<<KhOOQQ<<Ko<<KoO$0{Q!LbO<<KoOOQQ<<Kp<<KpO$1VQ`O<<KpO%,rQ&jO'#IVO%,}QWO,5?xOLXQYO,5?xOOQQ1G2n1G2nO#GWQ!LrO'#ETO!?{Q&jO'#GrOOQO'#IX'#IXO%-VQ&jO,5=]OOQQ,5=],5=]O%-^Q&jO'#ETO%-iQ&jO'#ETO%.QQ&jO'#ETO%.[Q&jO'#GrO%.mQWO7+(bO%.rQWO7+(bO%.zQ`O7+(bOOQQ7+(b7+(bOITQWO7+(bO){QYO7+(bOITQWO7+(bO%/UQWO7+(bOOQQ7+(e7+(eO5iQ!LYO7+(eO#5YQWO7+(eO5_QWO7+(eO!,rQpO7+(eO%/dQWO,5>tOOQO-E<W-E<WOOQO'#Gu'#GuO%/oQWO1G5eO5iQ!LYO<<GpOOQQ<<Gp<<GpO%/wQWO<<GpO%/|QWO7++SO%0RQWO7++TOOQQ7+(m7+(mO%0WQWO7+(mO%0]QYO7+(mO%0dQWO7+(mO){QYO7++SO){QYO7++TOOQQ<<La<<LaOOQQ<<Lc<<LcOOQQ-E<Z-E<ZOOQQ1G3b1G3bO%0iQWO,5=wOOQQ,5=y,5=yO:^QWO<<LlO%0nQWO<<LlOLXQYO7+&ROOQO7+%T7+%TO%0sQ7^O1G5VO:^QWO7+%TOOQO1G0T1G0TO%0}Q!LdO1G0ZOOQO1G0Z1G0ZO){QYO1G0ZO%1XQ!LYO1G0ZO:^QWO1G0TO!,rQpO1G0TO!?{Q&jO1G0TO%1dQ!LYO1G0ZO%1rQ&jO1G0TO%2TQ!LYO1G0ZO%2iQ!LrO1G0ZO%2sQ&jO1G0TO!?{Q&jO1G0ZOOQ!LS<<IV<<IVOOQ!LS<<Ig<<IgO:^QWO<<IgO%2zQ!LdO<<NYOOQO7+)l7+)lO%3[Q!LdO7+'gO%5dQbO1G3|O%5nQ7^O7+%|O%5{Q7^O,59jO%7xQ7^O,5<UO%9uQ7^O,5<WO%;rQ7^O,5<fO%=bQ7^O7+'WO%=oQ7^O7+'XO%=|QWO,5;xOOQO7+&{7+&{O%>RQ#tO<<KfOOQO1G4U1G4UO%>cQWO1G4UO%>nQWO1G4UO%>|QWO7+*yO%>|QWO7+*yOITQWO1G4WO%?UQ`O1G4WO%?`QWO7+*zOOQO7+'o7+'oO5iQ!LYO7+'oOOQO7+'n7+'nO$,[QWO7+'pO%?hQ`O7+'pOOQO7+'t7+'tO5iQ!LYO7+'nO$,[QWO7+'oO%?oQWO7+'pOITQWO7+'pO#5TQWO7+'oO%?tQ#tO<<MzOOQ!LS7+$r7+$rO%@OQ`O,5>oOOQO-E<R-E<RO#IWQWOANASOOQQANASANASOITQWOANASO%@YQ!LbO7+'rOOQQAN=eAN=eO5qQWO1G4aOOQO1G4a1G4aO%@gQWO1G4aO%@lQWO7++RO%@lQWO7++RO5iQ!LYOANAoO%@tQWOANAoOOQQANAoANAoO%@yQWOANASO%ARQ`OANASOOQQANAZANAZOOQQANA[ANA[O%A]QWO,5>qOOQO-E<T-E<TO%AhQ7^O1G5dO#5YQWO,5=^O5_QWO,5=^O!,rQpO,5=^OOQO-E<V-E<VOOQQ1G2w1G2wO$:lQ!LrO,5:oO!?{Q&jO,5=^O%ArQ&jO,5=^O%BTQ&jO,5:oOOQQ<<K|<<K|OITQWO<<K|O%.mQWO<<K|O%B_QWO<<K|O%BgQ`O<<K|O){QYO<<K|OITQWO<<K|OOQQ<<LP<<LPO5iQ!LYO<<LPO#5YQWO<<LPO5_QWO<<LPO%BqQ&jO1G4`O%ByQWO7++POOQQAN=[AN=[O5iQ!LYOAN=[OOQQ<<Nn<<NnOOQQ<<No<<NoOOQQ<<LX<<LXO%CRQWO<<LXO%CWQYO<<LXO%C_QWO<<NnO%CdQWO<<NoOOQQ1G3c1G3cOOQQANBWANBWO:^QWOANBWO%CiQ7^O<<ImOOQO<<Ho<<HoOOQO7+%u7+%uO%0}Q!LdO7+%uO){QYO7+%uOOQO7+%o7+%oO:^QWO7+%oO!,rQpO7+%oO%CsQ!LYO7+%uO!?{Q&jO7+%oO%DOQ!LYO7+%uO%D^Q&jO7+%oO%DoQ!LYO7+%uOOQ!LSAN?RAN?RO%ETQ!LdO<<KfO%G]Q7^O<<JTO%GjQ7^O1G1yO%IYQ7^O1G2[O%KVQ7^O1G2^O%MSQ7^O<<JsO%MaQ7^O<<IhOOQO1G1d1G1dOOQO7+)p7+)pO%MnQWO7+)pO%MyQWO<<NeO%NRQ`O7+)rOOQO<<KZ<<KZO5iQ!LYO<<K[O$,[QWO<<K[OOQO<<KY<<KYO5iQ!LYO<<KZO%N]Q`O<<K[O$,[QWO<<KZOOQQG26nG26nO#IWQWOG26nOOQO7+){7+){O5qQWO7+){O%NdQWO<<NmOOQQG27ZG27ZO5iQ!LYOG27ZOITQWOG26nOLXQYO1G4]O%NlQWO7++OO5iQ!LYO1G2xO#5YQWO1G2xO5_QWO1G2xO!,rQpO1G2xO!?{Q&jO1G2xO%2iQ!LrO1G0ZO%NtQ&jO1G2xO%.mQWOANAhOOQQANAhANAhOITQWOANAhO& VQWOANAhO& _Q`OANAhOOQQANAkANAkO5iQ!LYOANAkO#5YQWOANAkOOQO'#Gv'#GvOOQO7+)z7+)zOOQQG22vG22vOOQQANAsANAsO& iQWOANAsOOQQANDYANDYOOQQANDZANDZO& nQYOG27rOOQO<<Ia<<IaO%0}Q!LdO<<IaOOQO<<IZ<<IZO:^QWO<<IZO){QYO<<IaO!,rQpO<<IZO&%lQ!LYO<<IaO!?{Q&jO<<IZO&%wQ!LYO<<IaO&&VQ7^O7+'gOOQO<<M[<<M[OOQOAN@vAN@vO5iQ!LYOAN@vOOQOAN@uAN@uO$,[QWOAN@vO5iQ!LYOAN@uOOQQLD,YLD,YOOQO<<Mg<<MgOOQQLD,uLD,uO#IWQWOLD,YO&'uQ7^O7+)wOOQO7+(d7+(dO5iQ!LYO7+(dO#5YQWO7+(dO5_QWO7+(dO!,rQpO7+(dO!?{Q&jO7+(dOOQQG27SG27SO%.mQWOG27SOITQWOG27SOOQQG27VG27VO5iQ!LYOG27VOOQQG27_G27_O:^QWOLD-^OOQOAN>{AN>{OOQOAN>uAN>uO%0}Q!LdOAN>{O:^QWOAN>uO){QYOAN>{O!,rQpOAN>uO&(PQ!LYOAN>{O&([Q7^O<<KfOOQOG26bG26bO5iQ!LYOG26bOOQOG26aG26aOOQQ!$( t!$( tOOQO<<LO<<LOO5iQ!LYO<<LOO#5YQWO<<LOO5_QWO<<LOO!,rQpO<<LOOOQQLD,nLD,nO%.mQWOLD,nOOQQLD,qLD,qOOQQ!$(!x!$(!xOOQOG24gG24gOOQOG24aG24aO%0}Q!LdOG24gO:^QWOG24aO){QYOG24gOOQOLD+|LD+|OOQOANAjANAjO5iQ!LYOANAjO#5YQWOANAjO5_QWOANAjOOQQ!$(!Y!$(!YOOQOLD*RLD*ROOQOLD){LD){O%0}Q!LdOLD*ROOQOG27UG27UO5iQ!LYOG27UO#5YQWOG27UOOQO!$'Mm!$'MmOOQOLD,pLD,pO5iQ!LYOLD,pOOQO!$(![!$(![OLXQYO'#DrO&)zQbO'#IrOLXQYO'#DjO&*RQ!LdO'#ChO&*lQbO'#ChO&*|QYO,5:vOLXQYO,5;ROLXQYO,5;ROLXQYO,5;ROLXQYO,5;ROLXQYO,5;ROLXQYO,5;ROLXQYO,5;ROLXQYO,5;ROLXQYO,5;ROLXQYO,5;ROLXQYO,5;ROLXQYO'#H}O&,|QWO,5<TO&.`QWO,5;ROLXQYO,5;fO!(aQWO'#DOO!(aQWO'#DOOITQWO'#FjO&-UQWO'#FjOITQWO'#FlO&-UQWO'#FlOITQWO'#FzO&-UQWO'#FzOLXQYO,5?kO&*|QYO1G0bO&.gQ7^O'#ChOLXQYO1G1lOITQWO,5<pO&-UQWO,5<pOITQWO,5<rO&-UQWO,5<rOITQWO,5<_O&-UQWO,5<_O&*|QYO1G1mOLXQYO7+&iOITQWO1G1{O&-UQWO1G1{O&*|QYO7+'XO&*|QYO7+%|OITQWO7+'zO&-UQWO7+'zO&.qQWO'#E[O&.vQWO'#E[O&/OQWO'#EzO&/TQWO'#EgO&/YQWO'#JPO&/eQWO'#I}O&/pQWO,5:vO&/uQ#tO,5<QO&/|QWO'#FsO&0RQWO'#FsO&0WQWO,5<RO&0`QWO,5:vO&0hQ7^O1G0}O&0oQWO,5<aO&0tQWO,5<aO&0yQWO1G1mO&1OQWO1G0bO&1TQ#tO1G2`O&1[Q#tO1G2`O4QQWO'#FhO5_QWO'#FgOBwQWO'#EZOLXQYO,5;cO!(pQWO'#FuO!(pQWO'#FuOJdQWO,5<tOJdQWO,5<t",
    stateData: "&2X~O'WOS'XOSTOSUOS~OPTOQTOXyO]cO_hObnOcmOhcOjTOkcOlcOqTOsTOxRO{cO|cO}cO!TSO!_kO!dUO!gTO!hTO!iTO!jTO!kTO!nlO#dsO#tpO#x^O%PqO%RtO%TrO%UrO%XuO%ZvO%^wO%_wO%axO%nzO%t{O%v|O%x}O%z!OO%}!PO&T!QO&X!RO&Z!SO&]!TO&_!UO&a!VO'ZPO'dQO'mYO'zaO~OP[XZ[X_[Xj[Xu[Xv[Xx[X!R[X!a[X!b[X!d[X!j[X!{[X#WdX#[[X#][X#^[X#_[X#`[X#a[X#b[X#c[X#e[X#g[X#i[X#j[X#o[X'U[X'd[X'n[X'u[X'v[X~O!]$lX~P$zOR!WO'S!XO'T!ZO~OPTOQTO]cOb!kOc!jOhcOjTOkcOlcOqTOsTOxRO{cO|cO}cO!T!bO!_kO!dUO!gTO!hTO!iTO!jTO!kTO!n!iO#t!lO#x^O'Z![O'dQO'mYO'zaO~O!Q!`O!R!]O!O'hP!O'rP~P'dO!S!mO~P`OPTOQTO]cOb!kOc!jOhcOjTOkcOlcOqTOsTOxRO{cO|cO}cO!T!bO!_kO!dUO!gTO!hTO!iTO!jTO!kTO!n!iO#t!lO#x^O'Z9YO'dQO'mYO'zaO~OPTOQTO]cOb!kOc!jOhcOjTOkcOlcOqTOsTOxRO{cO|cO}cO!T!bO!_kO!dUO!gTO!hTO!iTO!jTO!kTO!n!iO#t!lO#x^O'dQO'mYO'zaO~O!Q!rO#U!uO#V!rO'Z9ZO!c'oP~P+{O#W!vO~O!]!wO#W!vO~OP#^OZ#dOj#ROu!{Ov!{Ox!|O!R#bO!a#TO!b!yO!d!zO!j#^O#[#PO#]#QO#^#QO#_#QO#`#SO#a#TO#b#TO#c#TO#e#UO#g#WO#i#YO#j#ZO'dQO'n#[O'u!}O'v#OO~O_'fX'U'fX!c'fX!O'fX!T'fX%Q'fX!]'fX~P.jO!{#eO#o#eOP'gXZ'gX_'gXj'gXu'gXv'gXx'gX!R'gX!a'gX!b'gX!d'gX!j'gX#['gX#]'gX#^'gX#_'gX#`'gX#a'gX#b'gX#e'gX#g'gX#i'gX#j'gX'd'gX'n'gX'u'gX'v'gX~O#c'gX'U'gX!O'gX!c'gXn'gX!T'gX%Q'gX!]'gX~P0zO!{#eO~O#z#fO$R#jO~O!T#kO#x^O$U#lO$W#nO~O]#qOh$OOj#rOk#qOl#qOq$POs$QOx#xO!T#yO!_$VO!d#vO#V$WO#t$TO$_$RO$a$SO$d$UO'Z#pO'd#sO'_'aP~O!d$XO~O!]$ZO~O_$[O'U$[O~O'Z$`O~O!d$XO'Z$`O'[$bO'`$cO~Oc$iO!d$XO'Z$`O~O#c#TO~O]$rOu$nO!T$kO!d$mO%R$qO'Z$`O'[$bO^(SP~O!n$sO~Ox$tO!T$uO'Z$`O~Ox$tO!T$uO%Z$yO'Z$`O~O'Z$zO~O#dsO%RtO%TrO%UrO%XuO%ZvO%^wO%_wO~Ob%TOc%SO!n%QO%P%RO%c%PO~P7uOb%WOcmO!T%VO!nlO#dsO%PqO%TrO%UrO%XuO%ZvO%^wO%_wO%axO~O`%ZO!{%^O%R%XO'[$bO~P8tO!d%_O!g%cO~O!d%dO~O!TSO~O_$[O'R%lO'U$[O~O_$[O'R%oO'U$[O~O_$[O'R%qO'U$[O~OR!WO'S!XO'T%uO~OP[XZ[Xj[Xu[Xv[Xx[X!R[X!RdX!a[X!b[X!d[X!j[X!{[X!{dX#WdX#[[X#][X#^[X#_[X#`[X#a[X#b[X#c[X#e[X#g[X#i[X#j[X#o[X'd[X'n[X'u[X'v[X~O!O[X!OdX~P;aO!Q%wO!O&gX!O&lX!R&gX!R&lX~P'dO!R%yO!O'hX~OP#^OZ#dOj#ROu!{Ov!{Ox!|O!R%yO!a#TO!b!yO!d!zO!j#^O#[#PO#]#QO#^#QO#_#QO#`#SO#a#TO#b#TO#c#TO#e#UO#g#WO#i#YO#j#ZO'dQO'n#[O'u!}O'v#OO~O!O'hX~P>^O!O&OO~Ox&RO!W&]O!X&UO!Y&UO'[$bO~O]&SOk&SO!Q&VO'e&PO!S'iP!S'tP~P@aO!O'qX!R'qX!]'qX!c'qX'n'qX~O!{'qX#W#PX!S'qX~PAYO!{&^O!O'sX!R'sX~O!R&_O!O'rX~O!O&bO~O!{#eO~PAYOS&fO!T&cO!o&eO'Z$`O~Oc&kO!d$XO'Z$`O~Ou$nO!d$mO~O!S&lO~P`Ou!{Ov!{Ox!|O!b!yO!d!zO'dQOP!faZ!faj!fa!R!fa!a!fa!j!fa#[!fa#]!fa#^!fa#_!fa#`!fa#a!fa#b!fa#c!fa#e!fa#g!fa#i!fa#j!fa'n!fa'u!fa'v!fa~O_!fa'U!fa!O!fa!c!fan!fa!T!fa%Q!fa!]!fa~PCcO!c&mO~O!]!wO!{&oO'n&nO!R'pX_'pX'U'pX~O!c'pX~PE{O!R&sO!c'oX~O!c&uO~Ox$tO!T$uO#V&vO'Z$`O~OPTOQTO]cOb!kOc!jOhcOjTOkcOlcOqTOsTOxRO{cO|cO}cO!TSO!_kO!dUO!gTO!hTO!iTO!jTO!kTO!n!iO#t!lO#x^O'Z9YO'dQO'mYO'zaO~O]#qOh$OOj#rOk#qOl#qOq$POs9lOx#xO!T#yO!_:oO!d#vO#V9rO#t$TO$_9nO$a9pO$d$UO'Z&zO'd#sO~O#W&|O~O]#qOh$OOj#rOk#qOl#qOq$POs$QOx#xO!T#yO!_$VO!d#vO#V$WO#t$TO$_$RO$a$SO$d$UO'Z&zO'd#sO~O'_'kP~PJdO!Q'QO!c'lP~P){O'e'SO'mYO~OP9VOQ9VO]cOb:mOc!jOhcOj9VOkcOlcOq9VOs9VOxRO{cO|cO}cO!T!bO!_9XO!dUO!g9VO!h9VO!i9VO!j9VO!k9VO!n!iO#t!lO#x^O'Z'bO'dQO'mYO'z:kO~O!d!zO~O!R#bO_$]a'U$]a!c$]a!O$]a!T$]a%Q$]a!]$]a~O#d'iO~PITO!]'kO!T'wX#w'wX#z'wX$R'wX~Ou'lO~P! POu'lO!T'wX#w'wX#z'wX$R'wX~O!T'nO#w'rO#z'mO$R'sO~O!Q'vO~PLXO#z#fO$R'yO~Ou$eXx$eX!b$eX'n$eX'u$eX'v$eX~OSfX!RfX!{fX'_fX'_$eX~P!!iOk'{O~OR'|O'S'}O'T(PO~Ou(ROx(SO'n#[O'u(UO'v(WO~O'_(QO~P!#rO'_(ZO~O]#qOh$OOj#rOk#qOl#qOq$POs9lOx#xO!T#yO!_:oO!d#vO#V9rO#t$TO$_9nO$a9pO$d$UO'd#sO~O!Q(_O'Z([O!c'{P~P!$aO#W(aO~O!Q(eO'Z(bO!O'|P~P!$aO_(nOj(sOx(kO!W(qO!X(jO!Y(jO!d(hO!x(rO$w(mO'[$bO'e(gO~O!S(pO~P!&XO!b!yOu'cXx'cX'n'cX'u'cX'v'cX!R'cX!{'cX~O'_'cX#m'cX~P!'TOS(vO!{(uO!R'bX'_'bX~O!R(wO'_'aX~O'Z(yO~O!d)OO~O'Z&zO~O!d(hO~Ox$tO!Q!rO!T$uO#U!uO#V!rO'Z$`O!c'oP~O!]!wO#W)SO~OP#^OZ#dOj#ROu!{Ov!{Ox!|O!a#TO!b!yO!d!zO!j#^O#[#PO#]#QO#^#QO#_#QO#`#SO#a#TO#b#TO#c#TO#e#UO#g#WO#i#YO#j#ZO'dQO'n#[O'u!}O'v#OO~O_!^a!R!^a'U!^a!O!^a!c!^an!^a!T!^a%Q!^a!]!^a~P!)fOS)[O!T&cO!o)ZO%Q)YO'`$cO~O'Z$zO'_'aP~O!])_O!T'^X_'^X'U'^X~O!d$XO'`$cO~O!d$XO'Z$`O'`$cO~O!]!wO#W&|O~O])jO%R)kO'Z)gO!S(TP~O!R)lO^(SX~O'e'SO~OZ)pO~O^)qO~O!T$kO'Z$`O'[$bO^(SP~Ox$tO!Q)vO!R&_O!T$uO'Z$`O!O'rP~O]&YOk&YO!Q)wO'e'SO!S'tP~O!R)xO_(PX'U(PX~O!{)|O'`$cO~OS*PO!T#yO'`$cO~O!T*RO~Ou*TO!TSO~O!n*YO~Oc*_O~O'Z(yO!S(RP~Oc$iO~O%RtO'Z$zO~P8tOZ*eO^*dO~OPTOQTO]cObnOcmOhcOjTOkcOlcOqTOsTOxRO{cO|cO}cO!_kO!dUO!gTO!hTO!iTO!jTO!kTO!nlO#x^O%PqO'dQO'mYO'zaO~O!T!bO#t!lO'Z9YO~P!1RO^*dO_$[O'U$[O~O_*iO#d*kO%T*kO%U*kO~P){O!d%_O~O%t*pO~O!T*rO~O&U*uO&V*tOP&SaQ&SaX&Sa]&Sa_&Sab&Sac&Sah&Saj&Sak&Sal&Saq&Sas&Sax&Sa{&Sa|&Sa}&Sa!T&Sa!_&Sa!d&Sa!g&Sa!h&Sa!i&Sa!j&Sa!k&Sa!n&Sa#d&Sa#t&Sa#x&Sa%P&Sa%R&Sa%T&Sa%U&Sa%X&Sa%Z&Sa%^&Sa%_&Sa%a&Sa%n&Sa%t&Sa%v&Sa%x&Sa%z&Sa%}&Sa&T&Sa&X&Sa&Z&Sa&]&Sa&_&Sa&a&Sa'Q&Sa'Z&Sa'd&Sa'm&Sa'z&Sa!S&Sa%{&Sa`&Sa&Q&Sa~O'Z*xO~On*{O~O!O&ga!R&ga~P!)fO!Q+PO!O&gX!R&gX~P){O!R%yO!O'ha~O!O'ha~P>^O!R&_O!O'ra~O!RwX!R!ZX!SwX!S!ZX!]wX!]!ZX!d!ZX!{wX'`!ZX~O!]+UO!{+TO!R#TX!R'jX!S#TX!S'jX!]'jX!d'jX'`'jX~O!]+WO!d$XO'`$cO!R!VX!S!VX~O]&QOk&QOx&RO'e(gO~OP9VOQ9VO]cOb:mOc!jOhcOj9VOkcOlcOq9VOs9VOxRO{cO|cO}cO!T!bO!_9XO!dUO!g9VO!h9VO!i9VO!j9VO!k9VO!n!iO#t!lO#x^O'dQO'mYO'z:kO~O'Z9vO~P!;^O!R+[O!S'iX~O!S+^O~O!]+UO!{+TO!R#TX!S#TX~O!R+_O!S'tX~O!S+aO~O]&QOk&QOx&RO'[$bO'e(gO~O!X+bO!Y+bO~P!>[Ox$tO!Q+dO!T$uO'Z$`O!O&lX!R&lX~O_+hO!W+kO!X+gO!Y+gO!r+oO!s+mO!t+nO!u+lO!x+pO'[$bO'e(gO'm+eO~O!S+jO~P!?]OS+uO!T&cO!o+tO~O!{+{O!R'pa!c'pa_'pa'U'pa~O!]!wO~P!@gO!R&sO!c'oa~Ox$tO!Q,OO!T$uO#U,QO#V,OO'Z$`O!R&nX!c&nX~O_#Oi!R#Oi'U#Oi!O#Oi!c#Oin#Oi!T#Oi%Q#Oi!]#Oi~P!)fO#W!za!R!za!c!za!{!za!T!za_!za'U!za!O!za~P!#rO#W'cXP'cXZ'cX_'cXj'cXv'cX!a'cX!d'cX!j'cX#['cX#]'cX#^'cX#_'cX#`'cX#a'cX#b'cX#c'cX#e'cX#g'cX#i'cX#j'cX'U'cX'd'cX!c'cX!O'cX!T'cXn'cX%Q'cX!]'cX~P!'TO!R,ZO'_'kX~P!#rO'_,]O~O!R,^O!c'lX~P!)fO!c,aO~O!O,bO~OP#^Ou!{Ov!{Ox!|O!b!yO!d!zO!j#^O'dQOZ#Zi_#Zij#Zi!R#Zi!a#Zi#]#Zi#^#Zi#_#Zi#`#Zi#a#Zi#b#Zi#c#Zi#e#Zi#g#Zi#i#Zi#j#Zi'U#Zi'n#Zi'u#Zi'v#Zi!O#Zi!c#Zin#Zi!T#Zi%Q#Zi!]#Zi~O#[#Zi~P!EtO#[#PO~P!EtOP#^Ou!{Ov!{Ox!|O!b!yO!d!zO!j#^O#[#PO#]#QO#^#QO#_#QO'dQOZ#Zi_#Zi!R#Zi!a#Zi#`#Zi#a#Zi#b#Zi#c#Zi#e#Zi#g#Zi#i#Zi#j#Zi'U#Zi'n#Zi'u#Zi'v#Zi!O#Zi!c#Zin#Zi!T#Zi%Q#Zi!]#Zi~Oj#Zi~P!H`Oj#RO~P!H`OP#^Oj#ROu!{Ov!{Ox!|O!b!yO!d!zO!j#^O#[#PO#]#QO#^#QO#_#QO#`#SO'dQO_#Zi!R#Zi#e#Zi#g#Zi#i#Zi#j#Zi'U#Zi'n#Zi'u#Zi'v#Zi!O#Zi!c#Zin#Zi!T#Zi%Q#Zi!]#Zi~OZ#Zi!a#Zi#a#Zi#b#Zi#c#Zi~P!JzOZ#dO!a#TO#a#TO#b#TO#c#TO~P!JzOP#^OZ#dOj#ROu!{Ov!{Ox!|O!a#TO!b!yO!d!zO!j#^O#[#PO#]#QO#^#QO#_#QO#`#SO#a#TO#b#TO#c#TO#e#UO'dQO_#Zi!R#Zi#g#Zi#i#Zi#j#Zi'U#Zi'n#Zi'v#Zi!O#Zi!c#Zin#Zi!T#Zi%Q#Zi!]#Zi~O'u#Zi~P!MrO'u!}O~P!MrOP#^OZ#dOj#ROu!{Ov!{Ox!|O!a#TO!b!yO!d!zO!j#^O#[#PO#]#QO#^#QO#_#QO#`#SO#a#TO#b#TO#c#TO#e#UO#g#WO'dQO'u!}O_#Zi!R#Zi#i#Zi#j#Zi'U#Zi'n#Zi!O#Zi!c#Zin#Zi!T#Zi%Q#Zi!]#Zi~O'v#Zi~P#!^O'v#OO~P#!^OP#^OZ#dOj#ROu!{Ov!{Ox!|O!a#TO!b!yO!d!zO!j#^O#[#PO#]#QO#^#QO#_#QO#`#SO#a#TO#b#TO#c#TO#e#UO#g#WO#i#YO'dQO'u!}O'v#OO~O_#Zi!R#Zi#j#Zi'U#Zi'n#Zi!O#Zi!c#Zin#Zi!T#Zi%Q#Zi!]#Zi~P#$xOP[XZ[Xj[Xu[Xv[Xx[X!a[X!b[X!d[X!j[X!{[X#WdX#[[X#][X#^[X#_[X#`[X#a[X#b[X#c[X#e[X#g[X#i[X#j[X#o[X'd[X'n[X'u[X'v[X!R[X!S[X~O#m[X~P#']OP#^OZ9jOj9_Ou!{Ov!{Ox!|O!a9aO!b!yO!d!zO!j#^O#[9]O#]9^O#^9^O#_9^O#`9`O#a9aO#b9aO#c9aO#e9bO#g9dO#i9fO#j9gO'dQO'n#[O'u!}O'v#OO~O#m,dO~P#)gOP'gXZ'gXj'gXu'gXv'gXx'gX!a'gX!b'gX!d'gX!j'gX#['gX#]'gX#^'gX#_'gX#`'gX#a'gX#b'gX#e'gX#g'gX#i'gX#j'gX'd'gX'n'gX'u'gX'v'gX!R'gX~O!{9kO#o9kO#c'gX#m'gX!S'gX~P#+bO_&qa!R&qa'U&qa!c&qan&qa!O&qa!T&qa%Q&qa!]&qa~P!)fOP#ZiZ#Zi_#Zij#Ziv#Zi!R#Zi!a#Zi!b#Zi!d#Zi!j#Zi#[#Zi#]#Zi#^#Zi#_#Zi#`#Zi#a#Zi#b#Zi#c#Zi#e#Zi#g#Zi#i#Zi#j#Zi'U#Zi'd#Zi!O#Zi!c#Zin#Zi!T#Zi%Q#Zi!]#Zi~P!#rO_#ni!R#ni'U#ni!O#ni!c#nin#ni!T#ni%Q#ni!]#ni~P!)fO#z,fO~O#z,gO~O!]'kO!{,hO!T$OX#w$OX#z$OX$R$OX~O!Q,iO~O!T'nO#w,kO#z'mO$R,lO~O!R9hO!S'fX~P#)gO!S,mO~O$R,oO~OR'|O'S'}O'T,rO~O],uOk,uO!O,vO~O!RdX!]dX!cdX!c$eX'ndX~P!!iO!c,|O~P!#rO!R,}O!]!wO'n&nO!c'{X~O!c-SO~O!O$eX!R$eX!]$lX~P!!iO!R-UO!O'|X~P!#rO!]-WO~O!O-YO~O!Q(_O'Z$`O!c'{P~Oj-^O!]!wO!d$XO'`$cO'n&nO~O!])_O~O!S-dO~P!&XO!X-eO!Y-eO'[$bO'e(gO~Ox-gO'e(gO~O!x-hO~O'Z$zO!R&vX'_&vX~O!R(wO'_'aa~Ou-mOv-mOx-nO'nra'ura'vra!Rra!{ra~O'_ra#mra~P#6fOu(ROx(SO'n$^a'u$^a'v$^a!R$^a!{$^a~O'_$^a#m$^a~P#7[Ou(ROx(SO'n$`a'u$`a'v$`a!R$`a!{$`a~O'_$`a#m$`a~P#7}O]-oO~O#W-pO~O'_$na!R$na#m$na!{$na~P!#rO#W-sO~OS-|O!T&cO!o-{O%Q-zO~O'_-}O~O]#qOj#rOk#qOl#qOq$POs9lOx#xO!T#yO!_:oO!d#vO#V9rO#t$TO$_9nO$a9pO$d$UO'd#sO~Oh.PO'Z.OO~P#9tO!])_O!T'^a_'^a'U'^a~O#W.VO~OZ[X!RdX!SdX~O!R.WO!S(TX~O!S.YO~OZ.ZO~O].]O'Z)gO~O!T$kO'Z$`O^'OX!R'OX~O!R)lO^(Sa~O!c.`O~P!)fO].bO~OZ.cO~O^.dO~OS-|O!T&cO!o-{O%Q-zO'`$cO~O!R)xO_(Pa'U(Pa~O!{.jO~OS.mO!T#yO~O'e'SO!S(QP~OS.wO!T.sO!o.vO%Q.uO'`$cO~OZ/RO!R/PO!S(RX~O!S/SO~O^/UO_$[O'U$[O~O]/VO~O]/WO'Z(yO~O#c/XO%r/YO~P0zO!{#eO#c/XO%r/YO~O_/ZO~P){O_/]O~O%{/aOP%yiQ%yiX%yi]%yi_%yib%yic%yih%yij%yik%yil%yiq%yis%yix%yi{%yi|%yi}%yi!T%yi!_%yi!d%yi!g%yi!h%yi!i%yi!j%yi!k%yi!n%yi#d%yi#t%yi#x%yi%P%yi%R%yi%T%yi%U%yi%X%yi%Z%yi%^%yi%_%yi%a%yi%n%yi%t%yi%v%yi%x%yi%z%yi%}%yi&T%yi&X%yi&Z%yi&]%yi&_%yi&a%yi'Q%yi'Z%yi'd%yi'm%yi'z%yi!S%yi`%yi&Q%yi~O`/gO!S/eO&Q/fO~P`O!TSO!d/jO~O!R#bOn$]a~O!O&gi!R&gi~P!)fO!R%yO!O'hi~O!R&_O!O'ri~O!O/nO~O!R!Va!S!Va~P#)gO]&QOk&QO!Q/tO'e(gO!R&hX!S&hX~P@aO!R+[O!S'ia~O]&YOk&YO!Q)wO'e'SO!R&mX!S&mX~O!R+_O!S'ta~O!O'si!R'si~P!)fO_$[O!]!wO!d$XO!j0OO!{/|O'U$[O'`$cO'n&nO~O!S0RO~P!?]O!X0SO!Y0SO'[$bO'e(gO'm+eO~O!W0TO~P#GyO!TSO!W0TO!u0VO!x0WO~P#GyO!W0TO!s0YO!t0YO!u0VO!x0WO~P#GyO!T&cO~O!T&cO~P!#rO!R'pi!c'pi_'pi'U'pi~P!)fO!{0cO!R'pi!c'pi_'pi'U'pi~O!R&sO!c'oi~Ox$tO!T$uO#V0eO'Z$`O~O#WraPraZra_rajra!ara!bra!dra!jra#[ra#]ra#^ra#_ra#`ra#ara#bra#cra#era#gra#ira#jra'Ura'dra!cra!Ora!Tranra%Qra!]ra~P#6fO#W$^aP$^aZ$^a_$^aj$^av$^a!a$^a!b$^a!d$^a!j$^a#[$^a#]$^a#^$^a#_$^a#`$^a#a$^a#b$^a#c$^a#e$^a#g$^a#i$^a#j$^a'U$^a'd$^a!c$^a!O$^a!T$^an$^a%Q$^a!]$^a~P#7[O#W$`aP$`aZ$`a_$`aj$`av$`a!a$`a!b$`a!d$`a!j$`a#[$`a#]$`a#^$`a#_$`a#`$`a#a$`a#b$`a#c$`a#e$`a#g$`a#i$`a#j$`a'U$`a'd$`a!c$`a!O$`a!T$`an$`a%Q$`a!]$`a~P#7}O#W$naP$naZ$na_$naj$nav$na!R$na!a$na!b$na!d$na!j$na#[$na#]$na#^$na#_$na#`$na#a$na#b$na#c$na#e$na#g$na#i$na#j$na'U$na'd$na!c$na!O$na!T$na!{$nan$na%Q$na!]$na~P!#rO_#Oq!R#Oq'U#Oq!O#Oq!c#Oqn#Oq!T#Oq%Q#Oq!]#Oq~P!)fO!R&iX'_&iX~PJdO!R,ZO'_'ka~O!Q0mO!R&jX!c&jX~P){O!R,^O!c'la~O!R,^O!c'la~P!)fO#m!fa!S!fa~PCcO#m!^a!R!^a!S!^a~P#)gO!T1QO#x^O$P1RO~O!S1VO~On1WO~P!#rO_$Yq!R$Yq'U$Yq!O$Yq!c$Yqn$Yq!T$Yq%Q$Yq!]$Yq~P!)fO!O1XO~O],uOk,uO~Ou(ROx(SO'v(WO'n$xi'u$xi!R$xi!{$xi~O'_$xi#m$xi~P$(jOu(ROx(SO'n$zi'u$zi'v$zi!R$zi!{$zi~O'_$zi#m$zi~P$)]O#m1YO~P!#rO!Q1[O'Z$`O!R&rX!c&rX~O!R,}O!c'{a~O!R,}O!]!wO!c'{a~O!R,}O!]!wO'n&nO!c'{a~O'_$gi!R$gi#m$gi!{$gi~P!#rO!Q1cO'Z(bO!O&tX!R&tX~P!$aO!R-UO!O'|a~O!R-UO!O'|a~P!#rO!]!wO~O!]!wO#c1mO~Oj1qO!]!wO'n&nO~O!R'bi'_'bi~P!#rO!{1tO!R'bi'_'bi~P!#rO!c1wO~O_$Zq!R$Zq'U$Zq!O$Zq!c$Zqn$Zq!T$Zq%Q$Zq!]$Zq~P!)fO!R1{O!T'}X~P!#rO!T&cO%Q2OO~O!T&cO%Q2OO~P!#rO!T$eX$u[X_$eX'U$eX~P!!iO$u2SOugXxgX!TgX'ngX'ugX'vgX_gX'UgX~O$u2SO~O]2YO%R2ZO'Z)gO!R&}X!S&}X~O!R.WO!S(Ta~OZ2_O~O^2`O~O]2cO~OS2eO!T&cO!o2dO%Q2OO~O_$[O'U$[O~P!#rO!T#yO~P!#rO!R2jO!{2lO!S(QX~O!S2mO~Ox(kO!W2vO!X2oO!Y2oO!r2uO!s2tO!t2tO!x2sO'[$bO'e(gO'm+eO~O!S2rO~P$1nOS2}O!T.sO!o2|O%Q2{O~OS2}O!T.sO!o2|O%Q2{O'`$cO~O'Z(yO!R&|X!S&|X~O!R/PO!S(Ra~O]3XO'e3WO~O]3YO~O^3[O~O!c3_O~P){O_3aO~O_3aO~P){O#c3cO%r3dO~PE{O`/gO!S3hO&Q/fO~P`O!]3jO~O&V3kOP&SqQ&SqX&Sq]&Sq_&Sqb&Sqc&Sqh&Sqj&Sqk&Sql&Sqq&Sqs&Sqx&Sq{&Sq|&Sq}&Sq!T&Sq!_&Sq!d&Sq!g&Sq!h&Sq!i&Sq!j&Sq!k&Sq!n&Sq#d&Sq#t&Sq#x&Sq%P&Sq%R&Sq%T&Sq%U&Sq%X&Sq%Z&Sq%^&Sq%_&Sq%a&Sq%n&Sq%t&Sq%v&Sq%x&Sq%z&Sq%}&Sq&T&Sq&X&Sq&Z&Sq&]&Sq&_&Sq&a&Sq'Q&Sq'Z&Sq'd&Sq'm&Sq'z&Sq!S&Sq%{&Sq`&Sq&Q&Sq~O!R#Ti!S#Ti~P#)gO!{3mO!R#Ti!S#Ti~O!R!Vi!S!Vi~P#)gO_$[O!{3tO'U$[O~O_$[O!]!wO!{3tO'U$[O~O!X3xO!Y3xO'[$bO'e(gO'm+eO~O_$[O!]!wO!d$XO!j3yO!{3tO'U$[O'`$cO'n&nO~O!W3zO~P$:ZO!W3zO!u3}O!x4OO~P$:ZO_$[O!]!wO!j3yO!{3tO'U$[O'n&nO~O!R'pq!c'pq_'pq'U'pq~P!)fO!R&sO!c'oq~O#W$xiP$xiZ$xi_$xij$xiv$xi!a$xi!b$xi!d$xi!j$xi#[$xi#]$xi#^$xi#_$xi#`$xi#a$xi#b$xi#c$xi#e$xi#g$xi#i$xi#j$xi'U$xi'd$xi!c$xi!O$xi!T$xin$xi%Q$xi!]$xi~P$(jO#W$ziP$ziZ$zi_$zij$ziv$zi!a$zi!b$zi!d$zi!j$zi#[$zi#]$zi#^$zi#_$zi#`$zi#a$zi#b$zi#c$zi#e$zi#g$zi#i$zi#j$zi'U$zi'd$zi!c$zi!O$zi!T$zin$zi%Q$zi!]$zi~P$)]O#W$giP$giZ$gi_$gij$giv$gi!R$gi!a$gi!b$gi!d$gi!j$gi#[$gi#]$gi#^$gi#_$gi#`$gi#a$gi#b$gi#c$gi#e$gi#g$gi#i$gi#j$gi'U$gi'd$gi!c$gi!O$gi!T$gi!{$gin$gi%Q$gi!]$gi~P!#rO!R&ia'_&ia~P!#rO!R&ja!c&ja~P!)fO!R,^O!c'li~O#m#Oi!R#Oi!S#Oi~P#)gOP#^Ou!{Ov!{Ox!|O!b!yO!d!zO!j#^O'dQOZ#Zij#Zi!a#Zi#]#Zi#^#Zi#_#Zi#`#Zi#a#Zi#b#Zi#c#Zi#e#Zi#g#Zi#i#Zi#j#Zi#m#Zi'n#Zi'u#Zi'v#Zi!R#Zi!S#Zi~O#[#Zi~P$CqO#[9]O~P$CqOP#^Ou!{Ov!{Ox!|O!b!yO!d!zO!j#^O#[9]O#]9^O#^9^O#_9^O'dQOZ#Zi!a#Zi#`#Zi#a#Zi#b#Zi#c#Zi#e#Zi#g#Zi#i#Zi#j#Zi#m#Zi'n#Zi'u#Zi'v#Zi!R#Zi!S#Zi~Oj#Zi~P$EyOj9_O~P$EyOP#^Oj9_Ou!{Ov!{Ox!|O!b!yO!d!zO!j#^O#[9]O#]9^O#^9^O#_9^O#`9`O'dQO#e#Zi#g#Zi#i#Zi#j#Zi#m#Zi'n#Zi'u#Zi'v#Zi!R#Zi!S#Zi~OZ#Zi!a#Zi#a#Zi#b#Zi#c#Zi~P$HROZ9jO!a9aO#a9aO#b9aO#c9aO~P$HROP#^OZ9jOj9_Ou!{Ov!{Ox!|O!a9aO!b!yO!d!zO!j#^O#[9]O#]9^O#^9^O#_9^O#`9`O#a9aO#b9aO#c9aO#e9bO'dQO#g#Zi#i#Zi#j#Zi#m#Zi'n#Zi'v#Zi!R#Zi!S#Zi~O'u#Zi~P$JgO'u!}O~P$JgOP#^OZ9jOj9_Ou!{Ov!{Ox!|O!a9aO!b!yO!d!zO!j#^O#[9]O#]9^O#^9^O#_9^O#`9`O#a9aO#b9aO#c9aO#e9bO#g9dO'dQO'u!}O#i#Zi#j#Zi#m#Zi'n#Zi!R#Zi!S#Zi~O'v#Zi~P$LoO'v#OO~P$LoOP#^OZ9jOj9_Ou!{Ov!{Ox!|O!a9aO!b!yO!d!zO!j#^O#[9]O#]9^O#^9^O#_9^O#`9`O#a9aO#b9aO#c9aO#e9bO#g9dO#i9fO'dQO'u!}O'v#OO~O#j#Zi#m#Zi'n#Zi!R#Zi!S#Zi~P$NwO_#ky!R#ky'U#ky!O#ky!c#kyn#ky!T#ky%Q#ky!]#ky~P!)fOP#ZiZ#Zij#Ziv#Zi!a#Zi!b#Zi!d#Zi!j#Zi#[#Zi#]#Zi#^#Zi#_#Zi#`#Zi#a#Zi#b#Zi#c#Zi#e#Zi#g#Zi#i#Zi#j#Zi#m#Zi'd#Zi!R#Zi!S#Zi~P!#rO!b!yOP'cXZ'cXj'cXu'cXv'cXx'cX!a'cX!d'cX!j'cX#['cX#]'cX#^'cX#_'cX#`'cX#a'cX#b'cX#c'cX#e'cX#g'cX#i'cX#j'cX#m'cX'd'cX'n'cX'u'cX'v'cX!R'cX!S'cX~O#m#ni!R#ni!S#ni~P#)gO!S4`O~O!R&qa!S&qa~P#)gO!]!wO'n&nO!R&ra!c&ra~O!R,}O!c'{i~O!R,}O!]!wO!c'{i~O!O&ta!R&ta~P!#rO!]4gO~O!R-UO!O'|i~P!#rO!R-UO!O'|i~O!O4mO~O!]!wO#c4sO~Oj4tO!]!wO'n&nO~O!O4vO~O'_$iq!R$iq#m$iq!{$iq~P!#rO_$Zy!R$Zy'U$Zy!O$Zy!c$Zyn$Zy!T$Zy%Q$Zy!]$Zy~P!)fO!R1{O!T'}a~O!T&cO%Q4{O~O!T&cO%Q4{O~P!#rO_#Oy!R#Oy'U#Oy!O#Oy!c#Oyn#Oy!T#Oy%Q#Oy!]#Oy~P!)fOZ5OO~O]5QO'Z)gO~O!R.WO!S(Ti~O]5TO~O^5UO~O'e'SO!R&yX!S&yX~O!R2jO!S(Qa~O!S5cO~P$1nOx-gO'e(gO'm+eO~O!W5fO!X5eO!Y5eO!x0WO'[$bO'e(gO'm+eO~O!s5gO!t5gO~P%-iO!X5eO!Y5eO'[$bO'e(gO'm+eO~O!T.sO~O!T.sO%Q5iO~O!T.sO%Q5iO~P!#rOS5nO!T.sO!o5mO%Q5iO~OZ5sO!R&|a!S&|a~O!R/PO!S(Ri~O]5vO~O!c5wO~O!c5xO~O!c5yO~O!c5yO~P){O_5{O~O!]6OO~O!c6QO~O!R'si!S'si~P#)gO_$[O'U$[O~P!)fO_$[O!{6VO'U$[O~O_$[O!]!wO!{6VO'U$[O~O!X6[O!Y6[O'[$bO'e(gO'm+eO~O_$[O!]!wO!j6]O!{6VO'U$[O'n&nO~O!d$XO'`$cO~P%2TO!W6^O~P%1rO!R'py!c'py_'py'U'py~P!)fO#W$iqP$iqZ$iq_$iqj$iqv$iq!R$iq!a$iq!b$iq!d$iq!j$iq#[$iq#]$iq#^$iq#_$iq#`$iq#a$iq#b$iq#c$iq#e$iq#g$iq#i$iq#j$iq'U$iq'd$iq!c$iq!O$iq!T$iq!{$iqn$iq%Q$iq!]$iq~P!#rO!R&ji!c&ji~P!)fO#m#Oq!R#Oq!S#Oq~P#)gOu-mOv-mOx-nOPraZrajra!ara!bra!dra!jra#[ra#]ra#^ra#_ra#`ra#ara#bra#cra#era#gra#ira#jra#mra'dra'nra'ura'vra!Rra!Sra~Ou(ROx(SOP$^aZ$^aj$^av$^a!a$^a!b$^a!d$^a!j$^a#[$^a#]$^a#^$^a#_$^a#`$^a#a$^a#b$^a#c$^a#e$^a#g$^a#i$^a#j$^a#m$^a'd$^a'n$^a'u$^a'v$^a!R$^a!S$^a~Ou(ROx(SOP$`aZ$`aj$`av$`a!a$`a!b$`a!d$`a!j$`a#[$`a#]$`a#^$`a#_$`a#`$`a#a$`a#b$`a#c$`a#e$`a#g$`a#i$`a#j$`a#m$`a'd$`a'n$`a'u$`a'v$`a!R$`a!S$`a~OP$naZ$naj$nav$na!a$na!b$na!d$na!j$na#[$na#]$na#^$na#_$na#`$na#a$na#b$na#c$na#e$na#g$na#i$na#j$na#m$na'd$na!R$na!S$na~P!#rO#m$Yq!R$Yq!S$Yq~P#)gO#m$Zq!R$Zq!S$Zq~P#)gO!S6hO~O'_$|y!R$|y#m$|y!{$|y~P!#rO!]!wO!R&ri!c&ri~O!]!wO'n&nO!R&ri!c&ri~O!R,}O!c'{q~O!O&ti!R&ti~P!#rO!R-UO!O'|q~O!O6oO~P!#rO!O6oO~O!R'by'_'by~P!#rO!R&wa!T&wa~P!#rO!T$tq_$tq'U$tq~P!#rOZ6wO~O!R.WO!S(Tq~O]6zO~O!T&cO%Q6{O~O!T&cO%Q6{O~P!#rO!{6|O!R&ya!S&ya~O!R2jO!S(Qi~P#)gO!X7SO!Y7SO'[$bO'e(gO'm+eO~O!W7UO!x4OO~P%ArO!T.sO%Q7XO~O!T.sO%Q7XO~P!#rO]7`O'e7_O~O!R/PO!S(Rq~O!c7bO~O!c7bO~P){O!c7dO~O!c7eO~O!R#Ty!S#Ty~P#)gO_$[O!{7kO'U$[O~O_$[O!]!wO!{7kO'U$[O~O!X7nO!Y7nO'[$bO'e(gO'm+eO~O_$[O!]!wO!j7oO!{7kO'U$[O'n&nO~O#W$|yP$|yZ$|y_$|yj$|yv$|y!R$|y!a$|y!b$|y!d$|y!j$|y#[$|y#]$|y#^$|y#_$|y#`$|y#a$|y#b$|y#c$|y#e$|y#g$|y#i$|y#j$|y'U$|y'd$|y!c$|y!O$|y!T$|y!{$|yn$|y%Q$|y!]$|y~P!#rO#m#ky!R#ky!S#ky~P#)gOP$giZ$gij$giv$gi!a$gi!b$gi!d$gi!j$gi#[$gi#]$gi#^$gi#_$gi#`$gi#a$gi#b$gi#c$gi#e$gi#g$gi#i$gi#j$gi#m$gi'd$gi!R$gi!S$gi~P!#rOu(ROx(SO'v(WOP$xiZ$xij$xiv$xi!a$xi!b$xi!d$xi!j$xi#[$xi#]$xi#^$xi#_$xi#`$xi#a$xi#b$xi#c$xi#e$xi#g$xi#i$xi#j$xi#m$xi'd$xi'n$xi'u$xi!R$xi!S$xi~Ou(ROx(SOP$ziZ$zij$ziv$zi!a$zi!b$zi!d$zi!j$zi#[$zi#]$zi#^$zi#_$zi#`$zi#a$zi#b$zi#c$zi#e$zi#g$zi#i$zi#j$zi#m$zi'd$zi'n$zi'u$zi'v$zi!R$zi!S$zi~O#m$Zy!R$Zy!S$Zy~P#)gO#m#Oy!R#Oy!S#Oy~P#)gO!]!wO!R&rq!c&rq~O!R,}O!c'{y~O!O&tq!R&tq~P!#rO!O7uO~P!#rO!R.WO!S(Ty~O!R2jO!S(Qq~O!X8RO!Y8RO'[$bO'e(gO'm+eO~O!T.sO%Q8UO~O!T.sO%Q8UO~P!#rO!c8XO~O&V8YOP&S!ZQ&S!ZX&S!Z]&S!Z_&S!Zb&S!Zc&S!Zh&S!Zj&S!Zk&S!Zl&S!Zq&S!Zs&S!Zx&S!Z{&S!Z|&S!Z}&S!Z!T&S!Z!_&S!Z!d&S!Z!g&S!Z!h&S!Z!i&S!Z!j&S!Z!k&S!Z!n&S!Z#d&S!Z#t&S!Z#x&S!Z%P&S!Z%R&S!Z%T&S!Z%U&S!Z%X&S!Z%Z&S!Z%^&S!Z%_&S!Z%a&S!Z%n&S!Z%t&S!Z%v&S!Z%x&S!Z%z&S!Z%}&S!Z&T&S!Z&X&S!Z&Z&S!Z&]&S!Z&_&S!Z&a&S!Z'Q&S!Z'Z&S!Z'd&S!Z'm&S!Z'z&S!Z!S&S!Z%{&S!Z`&S!Z&Q&S!Z~O_$[O!{8_O'U$[O~O_$[O!]!wO!{8_O'U$[O~OP$iqZ$iqj$iqv$iq!a$iq!b$iq!d$iq!j$iq#[$iq#]$iq#^$iq#_$iq#`$iq#a$iq#b$iq#c$iq#e$iq#g$iq#i$iq#j$iq#m$iq'd$iq!R$iq!S$iq~P!#rO!R&yq!S&yq~P#)gO_$[O!{8tO'U$[O~OP$|yZ$|yj$|yv$|y!a$|y!b$|y!d$|y!j$|y#[$|y#]$|y#^$|y#_$|y#`$|y#a$|y#b$|y#c$|y#e$|y#g$|y#i$|y#j$|y#m$|y'd$|y!R$|y!S$|y~P!#rOn'fX~P.jOn[X!O[X!c[X%r[X!T[X%Q[X!][X~P$zO!]dX!c[X!cdX'ndX~P;aOP9VOQ9VO]cOb:mOc!jOhcOj9VOkcOlcOq9VOs9VOxRO{cO|cO}cO!TSO!_9XO!dUO!g9VO!h9VO!i9VO!j9VO!k9VO!n!iO#t!lO#x^O'Z'bO'dQO'mYO'z:kO~O!R9hO!S$]a~O]#qOh$OOj#rOk#qOl#qOq$POs9mOx#xO!T#yO!_:pO!d#vO#V9sO#t$TO$_9oO$a9qO$d$UO'Z&zO'd#sO~O#d'iO~P&-UO!S[X!SdX~P;aO#W9[O~O!]!wO#W9[O~O!{9kO~O#c9aO~O!{9tO!R'sX!S'sX~O!{9kO!R'qX!S'qX~O#W9uO~O'_9wO~P!#rO#W9|O~O#W9}O~O!]!wO#W:OO~O!]!wO#W9uO~O#m:PO~P#)gO#W:QO~O#W:RO~O#W:SO~O#W:TO~O#m:UO~P!#rO#m:VO~P!#rO#x~!b!r!t!u#U#V'z$_$a$d$u%P%Q%R%X%Z%^%_%a%c~UT#x'z#]}'W'X#z'W'Z'e~",
    goto: "#Ed(XPPPPPPPP(YP(jP*^PPPP-uPP.[3n5b5uP5uPPP5uP7c5uP5uP7gPP7lP8Q<cPPPP<gPPPP<g?XPPP?_AjP<gPDTPPPPE{<gPPPPPGt<gPPJuKrPPPPKvM`PMhNiPKr<g<g!#p!&k!+^!+^!.mPPP!.t!1j<gPPPPPPPPPP!4aP!5rPP<g!7PP<gP<g<g<g<gP<g!9dPP!<]P!?Q!?Y!?^!?^P!<YP!?b!?bP!BVP!BZ<g<g!Ba!ET5uP5uP5u5uP!FW5u5u!HO5u!JQ5u!Kr5u5u!L`!NY!NY!N^!NY!NfP!NYP5u# b5u#!l5u5u-uPPP##yPP#$c#$cP#$cP#$x#$cPP#%OP#$uP#$u#%bMd#$u#&P#&V#&Y(Y#&](YP#&d#&d#&dP(YP(YP(YP(YPP(YP#&j#&mP#&m(YPPP(YP(YP(YP(YP(YP(Y(Y#&q#&{#'R#'X#'g#'m#'s#'}#(T#(d#(j#(x#)O#)U#)d#)y#+]#+k#+q#+w#+}#,T#,_#,e#,k#,u#-X#-_PPPPPPPP#-ePP#.X#2VPP#3m#3t#3|PP#8Y#:m#@i#@l#@o#@z#@}PP#AQ#AU#As#Bj#Bn#CSPP#CW#C^#CbP#Ce#Ci#Cl#D[#Dr#Dw#Dz#D}#ET#EW#E[#E`mhOSj}!n$Z%b%e%f%h*m*r/a/dQ$hmQ$opQ%YyS&U!b+[Q&j!jS(j#y(oQ)e$iQ)r$qQ*^%SQ+b&]S+g&c+iQ+y&kQ-e(qQ/O*_Y0S+k+l+m+n+oS2o.s2qU3x0T0V0YU5e2t2u2vS6[3z3}S7S5f5gQ7n6^R8R7U$p[ORSTUjk}!S!W!]!`!n!v!z!|#P#Q#R#S#T#U#V#W#X#Y#Z#b#e$Z$m%Z%^%b%d%e%f%h%l%w%y&R&^&e&o&|'Q(Q)S)Z*i*m*r+P+t+{,^,d-n-s-{.V.v/X/Y/Z/]/a/d/f/|0c0m2d2|3a3c3d3t5m5{6V7k8_8t!j'd#]#k&V'v+T+W,i/t1Q2l3m6|9V9X9[9]9^9_9`9a9b9c9d9e9f9g9h9k9t9u9w:O:P:S:T:nQ(z$QQ)j$kQ*`%VQ*g%_Q,T9lQ.Q)_Q.])kQ/W*eQ2Y.WQ3U/PQ4X9mR5Q2ZpeOSjy}!n$Z%X%b%e%f%h*m*r/a/dR*b%Z&WVOSTjkn}!S!W!k!n!v!z!|#P#Q#R#S#T#U#V#W#X#Y#Z#]#b#e#k$Z$m%Z%^%_%b%d%e%f%h%l%y&R&^&e&o&|'Q'v(Q)S)Z*i*m*r+P+T+W+t+{,^,d,i-n-s-{.V.v/X/Y/Z/]/a/d/f/t/|0c0m1Q2d2l2|3a3c3d3m3t5m5{6V6|7k8_8t9V9X9[9]9^9_9`9a9b9c9d9e9f9g9h9k9t9u9w:O:P:S:T:m:n[!cRU!]!`%w&VQ$alQ$gmS$lp$qv$vrs!r!u$X$t&_&s&v)v)w)x*k+U+d,O,Q/j0eQ%OwQ&g!iQ&i!jS(^#v(hS)d$h$iQ)h$kQ)u$sQ*X%QQ*]%SS+x&j&kQ-R(_Q.U)eQ.[)kQ.^)lQ.a)pQ.y*YS.}*^*_Q0a+yQ1Z,}Q2X.WQ2].ZQ2b.cQ3T/OQ4d1[Q5P2ZQ5S2_Q6v5OR7x6w!Y$em!j$g$h$i&T&i&j&k(i)d)e+X+f+x+y-_.U/y0P0U0a1p3w3|6Y7l8`Q)]$aQ)}${Q*Q$|Q*[%SQ.e)uQ.x*XU.|*]*^*_Q3O.yS3S.}/OQ5`2nQ5r3TS7Q5a5dS8P7R7TQ8j8QR8y8kW#|a$c(w:kS${t%XQ$|uQ$}vR){$y$V#{a!w!y#d#v#x$R$S$W&f'|(V(X(Y(a(e(u(v)Y)[)_)|*P+u,Z-U-W-p-z-|.j.m.u.w1Y1c1m1t1{2O2S2e2{2}4g4s4{5i5n6{7X8U9j9n9o9p9q9r9s9x9y9z9{9|9}:Q:R:U:V:k:q:rT'}#s(OV({$Q9l9mU&Y!b$u+_Q'T!{Q)o$nQ.n*RQ1u-mR5[2j&^cORSTUjk}!S!W!]!`!n!v!z!|#P#Q#R#S#T#U#V#W#X#Y#Z#]#b#e#k$Z$m%Z%^%_%b%d%e%f%h%l%w%y&R&V&^&e&o&|'Q'v(Q)S)Z*i*m*r+P+T+W+t+{,^,d,i-n-s-{.V.v/X/Y/Z/]/a/d/f/t/|0c0m1Q2d2l2|3a3c3d3m3t5m5{6V6|7k8_8t9V9X9[9]9^9_9`9a9b9c9d9e9f9g9h9k9t9u9w:O:P:S:T:n$]#aZ!_!o$_%v%|&x'P'V'W'X'Y'Z'[']'^'_'`'a'c'f'j't)n*}+Y+c+z,Y,`,c,e,s-q/o/r0b0l0p0q0r0s0t0u0v0w0x0y0z0{0|1P1U1y2V3o3r4S4V4W4]4^5^6R6U6b6f6g7h7{8]8r8}9W:dT!XQ!Y&_cORSTUjk}!S!W!]!`!n!v!z!|#P#Q#R#S#T#U#V#W#X#Y#Z#]#b#e#k$Z$m%Z%^%_%b%d%e%f%h%l%w%y&R&V&^&e&o&|'Q'v(Q)S)Z*i*m*r+P+T+W+t+{,^,d,i-n-s-{.V.v/X/Y/Z/]/a/d/f/t/|0c0m1Q2d2l2|3a3c3d3m3t5m5{6V6|7k8_8t9V9X9[9]9^9_9`9a9b9c9d9e9f9g9h9k9t9u9w:O:P:S:T:nQ&W!bR/u+[Y&Q!b&U&]+[+bS(i#y(oS+f&c+iS-_(j(qQ-`(kQ-f(rQ.p*TU0P+g+k+lU0U+m+n+oS0Z+p2sQ1p-eQ1r-gQ1s-hS2n.s2qU3w0S0T0VQ3{0WQ3|0YS5a2o2vS5d2t2uU6Y3x3z3}Q6_4OS7R5e5fQ7T5gS7l6[6^S8Q7S7UQ8`7nR8k8RlhOSj}!n$Z%b%e%f%h*m*r/a/dQ%j!QS&w!v9[Q)b$fQ*V%OQ*W%PQ+v&hS,X&|9uS-r)S:OQ.S)cQ.r*UQ/h*tQ/i*uQ/q+VQ0X+mQ0_+wS1z-s:SQ2T.TS2W.V:TQ3n/sQ3q/zQ4Q0`Q4}2UQ6P3kQ6S3pQ6W3vQ6`4RQ7f6QQ7i6XQ8[7jQ8o8YQ8q8^R8|8s$W#`Z!_!o%v%|&x'P'V'W'X'Y'Z'[']'^'_'`'a'c'f'j't)n*}+Y+c+z,Y,`,c,s-q/o/r0b0l0p0q0r0s0t0u0v0w0x0y0z0{0|1P1U1y2V3o3r4S4V4W4]4^5^6R6U6b6f6g7h7{8]8r8}9W:dU(t#z&{1OT)W$_,e$W#_Z!_!o%v%|&x'P'V'W'X'Y'Z'[']'^'_'`'a'c'f'j't)n*}+Y+c+z,Y,`,c,s-q/o/r0b0l0p0q0r0s0t0u0v0w0x0y0z0{0|1P1U1y2V3o3r4S4V4W4]4^5^6R6U6b6f6g7h7{8]8r8}9W:dQ'e#`S)V$_,eR-t)W&^cORSTUjk}!S!W!]!`!n!v!z!|#P#Q#R#S#T#U#V#W#X#Y#Z#]#b#e#k$Z$m%Z%^%_%b%d%e%f%h%l%w%y&R&V&^&e&o&|'Q'v(Q)S)Z*i*m*r+P+T+W+t+{,^,d,i-n-s-{.V.v/X/Y/Z/]/a/d/f/t/|0c0m1Q2d2l2|3a3c3d3m3t5m5{6V6|7k8_8t9V9X9[9]9^9_9`9a9b9c9d9e9f9g9h9k9t9u9w:O:P:S:T:nQ%e{Q%f|Q%h!OQ%i!PR/`*pQ&d!iQ)X$aQ+s&gS-y)])uS0[+q+rW1}-v-w-x.eS4P0]0^U4z2P2Q2RU6t4y5W5XQ7w6uR8f7zT+h&c+iS+f&c+iU0P+g+k+lU0U+m+n+oS0Z+p2sS2n.s2qU3w0S0T0VQ3{0WQ3|0YS5a2o2vS5d2t2uU6Y3x3z3}Q6_4OS7R5e5fQ7T5gS7l6[6^S8Q7S7UQ8`7nR8k8RS+h&c+iT2p.s2qS&q!q/^Q-Q(^Q-](iS0O+f2nQ1`-RS1j-^-fU3y0U0Z5dQ4c1ZS4q1q1sU6]3{3|7TQ6j4dQ6s4tR7o6_Q!xXS&p!q/^Q)T$YQ)`$dQ)f$jQ+|&qQ-P(^Q-[(iQ-a(lQ.R)aQ.z*ZS/}+f2nS1_-Q-RS1i-]-fQ1l-`Q1o-bQ3Q.{W3u0O0U0Z5dQ4b1ZQ4f1`S4k1j1sQ4r1rQ5p3RW6Z3y3{3|7TS6i4c4dQ6n4mQ6q4qQ7O5_Q7]5qS7m6]6_Q7q6jQ7s6oQ7v6sQ7}7PQ8W7^Q8a7oQ8d7uQ8h8OQ8w8iQ9P8xQ9T9QQ:^:XQ:g:bR:h:c$rWORSTUjk}!S!W!]!`!n!v!z!|#P#Q#R#S#T#U#V#W#X#Y#Z#b#e$Z$m%Z%^%_%b%d%e%f%h%l%w%y&R&^&e&o&|'Q(Q)S)Z*i*m*r+P+t+{,^,d-n-s-{.V.v/X/Y/Z/]/a/d/f/|0c0m2d2|3a3c3d3t5m5{6V7k8_8tS!xn!k!j:W#]#k&V'v+T+W,i/t1Q2l3m6|9V9X9[9]9^9_9`9a9b9c9d9e9f9g9h9k9t9u9w:O:P:S:T:nR:^:m$rXORSTUjk}!S!W!]!`!n!v!z!|#P#Q#R#S#T#U#V#W#X#Y#Z#b#e$Z$m%Z%^%_%b%d%e%f%h%l%w%y&R&^&e&o&|'Q(Q)S)Z*i*m*r+P+t+{,^,d-n-s-{.V.v/X/Y/Z/]/a/d/f/|0c0m2d2|3a3c3d3t5m5{6V7k8_8tQ$Yb!Y$dm!j$g$h$i&T&i&j&k(i)d)e+X+f+x+y-_.U/y0P0U0a1p3w3|6Y7l8`S$jn!kQ)a$eQ*Z%SW.{*[*]*^*_U3R.|.}/OQ5_2nS5q3S3TU7P5`5a5dQ7^5rU8O7Q7R7TS8i8P8QS8x8j8kQ9Q8y!j:X#]#k&V'v+T+W,i/t1Q2l3m6|9V9X9[9]9^9_9`9a9b9c9d9e9f9g9h9k9t9u9w:O:P:S:T:nQ:b:lR:c:m$f]OSTjk}!S!W!n!v!z!|#P#Q#R#S#T#U#V#W#X#Y#Z#b#e$Z$m%Z%^%b%d%e%f%h%l%y&R&^&e&o&|'Q(Q)S)Z*i*m*r+P+t+{,^,d-n-s-{.V.v/X/Y/Z/]/a/d/f/|0c0m2d2|3a3c3d3t5m5{6V7k8_8tY!hRU!]!`%wv$vrs!r!u$X$t&_&s&v)v)w)x*k+U+d,O,Q/j0eQ*h%_!h:Y#]#k'v+T+W,i/t1Q2l3m6|9V9X9[9]9^9_9`9a9b9c9d9e9f9g9h9k9t9u9w:O:P:S:T:nR:]&VS&Z!b$uR/w+_$p[ORSTUjk}!S!W!]!`!n!v!z!|#P#Q#R#S#T#U#V#W#X#Y#Z#b#e$Z$m%Z%^%b%d%e%f%h%l%w%y&R&^&e&o&|'Q(Q)S)Z*i*m*r+P+t+{,^,d-n-s-{.V.v/X/Y/Z/]/a/d/f/|0c0m2d2|3a3c3d3t5m5{6V7k8_8t!j'd#]#k&V'v+T+W,i/t1Q2l3m6|9V9X9[9]9^9_9`9a9b9c9d9e9f9g9h9k9t9u9w:O:P:S:T:nR*g%_$roORSTUjk}!S!W!]!`!n!v!z!|#P#Q#R#S#T#U#V#W#X#Y#Z#b#e$Z$m%Z%^%_%b%d%e%f%h%l%w%y&R&^&e&o&|'Q(Q)S)Z*i*m*r+P+t+{,^,d-n-s-{.V.v/X/Y/Z/]/a/d/f/|0c0m2d2|3a3c3d3t5m5{6V7k8_8tQ'T!{!k:Z#]#k&V'v+T+W,i/t1Q2l3m6|9V9X9[9]9^9_9`9a9b9c9d9e9f9g9h9k9t9u9w:O:P:S:T:n!h#VZ!_$_%v%|&x'P'^'_'`'a'f'j)n*}+c+z,Y,`,s-q0b0l0|1y2V3r4S4V6U7h8]8r8}9W!R9c'c't+Y,e/o/r0p0x0y0z0{1P1U3o4W4]4^5^6R6b6f6g7{:d!d#XZ!_$_%v%|&x'P'`'a'f'j)n*}+c+z,Y,`,s-q0b0l0|1y2V3r4S4V6U7h8]8r8}9W}9e'c't+Y,e/o/r0p0z0{1P1U3o4W4]4^5^6R6b6f6g7{:d!`#]Z!_$_%v%|&x'P'f'j)n*}+c+z,Y,`,s-q0b0l0|1y2V3r4S4V6U7h8]8r8}9Wl(Y#t&})R,{-T-i-j0j1x4a4u:_:i:jx:n'c't+Y,e/o/r0p1P1U3o4W4]4^5^6R6b6f6g7{:d!`:q&y'h(](c+r,W,p-X-u-x.i.k0^0i1a1e2R2g2i2y4U4h4n4w4|5X5l6a6l6r7ZZ:r0}4[6c7p8b&^cORSTUjk}!S!W!]!`!n!v!z!|#P#Q#R#S#T#U#V#W#X#Y#Z#]#b#e#k$Z$m%Z%^%_%b%d%e%f%h%l%w%y&R&V&^&e&o&|'Q'v(Q)S)Z*i*m*r+P+T+W+t+{,^,d,i-n-s-{.V.v/X/Y/Z/]/a/d/f/t/|0c0m1Q2d2l2|3a3c3d3m3t5m5{6V6|7k8_8t9V9X9[9]9^9_9`9a9b9c9d9e9f9g9h9k9t9u9w:O:P:S:T:nS#l`#mR1R,h&e_ORSTU`jk}!S!W!]!`!n!v!z!|#P#Q#R#S#T#U#V#W#X#Y#Z#]#b#e#k#m$Z$m%Z%^%_%b%d%e%f%h%l%w%y&R&V&^&e&o&|'Q'v(Q)S)Z*i*m*r+P+T+W+t+{,^,d,h,i-n-s-{.V.v/X/Y/Z/]/a/d/f/t/|0c0m1Q2d2l2|3a3c3d3m3t5m5{6V6|7k8_8t9V9X9[9]9^9_9`9a9b9c9d9e9f9g9h9k9t9u9w:O:P:S:T:nS#g^#nT'm#i'qT#h^#nT'o#i'q&e`ORSTU`jk}!S!W!]!`!n!v!z!|#P#Q#R#S#T#U#V#W#X#Y#Z#]#b#e#k#m$Z$m%Z%^%_%b%d%e%f%h%l%w%y&R&V&^&e&o&|'Q'v(Q)S)Z*i*m*r+P+T+W+t+{,^,d,h,i-n-s-{.V.v/X/Y/Z/]/a/d/f/t/|0c0m1Q2d2l2|3a3c3d3m3t5m5{6V6|7k8_8t9V9X9[9]9^9_9`9a9b9c9d9e9f9g9h9k9t9u9w:O:P:S:T:nT#l`#mQ#o`R'x#m$rbORSTUjk}!S!W!]!`!n!v!z!|#P#Q#R#S#T#U#V#W#X#Y#Z#b#e$Z$m%Z%^%_%b%d%e%f%h%l%w%y&R&^&e&o&|'Q(Q)S)Z*i*m*r+P+t+{,^,d-n-s-{.V.v/X/Y/Z/]/a/d/f/|0c0m2d2|3a3c3d3t5m5{6V7k8_8t!k:l#]#k&V'v+T+W,i/t1Q2l3m6|9V9X9[9]9^9_9`9a9b9c9d9e9f9g9h9k9t9u9w:O:P:S:T:n#RdOSUj}!S!W!n!|#k$Z%Z%^%_%b%d%e%f%h%l&R&e'v)Z*i*m*r+t,i-n-{.v/X/Y/Z/]/a/d/f1Q2d2|3a3c3d5m5{t#za!y$R$S$W(V(X(Y(a(u(v,Z-p1Y1t:k:q:r!|&{!w#d#v#x&f'|(e)Y)[)_)|*P+u-U-W-z-|.j.m.u.w1c1m1{2O2S2e2{2}4g4s4{5i5n6{7X8U9n9p9r9x9z9|:Q:UQ)P$UQ,t(Rc1O9j9o9q9s9y9{9}:R:Vt#wa!y$R$S$W(V(X(Y(a(u(v,Z-p1Y1t:k:q:rS(l#y(oQ)Q$VQ-b(m!|:`!w#d#v#x&f'|(e)Y)[)_)|*P+u-U-W-z-|.j.m.u.w1c1m1{2O2S2e2{2}4g4s4{5i5n6{7X8U9n9p9r9x9z9|:Q:Ub:a9j9o9q9s9y9{9}:R:VQ:e:oR:f:pt#za!y$R$S$W(V(X(Y(a(u(v,Z-p1Y1t:k:q:r!|&{!w#d#v#x&f'|(e)Y)[)_)|*P+u-U-W-z-|.j.m.u.w1c1m1{2O2S2e2{2}4g4s4{5i5n6{7X8U9n9p9r9x9z9|:Q:Uc1O9j9o9q9s9y9{9}:R:VlfOSj}!n$Z%b%e%f%h*m*r/a/dQ(d#xQ*y%oQ*z%qR1b-U$U#{a!w!y#d#v#x$R$S$W&f'|(V(X(Y(a(e(u(v)Y)[)_)|*P+u,Z-U-W-p-z-|.j.m.u.w1Y1c1m1t1{2O2S2e2{2}4g4s4{5i5n6{7X8U9j9n9o9p9q9r9s9x9y9z9{9|9}:Q:R:U:V:k:q:rQ*O$|Q.l*QQ2h.kR5Z2iT(n#y(oS(n#y(oT2p.s2qQ)`$dQ-a(lQ.R)aQ.z*ZQ3Q.{Q5p3RQ7O5_Q7]5qQ7}7PQ8W7^Q8h8OQ8w8iQ9P8xR9T9Ql(V#t&})R,{-T-i-j0j1x4a4u:_:i:j!`9x&y'h(](c+r,W,p-X-u-x.i.k0^0i1a1e2R2g2i2y4U4h4n4w4|5X5l6a6l6r7ZZ9y0}4[6c7p8bn(X#t&})R,y,{-T-i-j0j1x4a4u:_:i:j!b9z&y'h(](c+r,W,p-X-u-x.i.k0^0g0i1a1e2R2g2i2y4U4h4n4w4|5X5l6a6l6r7Z]9{0}4[6c6d7p8bpeOSjy}!n$Z%X%b%e%f%h*m*r/a/dQ%UxR*i%_peOSjy}!n$Z%X%b%e%f%h*m*r/a/dR%UxQ*S$}R.h){qeOSjy}!n$Z%X%b%e%f%h*m*r/a/dQ.t*XS2z.x.yW5h2w2x2y3OU7W5j5k5lU8S7V7Y7ZQ8l8TR8z8mQ%]yR*c%XR3X/RR7`5sS$lp$qR.^)lQ%bzR*m%cR*s%iT/b*r/dQjOQ!nST$^j!nQ(O#sR,q(OQ!YQR%t!YQ!^RU%z!^%{+QQ%{!_R+Q%|Q+]&WR/v+]Q,[&}R0k,[Q,_'PS0n,_0oR0o,`Q+i&cR0Q+iS!eR$tU&`!e&a+RQ&a!fR+R%}Q+`&ZR/x+`Q&t!sQ+}&rU,R&t+}0fR0f,SQ'q#iR,j'qQ#m`R'w#mQ#cZU'g#c*|9iQ*|9WR9i'tQ-O(^W1]-O1^4e6kU1^-P-Q-RS4e1_1`R6k4f#q(T#t&y&}'h(](c(|(})R+r,U,V,W,p,y,z,{-T-X-i-j-u-x.i.k0^0g0h0i0j0}1a1e1x2R2g2i2y4U4Y4Z4[4a4h4n4u4w4|5X5l6a6c6d6e6l6r7Z7p8b:_:i:jQ-V(cU1d-V1f4iQ1f-XR4i1eQ(o#yR-c(oQ(x#}R-l(xQ1|-uR4x1|Q)y$wR.g)yQ2k.nS5]2k6}R6}5^Q*U%OR.q*UQ2q.sR5b2qQ/Q*`S3V/Q5tR5t3XQ.X)hW2[.X2^5R6xQ2^.[Q5R2]R6x5SQ)m$lR._)mQ/d*rR3g/dWiOSj!nQ%g}Q)U$ZQ*l%bQ*n%eQ*o%fQ*q%hQ/_*mS/b*r/dR3f/aQ$]gQ%k!RQ%n!TQ%p!UQ%r!VQ)t$rQ)z$xQ*b%]Q*w%mS/T*c*fQ/k*vQ/l*yQ/m*zS/{+f2nQ1g-ZQ1h-[Q1n-aQ2a.bQ2f.iQ3P.zQ3Z/VQ3e/`Y3s/}0O0U0Z5dQ4j1iQ4l1kQ4o1oQ5V2cQ5Y2gQ5o3QQ5u3Y[6T3r3u3y3{3|7TQ6m4kQ6p4pQ6y5TQ7[5pQ7a5vW7g6U6Z6]6_Q7r6nQ7t6qQ7y6zQ7|7OQ8V7]U8Z7h7m7oQ8c7sQ8e7vQ8g7}Q8n8WS8p8]8aQ8u8dQ8v8hQ8{8rQ9O8wQ9R8}Q9S9PR9U9TQ$fmQ&h!jU)c$g$h$iQ+V&TU+w&i&j&kQ-Z(iS.T)d)eQ/s+XQ/z+fS0`+x+yQ1k-_Q2U.UQ3p/yS3v0P0UQ4R0aQ4p1pS6X3w3|Q7j6YQ8^7lR8s8`S#ua:kR)^$cU#}a$c:kR-k(wQ#taS&y!w)_Q&}!yQ'h#dQ(]#vQ(c#xQ(|$RQ(}$SQ)R$WQ+r&fQ,U9nQ,V9pQ,W9rQ,p'|Q,y(VQ,z(XQ,{(YQ-T(aQ-X(eQ-i(uQ-j(vd-u)Y-z.u2O2{4{5i6{7X8UQ-x)[Q.i)|Q.k*PQ0^+uQ0g9xQ0h9zQ0i9|Q0j,ZQ0}9jQ1a-UQ1e-WQ1x-pQ2R-|Q2g.jQ2i.mQ2y.wQ4U:QQ4Y9oQ4Z9qQ4[9sQ4a1YQ4h1cQ4n1mQ4u1tQ4w1{Q4|2SQ5X2eQ5l2}Q6a:UQ6c9}Q6d9yQ6e9{Q6l4gQ6r4sQ7Z5nQ7p:RQ8b:VQ:_:kQ:i:qR:j:rlgOSj}!n$Z%b%e%f%h*m*r/a/dS!pU%dQ%m!SQ%s!WQ'U!|Q'u#kS*f%Z%^Q*j%_Q*v%lQ+S&RQ+q&eQ,n'vQ-w)ZQ/[*iQ0]+tQ1T,iQ1v-nQ2Q-{Q2x.vQ3]/XQ3^/YQ3`/ZQ3b/]Q3i/fQ4_1QQ5W2dQ5k2|Q5z3aQ5|3cQ5}3dQ7Y5mR7c5{!vZOSUj}!S!n!|$Z%Z%^%_%b%d%e%f%h%l&R&e)Z*i*m*r+t-n-{.v/X/Y/Z/]/a/d/f2d2|3a3c3d5m5{Q!_RQ!oTQ$_kS%v!]%yQ%|!`Q&x!vQ'P!zQ'V#PQ'W#QQ'X#RQ'Y#SQ'Z#TQ'[#UQ']#VQ'^#WQ'_#XQ'`#YQ'a#ZQ'c#]Q'f#bQ'j#eW't#k'v,i1QQ)n$mS*}%w+PS+Y&V/tQ+c&^Q+z&oQ,Y&|Q,`'QQ,c9VQ,e9XQ,s(QQ-q)SQ/o+TQ/r+WQ0b+{Q0l,^Q0p9[Q0q9]Q0r9^Q0s9_Q0t9`Q0u9aQ0v9bQ0w9cQ0x9dQ0y9eQ0z9fQ0{9gQ0|,dQ1P9kQ1U9hQ1y-sQ2V.VQ3o9tQ3r/|Q4S0cQ4V0mQ4W9uQ4]9wQ4^:OQ5^2lQ6R3mQ6U3tQ6b:PQ6f:SQ6g:TQ7h6VQ7{6|Q8]7kQ8r8_Q8}8tQ9W!WR:d:nR!aRR&X!bS&T!b+[S+X&U&]R/y+bR'O!yR'R!zT!tU$XS!sU$XU$wrs*kS&r!r!uQ,P&sQ,S&vQ.f)xS0d,O,QR4T0e`!dR!]!`$t%w&_)v+dh!qUrs!r!u$X&s&v)x,O,Q0eQ/^*kQ/p+UQ3l/jT:[&V)wT!gR$tS!fR$tS%x!]&_S%}!`)vS+O%w+dT+Z&V)wT&[!b$uQ#i^R'z#nT'p#i'qR1S,hT(`#v(hR(f#xQ-v)YQ2P-zQ2w.uQ4y2OQ5j2{Q6u4{Q7V5iQ7z6{Q8T7XR8m8UlhOSj}!n$Z%b%e%f%h*m*r/a/dQ%[yR*b%XV$xrs*kR.o*RR*a%VQ$ppR)s$qR)i$kT%`z%cT%az%cT/c*r/d",
    nodeNames: "\u26A0 ArithOp ArithOp InterpolationStart extends LineComment BlockComment Script ExportDeclaration export Star as VariableName String from ; default FunctionDeclaration async function VariableDefinition TypeParamList TypeDefinition ThisType this LiteralType ArithOp Number BooleanLiteral TemplateType InterpolationEnd Interpolation VoidType void TypeofType typeof MemberExpression . ?. PropertyName [ TemplateString Interpolation null super RegExp ] ArrayExpression Spread , } { ObjectExpression Property async get set PropertyDefinition Block : NewExpression new TypeArgList CompareOp < ) ( ArgList UnaryExpression await yield delete LogicOp BitOp ParenthesizedExpression ClassExpression class extends ClassBody MethodDeclaration Privacy static abstract override PrivatePropertyDefinition PropertyDeclaration readonly Optional TypeAnnotation Equals StaticBlock FunctionExpression ArrowFunction ParamList ParamList ArrayPattern ObjectPattern PatternProperty Privacy readonly Arrow MemberExpression PrivatePropertyName BinaryExpression ArithOp ArithOp ArithOp ArithOp BitOp CompareOp instanceof in const CompareOp BitOp BitOp BitOp LogicOp LogicOp ConditionalExpression LogicOp LogicOp AssignmentExpression UpdateOp PostfixExpression CallExpression TaggedTemplateExpression DynamicImport import ImportMeta JSXElement JSXSelfCloseEndTag JSXStartTag JSXSelfClosingTag JSXIdentifier JSXNamespacedName JSXMemberExpression JSXSpreadAttribute JSXAttribute JSXAttributeValue JSXEscape JSXEndTag JSXOpenTag JSXFragmentTag JSXText JSXEscape JSXStartCloseTag JSXCloseTag PrefixCast ArrowFunction TypeParamList SequenceExpression KeyofType keyof UniqueType unique ImportType InferredType infer TypeName ParenthesizedType FunctionSignature ParamList NewSignature IndexedType TupleType Label ArrayType ReadonlyType ObjectType MethodType PropertyType IndexSignature CallSignature TypePredicate is NewSignature new UnionType LogicOp IntersectionType LogicOp ConditionalType ParameterizedType ClassDeclaration abstract implements type VariableDeclaration let var TypeAliasDeclaration InterfaceDeclaration interface EnumDeclaration enum EnumBody NamespaceDeclaration namespace module AmbientDeclaration declare GlobalDeclaration global ClassDeclaration ClassBody MethodDeclaration AmbientFunctionDeclaration ExportGroup VariableName VariableName ImportDeclaration ImportGroup ForStatement for ForSpec ForInSpec ForOfSpec of WhileStatement while WithStatement with DoStatement do IfStatement if else SwitchStatement switch SwitchBody CaseLabel case DefaultLabel TryStatement try catch finally ReturnStatement return ThrowStatement throw BreakStatement break ContinueStatement continue DebuggerStatement debugger LabeledStatement ExpressionStatement",
    maxTerm: 330,
    context: trackNewline,
    nodeProps: [
      [NodeProp.closedBy, 3, "InterpolationEnd", 40, "]", 51, "}", 66, ")", 132, "JSXSelfCloseEndTag JSXEndTag", 146, "JSXEndTag"],
      [NodeProp.group, -26, 8, 15, 17, 58, 184, 188, 191, 192, 194, 197, 200, 211, 213, 219, 221, 223, 225, 228, 234, 238, 240, 242, 244, 246, 248, 249, "Statement", -30, 12, 13, 24, 27, 28, 41, 43, 44, 45, 47, 52, 60, 68, 74, 75, 91, 92, 101, 103, 119, 122, 124, 125, 126, 127, 129, 130, 148, 149, 151, "Expression", -22, 23, 25, 29, 32, 34, 152, 154, 156, 157, 159, 160, 161, 163, 164, 165, 167, 168, 169, 178, 180, 182, 183, "Type", -3, 79, 85, 90, "ClassItem"],
      [NodeProp.openedBy, 30, "InterpolationStart", 46, "[", 50, "{", 65, "(", 131, "JSXStartTag", 141, "JSXStartTag JSXStartCloseTag"]
    ],
    skippedNodes: [0, 5, 6],
    repeatNodeCount: 28,
    tokenData: "!C}~R!`OX%TXY%cYZ'RZ[%c[]%T]^'R^p%Tpq%cqr'crs(kst0htu2`uv4pvw5ewx6cxy<yyz=Zz{=k{|>k|}?O}!O>k!O!P?`!P!QCl!Q!R!0[!R![!1q![!]!7s!]!^!8V!^!_!8g!_!`!9d!`!a!:[!a!b!<R!b!c%T!c!}2`!}#O!=d#O#P%T#P#Q!=t#Q#R!>U#R#S2`#S#T!>i#T#o2`#o#p!>y#p#q!?O#q#r!?f#r#s!?x#s$f%T$f$g%c$g#BY2`#BY#BZ!@Y#BZ$IS2`$IS$I_!@Y$I_$I|2`$I|$I}!Bq$I}$JO!Bq$JO$JT2`$JT$JU!@Y$JU$KV2`$KV$KW!@Y$KW&FU2`&FU&FV!@Y&FV?HT2`?HT?HU!@Y?HU~2`W%YR$UWO!^%T!_#o%T#p~%T,T%jg$UW'W+{OX%TXY%cYZ%TZ[%c[p%Tpq%cq!^%T!_#o%T#p$f%T$f$g%c$g#BY%T#BY#BZ%c#BZ$IS%T$IS$I_%c$I_$JT%T$JT$JU%c$JU$KV%T$KV$KW%c$KW&FU%T&FU&FV%c&FV?HT%T?HT?HU%c?HU~%T,T'YR$UW'X+{O!^%T!_#o%T#p~%T$T'jS$UW!j#{O!^%T!_!`'v!`#o%T#p~%T$O'}S#e#v$UWO!^%T!_!`(Z!`#o%T#p~%T$O(bR#e#v$UWO!^%T!_#o%T#p~%T'u(rZ$UW]!ROY(kYZ)eZr(krs*rs!^(k!^!_+U!_#O(k#O#P-b#P#o(k#o#p+U#p~(k&r)jV$UWOr)ers*Ps!^)e!^!_*a!_#o)e#o#p*a#p~)e&r*WR$P&j$UWO!^%T!_#o%T#p~%T&j*dROr*ars*ms~*a&j*rO$P&j'u*{R$P&j$UW]!RO!^%T!_#o%T#p~%T'm+ZV]!ROY+UYZ*aZr+Urs+ps#O+U#O#P+w#P~+U'm+wO$P&j]!R'm+zROr+Urs,Ts~+U'm,[U$P&j]!ROY,nZr,nrs-Vs#O,n#O#P-[#P~,n!R,sU]!ROY,nZr,nrs-Vs#O,n#O#P-[#P~,n!R-[O]!R!R-_PO~,n'u-gV$UWOr(krs-|s!^(k!^!_+U!_#o(k#o#p+U#p~(k'u.VZ$P&j$UW]!ROY.xYZ%TZr.xrs/rs!^.x!^!_,n!_#O.x#O#P0S#P#o.x#o#p,n#p~.x!Z/PZ$UW]!ROY.xYZ%TZr.xrs/rs!^.x!^!_,n!_#O.x#O#P0S#P#o.x#o#p,n#p~.x!Z/yR$UW]!RO!^%T!_#o%T#p~%T!Z0XT$UWO!^.x!^!_,n!_#o.x#o#p,n#p~.xy0mZ$UWOt%Ttu1`u!^%T!_!c%T!c!}1`!}#R%T#R#S1`#S#T%T#T#o1`#p$g%T$g~1`y1g]$UW'mqOt%Ttu1`u!Q%T!Q![1`![!^%T!_!c%T!c!}1`!}#R%T#R#S1`#S#T%T#T#o1`#p$g%T$g~1`&i2k_$UW#zS'Z%k'epOt%Ttu2`u}%T}!O3j!O!Q%T!Q![2`![!^%T!_!c%T!c!}2`!}#R%T#R#S2`#S#T%T#T#o2`#p$g%T$g~2`[3q_$UW#zSOt%Ttu3ju}%T}!O3j!O!Q%T!Q![3j![!^%T!_!c%T!c!}3j!}#R%T#R#S3j#S#T%T#T#o3j#p$g%T$g~3j$O4wS#^#v$UWO!^%T!_!`5T!`#o%T#p~%T$O5[R$UW#o#vO!^%T!_#o%T#p~%T%r5lU'v%j$UWOv%Tvw6Ow!^%T!_!`5T!`#o%T#p~%T$O6VS$UW#i#vO!^%T!_!`5T!`#o%T#p~%T'u6jZ$UW]!ROY6cYZ7]Zw6cwx*rx!^6c!^!_8T!_#O6c#O#P:T#P#o6c#o#p8T#p~6c&r7bV$UWOw7]wx*Px!^7]!^!_7w!_#o7]#o#p7w#p~7]&j7zROw7wwx*mx~7w'm8YV]!ROY8TYZ7wZw8Twx+px#O8T#O#P8o#P~8T'm8rROw8Twx8{x~8T'm9SU$P&j]!ROY9fZw9fwx-Vx#O9f#O#P9}#P~9f!R9kU]!ROY9fZw9fwx-Vx#O9f#O#P9}#P~9f!R:QPO~9f'u:YV$UWOw6cwx:ox!^6c!^!_8T!_#o6c#o#p8T#p~6c'u:xZ$P&j$UW]!ROY;kYZ%TZw;kwx/rx!^;k!^!_9f!_#O;k#O#P<e#P#o;k#o#p9f#p~;k!Z;rZ$UW]!ROY;kYZ%TZw;kwx/rx!^;k!^!_9f!_#O;k#O#P<e#P#o;k#o#p9f#p~;k!Z<jT$UWO!^;k!^!_9f!_#o;k#o#p9f#p~;k%V=QR!d$}$UWO!^%T!_#o%T#p~%TZ=bR!cR$UWO!^%T!_#o%T#p~%T%R=tU'[!R#_#v$UWOz%Tz{>W{!^%T!_!`5T!`#o%T#p~%T$O>_S#[#v$UWO!^%T!_!`5T!`#o%T#p~%T$u>rSj$m$UWO!^%T!_!`5T!`#o%T#p~%T&i?VR!R&a$UWO!^%T!_#o%T#p~%T&i?gVu%n$UWO!O%T!O!P?|!P!Q%T!Q![@r![!^%T!_#o%T#p~%Ty@RT$UWO!O%T!O!P@b!P!^%T!_#o%T#p~%Ty@iR!Qq$UWO!^%T!_#o%T#p~%Ty@yZ$UWkqO!Q%T!Q![@r![!^%T!_!g%T!g!hAl!h#R%T#R#S@r#S#X%T#X#YAl#Y#o%T#p~%TyAqZ$UWO{%T{|Bd|}%T}!OBd!O!Q%T!Q![CO![!^%T!_#R%T#R#SCO#S#o%T#p~%TyBiV$UWO!Q%T!Q![CO![!^%T!_#R%T#R#SCO#S#o%T#p~%TyCVV$UWkqO!Q%T!Q![CO![!^%T!_#R%T#R#SCO#S#o%T#p~%T,TCs`$UW#]#vOYDuYZ%TZzDuz{Jl{!PDu!P!Q!-e!Q!^Du!^!_Fx!_!`!.^!`!a!/]!a!}Du!}#OHq#O#PJQ#P#oDu#o#pFx#p~DuXD|[$UW}POYDuYZ%TZ!PDu!P!QEr!Q!^Du!^!_Fx!_!}Du!}#OHq#O#PJQ#P#oDu#o#pFx#p~DuXEy_$UW}PO!^%T!_#Z%T#Z#[Er#[#]%T#]#^Er#^#a%T#a#bEr#b#g%T#g#hEr#h#i%T#i#jEr#j#m%T#m#nEr#n#o%T#p~%TPF}V}POYFxZ!PFx!P!QGd!Q!}Fx!}#OG{#O#PHh#P~FxPGiU}P#Z#[Gd#]#^Gd#a#bGd#g#hGd#i#jGd#m#nGdPHOTOYG{Z#OG{#O#PH_#P#QFx#Q~G{PHbQOYG{Z~G{PHkQOYFxZ~FxXHvY$UWOYHqYZ%TZ!^Hq!^!_G{!_#OHq#O#PIf#P#QDu#Q#oHq#o#pG{#p~HqXIkV$UWOYHqYZ%TZ!^Hq!^!_G{!_#oHq#o#pG{#p~HqXJVV$UWOYDuYZ%TZ!^Du!^!_Fx!_#oDu#o#pFx#p~Du,TJs^$UW}POYJlYZKoZzJlz{NQ{!PJl!P!Q!,R!Q!^Jl!^!_!!]!_!}Jl!}#O!'|#O#P!+a#P#oJl#o#p!!]#p~Jl,TKtV$UWOzKoz{LZ{!^Ko!^!_M]!_#oKo#o#pM]#p~Ko,TL`X$UWOzKoz{LZ{!PKo!P!QL{!Q!^Ko!^!_M]!_#oKo#o#pM]#p~Ko,TMSR$UWU+{O!^%T!_#o%T#p~%T+{M`ROzM]z{Mi{~M]+{MlTOzM]z{Mi{!PM]!P!QM{!Q~M]+{NQOU+{,TNX^$UW}POYJlYZKoZzJlz{NQ{!PJl!P!Q! T!Q!^Jl!^!_!!]!_!}Jl!}#O!'|#O#P!+a#P#oJl#o#p!!]#p~Jl,T! ^_$UWU+{}PO!^%T!_#Z%T#Z#[Er#[#]%T#]#^Er#^#a%T#a#bEr#b#g%T#g#hEr#h#i%T#i#jEr#j#m%T#m#nEr#n#o%T#p~%T+{!!bY}POY!!]YZM]Zz!!]z{!#Q{!P!!]!P!Q!&x!Q!}!!]!}#O!$`#O#P!&f#P~!!]+{!#VY}POY!!]YZM]Zz!!]z{!#Q{!P!!]!P!Q!#u!Q!}!!]!}#O!$`#O#P!&f#P~!!]+{!#|UU+{}P#Z#[Gd#]#^Gd#a#bGd#g#hGd#i#jGd#m#nGd+{!$cWOY!$`YZM]Zz!$`z{!${{#O!$`#O#P!&S#P#Q!!]#Q~!$`+{!%OYOY!$`YZM]Zz!$`z{!${{!P!$`!P!Q!%n!Q#O!$`#O#P!&S#P#Q!!]#Q~!$`+{!%sTU+{OYG{Z#OG{#O#PH_#P#QFx#Q~G{+{!&VTOY!$`YZM]Zz!$`z{!${{~!$`+{!&iTOY!!]YZM]Zz!!]z{!#Q{~!!]+{!&}_}POzM]z{Mi{#ZM]#Z#[!&x#[#]M]#]#^!&x#^#aM]#a#b!&x#b#gM]#g#h!&x#h#iM]#i#j!&x#j#mM]#m#n!&x#n~M],T!(R[$UWOY!'|YZKoZz!'|z{!(w{!^!'|!^!_!$`!_#O!'|#O#P!*o#P#QJl#Q#o!'|#o#p!$`#p~!'|,T!(|^$UWOY!'|YZKoZz!'|z{!(w{!P!'|!P!Q!)x!Q!^!'|!^!_!$`!_#O!'|#O#P!*o#P#QJl#Q#o!'|#o#p!$`#p~!'|,T!*PY$UWU+{OYHqYZ%TZ!^Hq!^!_G{!_#OHq#O#PIf#P#QDu#Q#oHq#o#pG{#p~Hq,T!*tX$UWOY!'|YZKoZz!'|z{!(w{!^!'|!^!_!$`!_#o!'|#o#p!$`#p~!'|,T!+fX$UWOYJlYZKoZzJlz{NQ{!^Jl!^!_!!]!_#oJl#o#p!!]#p~Jl,T!,Yc$UW}POzKoz{LZ{!^Ko!^!_M]!_#ZKo#Z#[!,R#[#]Ko#]#^!,R#^#aKo#a#b!,R#b#gKo#g#h!,R#h#iKo#i#j!,R#j#mKo#m#n!,R#n#oKo#o#pM]#p~Ko,T!-lV$UWT+{OY!-eYZ%TZ!^!-e!^!_!.R!_#o!-e#o#p!.R#p~!-e+{!.WQT+{OY!.RZ~!.R$P!.g[$UW#o#v}POYDuYZ%TZ!PDu!P!QEr!Q!^Du!^!_Fx!_!}Du!}#OHq#O#PJQ#P#oDu#o#pFx#p~Du]!/f[#wS$UW}POYDuYZ%TZ!PDu!P!QEr!Q!^Du!^!_Fx!_!}Du!}#OHq#O#PJQ#P#oDu#o#pFx#p~Duy!0cd$UWkqO!O%T!O!P@r!P!Q%T!Q![!1q![!^%T!_!g%T!g!hAl!h#R%T#R#S!1q#S#U%T#U#V!3X#V#X%T#X#YAl#Y#b%T#b#c!2w#c#d!4m#d#l%T#l#m!5{#m#o%T#p~%Ty!1x_$UWkqO!O%T!O!P@r!P!Q%T!Q![!1q![!^%T!_!g%T!g!hAl!h#R%T#R#S!1q#S#X%T#X#YAl#Y#b%T#b#c!2w#c#o%T#p~%Ty!3OR$UWkqO!^%T!_#o%T#p~%Ty!3^W$UWO!Q%T!Q!R!3v!R!S!3v!S!^%T!_#R%T#R#S!3v#S#o%T#p~%Ty!3}Y$UWkqO!Q%T!Q!R!3v!R!S!3v!S!^%T!_#R%T#R#S!3v#S#b%T#b#c!2w#c#o%T#p~%Ty!4rV$UWO!Q%T!Q!Y!5X!Y!^%T!_#R%T#R#S!5X#S#o%T#p~%Ty!5`X$UWkqO!Q%T!Q!Y!5X!Y!^%T!_#R%T#R#S!5X#S#b%T#b#c!2w#c#o%T#p~%Ty!6QZ$UWO!Q%T!Q![!6s![!^%T!_!c%T!c!i!6s!i#R%T#R#S!6s#S#T%T#T#Z!6s#Z#o%T#p~%Ty!6z]$UWkqO!Q%T!Q![!6s![!^%T!_!c%T!c!i!6s!i#R%T#R#S!6s#S#T%T#T#Z!6s#Z#b%T#b#c!2w#c#o%T#p~%T%w!7|R!]V$UW#m%hO!^%T!_#o%T#p~%T!P!8^R_w$UWO!^%T!_#o%T#p~%T+c!8rR'`d!a%Y#x&s'zP!P!Q!8{!^!_!9Q!_!`!9_W!9QO$WW#v!9VP#`#v!_!`!9Y#v!9_O#o#v#v!9dO#a#v%w!9kT!{%o$UWO!^%T!_!`'v!`!a!9z!a#o%T#p~%T$P!:RR#W#w$UWO!^%T!_#o%T#p~%T%w!:gT'_!s#a#v$RS$UWO!^%T!_!`!:v!`!a!;W!a#o%T#p~%T$O!:}R#a#v$UWO!^%T!_#o%T#p~%T$O!;_T#`#v$UWO!^%T!_!`5T!`!a!;n!a#o%T#p~%T$O!;uS#`#v$UWO!^%T!_!`5T!`#o%T#p~%T%w!<YV'n%o$UWO!O%T!O!P!<o!P!^%T!_!a%T!a!b!=P!b#o%T#p~%T$`!<vRv$W$UWO!^%T!_#o%T#p~%T$O!=WS$UW#j#vO!^%T!_!`5T!`#o%T#p~%T&e!=kRx&]$UWO!^%T!_#o%T#p~%TZ!={R!OR$UWO!^%T!_#o%T#p~%T$O!>]S#g#v$UWO!^%T!_!`5T!`#o%T#p~%T$P!>pR$UW'd#wO!^%T!_#o%T#p~%T~!?OO!T~%r!?VT'u%j$UWO!^%T!_!`5T!`#o%T#p#q!=P#q~%T$u!?oR!S$knQ$UWO!^%T!_#o%T#p~%TX!@PR!kP$UWO!^%T!_#o%T#p~%T,T!@gr$UW'W+{#zS'Z%k'epOX%TXY%cYZ%TZ[%c[p%Tpq%cqt%Ttu2`u}%T}!O3j!O!Q%T!Q![2`![!^%T!_!c%T!c!}2`!}#R%T#R#S2`#S#T%T#T#o2`#p$f%T$f$g%c$g#BY2`#BY#BZ!@Y#BZ$IS2`$IS$I_!@Y$I_$JT2`$JT$JU!@Y$JU$KV2`$KV$KW!@Y$KW&FU2`&FU&FV!@Y&FV?HT2`?HT?HU!@Y?HU~2`,T!CO_$UW'X+{#zS'Z%k'epOt%Ttu2`u}%T}!O3j!O!Q%T!Q![2`![!^%T!_!c%T!c!}2`!}#R%T#R#S2`#S#T%T#T#o2`#p$g%T$g~2`",
    tokenizers: [noSemicolon, incdecToken, template, 0, 1, 2, 3, 4, 5, 6, 7, 8, insertSemicolon],
    topRules: { "Script": [0, 7] },
    dialects: { jsx: 11335, ts: 11337 },
    dynamicPrecedences: { "149": 1, "176": 1 },
    specialized: [{ term: 287, get: (value, stack) => tsExtends(value, stack) << 1 }, { term: 287, get: (value) => spec_identifier[value] || -1 }, { term: 297, get: (value) => spec_word[value] || -1 }, { term: 63, get: (value) => spec_LessThan[value] || -1 }],
    tokenPrec: 11358
  });

  // node_modules/@codemirror/lang-javascript/dist/index.js
  var snippets = [
    /* @__PURE__ */ snippetCompletion("function ${name}(${params}) {\n	${}\n}", {
      label: "function",
      detail: "definition",
      type: "keyword"
    }),
    /* @__PURE__ */ snippetCompletion("for (let ${index} = 0; ${index} < ${bound}; ${index}++) {\n	${}\n}", {
      label: "for",
      detail: "loop",
      type: "keyword"
    }),
    /* @__PURE__ */ snippetCompletion("for (let ${name} of ${collection}) {\n	${}\n}", {
      label: "for",
      detail: "of loop",
      type: "keyword"
    }),
    /* @__PURE__ */ snippetCompletion("try {\n	${}\n} catch (${error}) {\n	${}\n}", {
      label: "try",
      detail: "block",
      type: "keyword"
    }),
    /* @__PURE__ */ snippetCompletion("class ${name} {\n	constructor(${params}) {\n		${}\n	}\n}", {
      label: "class",
      detail: "definition",
      type: "keyword"
    }),
    /* @__PURE__ */ snippetCompletion('import {${names}} from "${module}"\n${}', {
      label: "import",
      detail: "named",
      type: "keyword"
    }),
    /* @__PURE__ */ snippetCompletion('import ${name} from "${module}"\n${}', {
      label: "import",
      detail: "default",
      type: "keyword"
    })
  ];
  var javascriptLanguage = /* @__PURE__ */ LRLanguage.define({
    parser: /* @__PURE__ */ parser.configure({
      props: [
        /* @__PURE__ */ indentNodeProp.add({
          IfStatement: /* @__PURE__ */ continuedIndent({ except: /^\s*({|else\b)/ }),
          TryStatement: /* @__PURE__ */ continuedIndent({ except: /^\s*({|catch\b|finally\b)/ }),
          LabeledStatement: flatIndent,
          SwitchBody: (context) => {
            let after = context.textAfter, closed = /^\s*\}/.test(after), isCase = /^\s*(case|default)\b/.test(after);
            return context.baseIndent + (closed ? 0 : isCase ? 1 : 2) * context.unit;
          },
          Block: /* @__PURE__ */ delimitedIndent({ closing: "}" }),
          ArrowFunction: (cx) => cx.baseIndent + cx.unit,
          "TemplateString BlockComment": () => -1,
          "Statement Property": /* @__PURE__ */ continuedIndent({ except: /^{/ }),
          JSXElement(context) {
            let closed = /^\s*<\//.test(context.textAfter);
            return context.lineIndent(context.node.from) + (closed ? 0 : context.unit);
          },
          JSXEscape(context) {
            let closed = /\s*\}/.test(context.textAfter);
            return context.lineIndent(context.node.from) + (closed ? 0 : context.unit);
          },
          "JSXOpenTag JSXSelfClosingTag"(context) {
            return context.column(context.node.from) + context.unit;
          }
        }),
        /* @__PURE__ */ foldNodeProp.add({
          "Block ClassBody SwitchBody EnumBody ObjectExpression ArrayExpression": foldInside,
          BlockComment(tree) {
            return { from: tree.from + 2, to: tree.to - 2 };
          }
        }),
        /* @__PURE__ */ styleTags({
          "get set async static": tags.modifier,
          "for while do if else switch try catch finally return throw break continue default case": tags.controlKeyword,
          "in of await yield void typeof delete instanceof": tags.operatorKeyword,
          "let var const function class extends": tags.definitionKeyword,
          "import export from": tags.moduleKeyword,
          "with debugger as new": tags.keyword,
          TemplateString: /* @__PURE__ */ tags.special(tags.string),
          Super: tags.atom,
          BooleanLiteral: tags.bool,
          this: tags.self,
          null: tags.null,
          Star: tags.modifier,
          VariableName: tags.variableName,
          "CallExpression/VariableName TaggedTemplateExpression/VariableName": /* @__PURE__ */ tags.function(tags.variableName),
          VariableDefinition: /* @__PURE__ */ tags.definition(tags.variableName),
          Label: tags.labelName,
          PropertyName: tags.propertyName,
          PrivatePropertyName: /* @__PURE__ */ tags.special(tags.propertyName),
          "CallExpression/MemberExpression/PropertyName": /* @__PURE__ */ tags.function(tags.propertyName),
          "FunctionDeclaration/VariableDefinition": /* @__PURE__ */ tags.function(/* @__PURE__ */ tags.definition(tags.variableName)),
          "ClassDeclaration/VariableDefinition": /* @__PURE__ */ tags.definition(tags.className),
          PropertyDefinition: /* @__PURE__ */ tags.definition(tags.propertyName),
          PrivatePropertyDefinition: /* @__PURE__ */ tags.definition(/* @__PURE__ */ tags.special(tags.propertyName)),
          UpdateOp: tags.updateOperator,
          LineComment: tags.lineComment,
          BlockComment: tags.blockComment,
          Number: tags.number,
          String: tags.string,
          ArithOp: tags.arithmeticOperator,
          LogicOp: tags.logicOperator,
          BitOp: tags.bitwiseOperator,
          CompareOp: tags.compareOperator,
          RegExp: tags.regexp,
          Equals: tags.definitionOperator,
          "Arrow : Spread": tags.punctuation,
          "( )": tags.paren,
          "[ ]": tags.squareBracket,
          "{ }": tags.brace,
          "InterpolationStart InterpolationEnd": /* @__PURE__ */ tags.special(tags.brace),
          ".": tags.derefOperator,
          ", ;": tags.separator,
          TypeName: tags.typeName,
          TypeDefinition: /* @__PURE__ */ tags.definition(tags.typeName),
          "type enum interface implements namespace module declare": tags.definitionKeyword,
          "abstract global Privacy readonly override": tags.modifier,
          "is keyof unique infer": tags.operatorKeyword,
          JSXAttributeValue: tags.attributeValue,
          JSXText: tags.content,
          "JSXStartTag JSXStartCloseTag JSXSelfCloseEndTag JSXEndTag": tags.angleBracket,
          "JSXIdentifier JSXNameSpacedName": tags.tagName,
          "JSXAttribute/JSXIdentifier JSXAttribute/JSXNameSpacedName": tags.attributeName
        })
      ]
    }),
    languageData: {
      closeBrackets: { brackets: ["(", "[", "{", "'", '"', "`"] },
      commentTokens: { line: "//", block: { open: "/*", close: "*/" } },
      indentOnInput: /^\s*(?:case |default:|\{|\}|<\/)$/,
      wordChars: "$"
    }
  });
  var typescriptLanguage = /* @__PURE__ */ javascriptLanguage.configure({ dialect: "ts" });
  var jsxLanguage = /* @__PURE__ */ javascriptLanguage.configure({ dialect: "jsx" });
  var tsxLanguage = /* @__PURE__ */ javascriptLanguage.configure({ dialect: "jsx ts" });
  function javascript(config2 = {}) {
    let lang = config2.jsx ? config2.typescript ? tsxLanguage : jsxLanguage : config2.typescript ? typescriptLanguage : javascriptLanguage;
    return new LanguageSupport(lang, javascriptLanguage.data.of({
      autocomplete: ifNotIn(["LineComment", "BlockComment", "String"], completeFromList(snippets))
    }));
  }

  // node_modules/@lezer/json/dist/index.es.js
  var parser2 = LRParser.deserialize({
    version: 13,
    states: "$bOVQPOOOOQO'#Cb'#CbOnQPO'#CeOvQPO'#CjOOQO'#Cp'#CpQOQPOOOOQO'#Cg'#CgO}QPO'#CfO!SQPO'#CrOOQO,59P,59PO![QPO,59PO!aQPO'#CuOOQO,59U,59UO!iQPO,59UOVQPO,59QOqQPO'#CkO!nQPO,59^OOQO1G.k1G.kOVQPO'#ClO!vQPO,59aOOQO1G.p1G.pOOQO1G.l1G.lOOQO,59V,59VOOQO-E6i-E6iOOQO,59W,59WOOQO-E6j-E6j",
    stateData: "#O~OcOS~OQSORSOSSOTSOWQO]ROePO~OVXOeUO~O[[O~PVOg^O~Oh_OVfX~OVaO~OhbO[iX~O[dO~Oh_OVfa~OhbO[ia~O",
    goto: "!kjPPPPPPkPPkqwPPk{!RPPP!XP!ePP!hXSOR^bQWQRf_TVQ_Q`WRg`QcZRicQTOQZRQe^RhbRYQR]R",
    nodeNames: "\u26A0 JsonText True False Null Number String } { Object Property PropertyName ] [ Array",
    maxTerm: 25,
    nodeProps: [
      [NodeProp.openedBy, 7, "{", 12, "["],
      [NodeProp.closedBy, 8, "}", 13, "]"]
    ],
    skippedNodes: [0],
    repeatNodeCount: 2,
    tokenData: "(p~RaXY!WYZ!W]^!Wpq!Wrs!]|}$i}!O$n!Q!R$w!R![&V![!]&h!}#O&m#P#Q&r#Y#Z&w#b#c'f#h#i'}#o#p(f#q#r(k~!]Oc~~!`Upq!]qr!]rs!rs#O!]#O#P!w#P~!]~!wOe~~!zXrs!]!P!Q!]#O#P!]#U#V!]#Y#Z!]#b#c!]#f#g!]#h#i!]#i#j#g~#jR!Q![#s!c!i#s#T#Z#s~#vR!Q![$P!c!i$P#T#Z$P~$SR!Q![$]!c!i$]#T#Z$]~$`R!Q![!]!c!i!]#T#Z!]~$nOh~~$qQ!Q!R$w!R![&V~$|RT~!O!P%V!g!h%k#X#Y%k~%YP!Q![%]~%bRT~!Q![%]!g!h%k#X#Y%k~%nR{|%w}!O%w!Q![%}~%zP!Q![%}~&SPT~!Q![%}~&[ST~!O!P%V!Q![&V!g!h%k#X#Y%k~&mOg~~&rO]~~&wO[~~&zP#T#U&}~'QP#`#a'T~'WP#g#h'Z~'^P#X#Y'a~'fOR~~'iP#i#j'l~'oP#`#a'r~'uP#`#a'x~'}OS~~(QP#f#g(T~(WP#i#j(Z~(^P#X#Y(a~(fOQ~~(kOW~~(pOV~",
    tokenizers: [0],
    topRules: { "JsonText": [0, 1] },
    tokenPrec: 0
  });

  // node_modules/@codemirror/lang-json/dist/index.js
  var jsonLanguage = /* @__PURE__ */ LRLanguage.define({
    parser: /* @__PURE__ */ parser2.configure({
      props: [
        /* @__PURE__ */ indentNodeProp.add({
          Object: /* @__PURE__ */ continuedIndent({ except: /^\s*\}/ }),
          Array: /* @__PURE__ */ continuedIndent({ except: /^\s*\]/ })
        }),
        /* @__PURE__ */ foldNodeProp.add({
          "Object Array": foldInside
        }),
        /* @__PURE__ */ styleTags({
          String: tags.string,
          Number: tags.number,
          "True False": tags.bool,
          PropertyName: tags.propertyName,
          Null: tags.null,
          ",": tags.separator,
          "[ ]": tags.squareBracket,
          "{ }": tags.brace
        })
      ]
    }),
    languageData: {
      closeBrackets: { brackets: ["[", "{", '"'] },
      indentOnInput: /^\s*[\}\]]$/
    }
  });
  function json() {
    return new LanguageSupport(jsonLanguage);
  }

  // node_modules/@lezer/php/dist/index.es.js
  var castOpen = 1;
  var HeredocString = 2;
  var interpolatedStringContent = 262;
  var EscapeSequence = 3;
  var afterInterpolation = 263;
  var automaticSemicolon = 264;
  var eof = 265;
  var abstract = 4;
  var and = 5;
  var array = 6;
  var as = 7;
  var Boolean = 8;
  var _break = 9;
  var _case = 10;
  var _catch = 11;
  var _class = 12;
  var clone = 13;
  var _const = 14;
  var _continue = 15;
  var _default = 16;
  var declare = 17;
  var _do = 18;
  var echo = 19;
  var _else = 20;
  var elseif = 21;
  var enddeclare = 22;
  var endfor = 23;
  var endforeach = 24;
  var endif = 25;
  var endswitch = 26;
  var endwhile = 27;
  var _enum = 28;
  var _extends = 29;
  var final = 30;
  var _finally = 31;
  var fn = 32;
  var _for = 33;
  var foreach = 34;
  var from = 35;
  var _function = 36;
  var global = 37;
  var goto = 38;
  var _if = 39;
  var _implements = 40;
  var include = 41;
  var include_once = 42;
  var _instanceof = 43;
  var insteadof = 44;
  var _interface = 45;
  var list = 46;
  var match = 47;
  var namespace = 48;
  var _new = 49;
  var _null = 50;
  var or = 51;
  var print = 52;
  var require2 = 53;
  var require_once = 54;
  var _return = 55;
  var _switch = 56;
  var _throw = 57;
  var trait = 58;
  var _try = 59;
  var unset = 60;
  var use = 61;
  var _var = 62;
  var Visibility = 63;
  var _while = 64;
  var xor = 65;
  var _yield = 66;
  var keywordMap = {
    abstract,
    and,
    array,
    as,
    true: Boolean,
    false: Boolean,
    break: _break,
    case: _case,
    catch: _catch,
    class: _class,
    clone,
    const: _const,
    continue: _continue,
    declare,
    default: _default,
    do: _do,
    echo,
    else: _else,
    elseif,
    enddeclare,
    endfor,
    endforeach,
    endif,
    endswitch,
    endwhile,
    enum: _enum,
    extends: _extends,
    final,
    finally: _finally,
    fn,
    for: _for,
    foreach,
    from,
    function: _function,
    global,
    goto,
    if: _if,
    implements: _implements,
    include,
    include_once,
    instanceof: _instanceof,
    insteadof,
    interface: _interface,
    list,
    match,
    namespace,
    new: _new,
    null: _null,
    or,
    print,
    require: require2,
    require_once,
    return: _return,
    switch: _switch,
    throw: _throw,
    trait,
    try: _try,
    unset,
    use,
    var: _var,
    public: Visibility,
    private: Visibility,
    protected: Visibility,
    while: _while,
    xor,
    yield: _yield,
    __proto__: null
  };
  function keywords(name2) {
    let found = keywordMap[name2.toLowerCase()];
    return found == null ? -1 : found;
  }
  function isSpace(ch) {
    return ch == 9 || ch == 10 || ch == 13 || ch == 32;
  }
  function isASCIILetter(ch) {
    return ch >= 97 && ch <= 122 || ch >= 65 && ch <= 90;
  }
  function isIdentifierStart(ch) {
    return ch == 95 || ch >= 128 || isASCIILetter(ch);
  }
  function isHex(ch) {
    return ch >= 48 && ch <= 55 || ch >= 97 && ch <= 102 || ch >= 65 && ch <= 70;
  }
  var castTypes = {
    int: true,
    integer: true,
    bool: true,
    boolean: true,
    float: true,
    double: true,
    real: true,
    string: true,
    array: true,
    object: true,
    unset: true,
    __proto__: null
  };
  var expression = new ExternalTokenizer((input) => {
    if (input.next == 40) {
      input.advance();
      let peek = 0;
      while (isSpace(input.peek(peek)))
        peek++;
      let name2 = "", next;
      while (isASCIILetter(next = input.peek(peek))) {
        name2 += String.fromCharCode(next);
        peek++;
      }
      while (isSpace(input.peek(peek)))
        peek++;
      if (input.peek(peek) == 41 && castTypes[name2.toLowerCase()])
        input.acceptToken(castOpen);
    } else if (input.next == 60 && input.peek(1) == 60 && input.peek(2) == 60) {
      for (let i = 0; i < 3; i++)
        input.advance();
      while (input.next == 32 || input.next == 9)
        input.advance();
      let quoted = input.next == 39;
      if (quoted)
        input.advance();
      if (!isIdentifierStart(input.next))
        return;
      let tag = String.fromCharCode(input.next);
      for (; ; ) {
        input.advance();
        if (!isIdentifierStart(input.next) && !(input.next >= 48 && input.next <= 55))
          break;
        tag += String.fromCharCode(input.next);
      }
      if (quoted) {
        if (input.next != 39)
          return;
        input.advance();
      }
      if (input.next != 10 && input.next != 13)
        return;
      for (; ; ) {
        let lineStart = input.next == 10 || input.next == 13;
        input.advance();
        if (input.next < 0)
          return;
        if (lineStart) {
          while (input.next == 32 || input.next == 9)
            input.advance();
          let match2 = true;
          for (let i = 0; i < tag.length; i++) {
            if (input.next != tag.charCodeAt(i)) {
              match2 = false;
              break;
            }
            input.advance();
          }
          if (match2)
            return input.acceptToken(HeredocString);
        }
      }
    }
  });
  var eofToken = new ExternalTokenizer((input) => {
    if (input.next < 0)
      input.acceptToken(eof);
  });
  var semicolon2 = new ExternalTokenizer((input, stack) => {
    if (input.next == 63 && stack.canShift(automaticSemicolon) && input.peek(1) == 62)
      input.acceptToken(automaticSemicolon);
  });
  function scanEscape(input) {
    let after = input.peek(1);
    if (after == 110 || after == 114 || after == 116 || after == 118 || after == 101 || after == 102 || after == 92 || after == 36 || after == 34 || after == 123)
      return 2;
    if (after >= 48 && after <= 55) {
      let size = 2, next;
      while (size < 5 && (next = input.peek(size)) >= 48 && next <= 55)
        size++;
      return size;
    }
    if (after == 120 && isHex(input.peek(2))) {
      return isHex(input.peek(3)) ? 4 : 3;
    }
    if (after == 117 && input.peek(2) == 123) {
      for (let size = 3; ; size++) {
        let next = input.peek(size);
        if (next == 125)
          return size == 2 ? 0 : size + 1;
        if (!isHex(next))
          break;
      }
    }
    return 0;
  }
  var interpolated = new ExternalTokenizer((input, stack) => {
    let content2 = false;
    for (; ; content2 = true) {
      if (input.next == 34 || input.next < 0 || input.next == 36 && (isIdentifierStart(input.peek(1)) || input.peek(1) == 123) || input.next == 123 && input.peek(1) == 36) {
        break;
      } else if (input.next == 92) {
        let escaped = scanEscape(input);
        if (escaped) {
          if (content2)
            break;
          else
            return input.acceptToken(EscapeSequence, escaped);
        }
      } else if (!content2 && (input.next == 91 || input.next == 45 && input.peek(1) == 62 && isIdentifierStart(input.peek(2)) || input.next == 63 && input.peek(1) == 45 && input.peek(2) == 62 && isIdentifierStart(input.peek(3))) && stack.canShift(afterInterpolation)) {
        break;
      }
      input.advance();
    }
    if (content2)
      input.acceptToken(interpolatedStringContent);
  });
  var spec_Name = { __proto__: null, static: 311, STATIC: 311 };
  var parser3 = LRParser.deserialize({
    version: 13,
    states: "$F|Q`OWOOQhQaOOP%oO`OOOOO#t'#H^'#H^O%tO#|O'#DuOOO#u'#Dx'#DxQ&SOWO'#DxO&XO$VOOOOQ#u'#Dy'#DyO&lQaO'#D}O(mQdO'#FOO(tQdO'#ERO*kQaO'#EXO,zQ`O'#EUO-PQ`O'#E_O/nQaO'#E_O/uQ`O'#EgO/zQ`O'#EpO*kQaO'#EpO0VQ`O'#HgO0[Q`O'#E|O0[Q`O'#E|OOQS'#Ib'#IbO0aQ`O'#EwOOQS'#IY'#IYO2oQdO'#IVO6tQeO'#FUO*kQaO'#FeO*kQaO'#FfO*kQaO'#FgO*kQaO'#FhO*kQaO'#FhO*kQaO'#FkOOQO'#Ic'#IcO7RQ`O'#FqOOQO'#Hh'#HhO7ZQ`O'#G}O7uQ`O'#FlO8QQ`O'#H[O8]Q`O'#FvO8eQaO'#FwO*kQaO'#GUO*kQaO'#GXO8}OrO'#G[OOQS'#Ip'#IpOOQS'#Io'#IoOOQS'#IV'#IVO,zQ`O'#GcO,zQ`O'#GeO,zQ`O'#GjOhQaO'#GlO9UQ`O'#GmO9ZQ`O'#GpO9`Q`O'#GsO9eQeO'#GtO9eQeO'#GuO9eQeO'#GvO9oQ`O'#GwO9tQ`O'#GyO9yQaO'#GzO<YQ`O'#G{O<_Q`O'#G|O<dQ`O'#G|O9oQ`O'#G}O<iQ`O'#HOO<nQ`O'#HOO<sQ`O'#HPO<xQ`O'#HQO<}Q`O'#HRO=SQ`O'#HUO=_Q`O'#HVO9yQaO'#HZOOQ#u'#IU'#IUOOQ#u'#H`'#H`QhQaOOO=pO#|O'#DsPOOO)CCv)CCvOOO#t-E;[-E;[OOO#u,5:d,5:dOOO#u'#H_'#H_O&XO$VOOO={Q$VO'#ITOOOO'#IT'#ITQOOOOOOOQ#y,5:i,5:iO>SQaO,5:iOOQ#u,5:k,5:kO@jQaO,5:nO@qQaO,5;VO*kQaO,5;VO@xQ`O,5;WOCgQaO'#EtOOQS,5;_,5;_OCnQ`O,5;kOOQP'#F]'#F]O*kQaO,5;qO*kQaO,5;qO*kQaO,5;qO*kQaO,5;qO*kQaO,5;qO*kQaO,5;qO*kQaO,5;qO*kQaO,5;qO*kQaO,5;qO*kQaO,5;qO*kQaO,5;qO*kQaO,5;qO*kQaO,5;qO*kQaO,5;qO*kQaO,5;qO*kQaO,5;qOOQ#u'#Il'#IlOOQS,5<p,5<pOOQ#u,5:m,5:mOEgQ`O,5:sOEnQdO'#FOOFbQ`O'#FlOFjQ`O'#FlOFrQ`O,5:pOFwQaO'#E`OOQS,5:y,5:yOIOQ`O'#I[O9yQaO'#EbO9yQaO'#I[OOQS'#I['#I[OIVQ`O'#IZOI_Q`O,5:yO-UQaO,5:yOIdQaO'#EhOOQS,5;R,5;ROOQS,5;[,5;[OInQ`O,5;[OOQO,5>R,5>ROJaQdO,5;hOOQO-E;e-E;eOLcQ`O,5;hOLhQpO,5;cO0aQ`O'#EzOLpQtO'#FOOOQS'#E{'#E{OOQS'#Ia'#IaOMeQaO,5:xO*kQaO,5;nOOQS,5;p,5;pO*kQaO,5;pOMlQdO,5<POM|QdO,5<QON^QdO,5<RONnQdO,5<SO!!xQdO,5<SO!#PQdO,5<VO!#aQ`O'#FrO!#lQ`O'#IfO!#tQ`O,5<]OOQO-E;f-E;fO!#yQ`O'#InO<_Q`O,5=hO!$RQ`O,5=hO9oQ`O,5=iO!$WQ`O,5=jO!$]Q`O,5=jO!$bQ`O,5=mO!$gQ`O'#FnO!$}Q`O,5<WO!%YQ`O,5<WO!%]Q`O,5?YO!%bQ`O,5<WO!%jQ`O,5<bO!%rQdO'#GOO!&QQdO'#ImO!&]QdO,5=vO!&eQ`O,5<bO!%]Q`O,5<bO!&mQdO,5<cO!&}Q`O,5<cO!'qQdO,5<pO!)sQdO,5<sO!*TOrO'#HrOOOQ'#Is'#IsO*kQaO'#GaOOOQ'#Hr'#HrO!*uOrO,5<vOOQS,5<v,5<vO!*|QaO,5<}O!+TQ`O,5=PO!+]QeO,5=UO!+gQ`O,5=WO!+lQaO'#GnO!+]QeO,5=XO9yQaO'#GqO!+]QeO,5=[O!&]QdO,5=_O(tQdO,5=`OOQ#u,5=`,5=`O(tQdO,5=aOOQ#u,5=a,5=aO(tQdO,5=bOOQ#u,5=b,5=bO!+sQ`O,5=cO!+{Q`O,5=eO!,QQdO'#IuOOQS'#Iu'#IuO!&]QdO,5=fO>ZQaO,5=gO!-jQ`O'#F|O!-oQdO'#IkO!&]QdO,5=hOOQ#u,5=i,5=iO!-zQ`O,5=jO!.VQ`O,5=kO!.QQ`O,5=lO!._Q`O,5=mO!.jQdO,5=pOOQ#u,5=p,5=pO!.uQ`O,5=qO!.uQ`O,5=qO!.}QdO'#IvO!/]Q`O'#HWO!&]QdO,5=qO!/kQ`O,5=qO!/vQdO'#IXO!&]QdO,5=uOOQ#u-E;^-E;^OOO#u,5:_,5:_O!1cO#|O,5:_OOO#u-E;]-E;]OOOO,5>o,5>oOOQ#y1G0T1G0TO!1kQ`O1G0YO*kQaO1G0YO!2}Q`O1G0qOOQS1G0q1G0qO!4aQ`O1G0qOOQS'#I^'#I^O*kQaO'#I^OOQS1G0r1G0rO!4hQ`O'#I`O!7qQ`O'#FOO!8OQaO'#EvOOQO'#I`'#I`O!8YQ`O'#I_O!8bQ`O,5;`OOQS1G1V1G1VO!8gQdO1G1]O!:iQdO1G1]O!<UQdO1G1]O!=qQdO1G1]O!?^QdO1G1]O!@yQdO1G1]O!BfQdO1G1]O!DRQdO1G1]O!EnQdO1G1]O!GZQdO1G1]O!HvQdO1G1]O!JcQdO1G1]O!LOQdO1G1]O!MkQdO1G1]O# WQdO1G1]O#!sQdO1G1]OOQT1G0_1G0_O!%]Q`O,5<WO#$`QaO'#EYOOQS1G0[1G0[O#$gQ`O,5:zOFzQaO,5:zO#$lQaO,5;OO#$sQdO,5:|O#&oQdO,5>vO#(kQaO'#HcO#({Q`O,5>uOOQS1G0e1G0eO#)TQ`O1G0eO#)YQ`O'#I]O#*rQ`O'#I]O#*zQ`O,5;SOIgQaO,5;SOOQS1G0v1G0vPOQO'#FO'#FOO#+kQdO1G1SO0aQ`O'#HfO#-mQtO,5;dO#._QaO1G0}OOQS,5;f,5;fO#0nQtO,5;hO#0{QdO1G0dO*kQaO1G0dO#2hQdO1G1YO#4TQdO1G1[OOQO,5<^,5<^O#4eQ`O'#HiO#4sQ`O,5?QOOQO1G1w1G1wO#4{Q`O,5?YO!&]QdO1G3SO<_Q`O1G3SOOQ#u1G3T1G3TO!-zQ`O1G3UO#5QQ`O1G3UO#5VQ`O1G3XO#5bQpO'#FoO#5pQ`O'#FoO#6QQ`O'#FoO#6]Q`O'#FoO#6eQ`O'#FsO#6jQ`O'#FtOOQO'#Ie'#IeO#6qQ`O'#IdO#6yQ`O,5<YOOQS1G1r1G1rO0aQ`O1G1rO#7OQ`O1G1rO#7TQ`O1G1rO!%]Q`O1G4tO#7`QdO1G4tO!%]Q`O1G1rO#7nQ`O1G1|O!%]Q`O1G1|O9yQaO,5<jO#7vQdO'#HpO#8UQdO,5?XOOQ#u1G3b1G3bO*kQaO1G1|O0aQ`O1G1|O#8aQdO1G1}O7RQ`O'#FxO7RQ`O'#FyO#:sQ`O'#FzOOQS1G1}1G1}O!.QQ`O1G1}O!-}Q`O1G1}O!-zQ`O1G1}O#;jO`O,5<wO#;oO`O,5<wO#;zO!bO,5<xO#<YQ`O,5<{OOOQ-E;p-E;pOOQS1G2b1G2bO#<aQaO'#GdO#<zQ$VO1G2iO#AzQ`O1G2iO#BVQ`O'#GfO#BbQ`O'#GiOOQ#u1G2k1G2kO#BmQ`O1G2kOOQ#u'#Gk'#GkOOQ#u'#It'#ItOOQ#u1G2p1G2pO#BrQ`O1G2pO,zQ`O1G2rO#BwQaO,5=YO#COQ`O,5=YOOQ#u1G2s1G2sO#CTQ`O1G2sO#CYQ`O,5=]OOQ#u1G2v1G2vO#DlQ`O1G2vOOQ#u1G2y1G2yOOQ#u1G2z1G2zOOQ#u1G2{1G2{OOQ#u1G2|1G2|O#DqQ`O'#HwO9oQ`O'#HwO#DvQ$VO1G2}O#I|Q`O1G3PO9yQaO'#HvO#JRQdO,5=ZOOQ#u1G3Q1G3QO#J^Q`O1G3RO9yQaO,5<hO#JcQdO'#HoO#JqQdO,5?VOOQ#u1G3S1G3SOOQ#u1G3U1G3UO!.QQ`O1G3UO!-}Q`O1G3UOOQ#u1G3V1G3VO!.QQ`O1G3VOOQ#u1G3W1G3WO#KkQ`O'#HSOOQ#u1G3X1G3XO#KrQ`O1G3XO0aQ`O1G3XOOQ#u1G3[1G3[O!&]QdO1G3]O#KwQ`O1G3]O#LPQdO'#HyO#LbQdO,5?bO#LmQ`O,5?bO#LrQ`O'#HXO7RQ`O'#HXO#L}Q`O'#IwO#MVQ`O,5=rOOQ#u1G3]1G3]O!.uQ`O1G3]O!.uQ`O1G3]O#M[QeO'#HaO#MlQdO,5>sOOQ#u1G3a1G3aOOO#u1G/y1G/yO*kQaO7+%tO#MzQdO7+%tOOQS7+&]7+&]O$ gQ`O,5>xO>ZQaO,5;aO$ nQ`O,5;bO$#TQaO'#HeO$#_Q`O,5>yOOQS1G0z1G0zO$#gQ`O'#EZO$#lQ`O'#IWO$#tQ`O,5:tOOQS1G0f1G0fO$#yQ`O1G0fO$$OQ`O1G0jO9yQaO1G0jOOQO,5=},5=}OOQO-E;a-E;aOOQS7+&P7+&PO>ZQaO,5;TO$%eQaO'#HdO$%oQ`O,5>wOOQS1G0n1G0nO$%wQ`O1G0nOOQS,5>Q,5>QOOQS-E;d-E;dO$%|QdO7+&iO$(OQtO1G1SO$(]QdO7+&OOOQS1G0j1G0jOOQO,5>T,5>TOOQO-E;g-E;gOOQ#u7+(n7+(nO!&]QdO7+(nOOQ#u7+(p7+(pO!.QQ`O7+(pO!-}Q`O7+(pO!-zQ`O7+(pOOQ#u7+(s7+(sO#KrQ`O7+(sO0aQ`O7+(sO$)xQ`O,5<ZO$*TQ`O,5<ZO$*]Q`O,5<_O$*bQpO,5<ZO>ZQaO,5<ZOOQO,5<_,5<_O$*pQpO,5<`O$*xQ`O,5<`O$+TQ`O'#HjO$+nQ`O,5?OOOQS1G1t1G1tO$+vQpO7+'^O$,OQ`O'#FuO$,ZQ`O7+'^OOQS7+'^7+'^O0aQ`O7+'^O#7OQ`O7+'^O$,cQdO7+*`O0aQ`O7+*`O$,qQ`O7+'^O*kQaO7+'hO0aQ`O7+'hO$,|Q`O7+'hO$-UQdO1G2UOOQS,5>[,5>[OOQS-E;n-E;nO$.nQdO7+'hO$/OQpO7+'hO$/WQdO'#IhOOQO,5<d,5<dOOQO,5<e,5<eO$/iQpO'#F}O$/qQ`O'#F}OOQO'#Ij'#IjOOQO'#Hn'#HnO$0bQ`O'#F}O<_Q`O'#F{O!&]QdO'#F}O!.jQdO'#GPO7RQ`O'#GQOOQO'#Ii'#IiOOQO'#Hm'#HmO$1OQ`O,5<fOOQ#y,5<f,5<fOOQS7+'i7+'iO!.QQ`O7+'iO!-}Q`O7+'iOOOQ1G2c1G2cO$1uO`O1G2cO$1zO!bO1G2dO$2YO`O'#G_O$2_O`O1G2dOOOQ1G2g1G2gO$2dQaO,5=OO,zQ`O'#HsO$2}Q$VO7+(TOhQaO7+(TO,zQ`O'#HtO$7}Q`O7+(TO!&]QdO7+(TO$8YQ`O7+(TO$8_QaO'#GgO$:nQ`O'#GhOOQO'#Hu'#HuO$:vQ`O,5=QOOQ#u,5=Q,5=QO$;RQ`O,5=TO!&]QdO7+(VO!&]QdO7+([O!&]QdO7+(^O$;^QaO1G2tO$;eQ`O1G2tO$;jQaO1G2tO!&]QdO7+(_O9yQaO1G2wO!&]QdO7+(bO0aQ`O'#GxO9oQ`O,5>cOOQ#u,5>c,5>cOOQ#u-E;u-E;uO$;qQaO7+(kO$<YQdO,5>bOOQS-E;t-E;tO!&]QdO7+(mO$=rQdO1G2SOOQS,5>Z,5>ZOOQS-E;m-E;mOOQ#u7+(q7+(qO$?sQ`O'#GPO$?zQ`O'#GPO$@`Q`O'#HTOOQO'#Hx'#HxO$@eQ`O,5=nOOQ#u,5=n,5=nO$@lQpO7+(sOOQ#u7+(w7+(wO!&]QdO7+(wO$@wQdO,5>eOOQS-E;w-E;wO$AVQdO1G4|O$AbQ`O,5=sO$AgQ`O,5=sO$ArQ`O'#HzO$BWQ`O,5?cOOQS1G3^1G3^O#KwQ`O7+(wO$B`QdO,5={OOQS-E;_-E;_O$C{QdO<<I`OOQS1G4d1G4dO$EhQ`O1G0{OOQO,5>P,5>POOQO-E;c-E;cO$8_QaO,5:uO$F}QaO'#HbO$G[Q`O,5>rOOQS1G0`1G0`OOQS7+&Q7+&QO$GdQ`O7+&UO$HyQ`O1G0oO$J`Q`O,5>OOOQO,5>O,5>OOOQO-E;b-E;bOOQS7+&Y7+&YOOQS7+&U7+&UOOQ#u<<LY<<LYOOQ#u<<L[<<L[O!.QQ`O<<L[O!-}Q`O<<L[OOQ#u<<L_<<L_O$@lQpO<<L_O>ZQaO1G1uO$KxQ`O1G1uO$LTQ`O1G1yOOQO1G1y1G1yO$LYQ`O1G1uO$LbQ`O1G1uO$MwQ`O1G1zO>ZQaO1G1zOOQO,5>U,5>UOOQO-E;h-E;hOOQS<<Jx<<JxO$NSQ`O'#IgO$N[Q`O'#IgO$NaQ`O,5<aO0aQ`O<<JxO$+vQpO<<JxO$NfQ`O<<JxO0aQ`O<<MzO$NnQtO<<MzO#7OQ`O<<JxO$N|QdO<<KSO% ^QpO<<KSO*kQaO<<KSO0aQ`O<<KSO% fQdO'#HlO% }QdO,5?SO!&]QdO,5<iO$/iQpO,5<iO%!`Q`O,5<iO<_Q`O,5<gO!.jQdO,5<kOOQO-E;l-E;lO!&]QdO,5<gOOQO,5<i,5<iOOQO,5<k,5<kO%!yQdO,5<lOOQO-E;k-E;kOOQ#y1G2Q1G2QOOQS<<KT<<KTO!.QQ`O<<KTOOOQ7+'}7+'}O%#UO`O7+(OOOOO,5<y,5<yOOOQ7+(O7+(OOhQaO,5>_OOQ#u-E;q-E;qOhQaO<<KoOOQ#u<<Ko<<KoO$8YQ`O,5>`OOQO-E;r-E;rO!&]QdO<<KoO$8YQ`O<<KoO%#ZQ`O<<KoO%#`Q`O,5=RO%$uQaO,5=SOOQO-E;s-E;sOOQ#u1G2l1G2lOOQ#u<<Kq<<KqOOQ#u<<Kv<<KvOOQ#u<<Kx<<KxOOQT7+(`7+(`O%%VQ`O7+(`O%%[QaO7+(`O%%cQ`O7+(`OOQ#u<<Ky<<KyO%%hQ`O7+(cO%&}Q`O7+(cOOQ#u<<K|<<K|O%'SQpO,5=dOOQ#u1G3}1G3}O%'_Q`O<<LVOOQ#u<<LX<<LXO$?zQ`O,5<kO%'dQ`O,5=oO%'iQdO,5=oOOQO-E;v-E;vOOQ#u1G3Y1G3YO#KrQ`O<<L_OOQ#u<<Lc<<LcO%'tQ`O1G4PO%'yQdO7+*hOOQO1G3_1G3_O%(UQ`O1G3_O%(ZQ`O'#HYO7RQ`O'#HYOOQO,5>f,5>fOOQO-E;x-E;xO!&]QdO<<LcO%(fQ`O1G0aOOQO,5=|,5=|OOQO-E;`-E;`O>ZQaO,5;UOOQ#uANAvANAvO!.QQ`OANAvOOQ#uANAyANAyO#KrQ`OANAyO%){Q`O7+'aO>ZQaO7+'aOOQO7+'e7+'eO%+bQ`O7+'aO%+mQ`O7+'eO>ZQaO7+'fO%+rQ`O7+'fO%-XQ`O'#HkO%-gQ`O,5?RO%-gQ`O,5?ROOQO1G1{1G1{O$+vQpOAN@dOOQSAN@dAN@dO0aQ`OAN@dO%-oQtOANCfO%-}Q`OAN@dO*kQaOAN@nO%.VQdOAN@nO%.gQpOAN@nOOQS,5>W,5>WOOQS-E;j-E;jOOQO1G2T1G2TO!&]QdO1G2TO$/iQpO1G2TO<_Q`O1G2RO!.jQdO1G2VO!&]QdO1G2ROOQO1G2V1G2VOOQO1G2R1G2RO%.oQaO'#GROOQO1G2W1G2WOOQSAN@oAN@oOOOQ<<Kj<<KjOOQ#u1G3y1G3yOOQ#uANAZANAZOOQO1G3z1G3zO%0nQ`OANAZO!&]QdOANAZO%0sQaO1G2mO%1TQaO1G2nOOQT<<Kz<<KzO%1eQ`O<<KzO%1jQaO<<KzO*kQaO,5=^OOQT<<K}<<K}OOQO1G3O1G3OO%1qQ`O1G3OO!+]QeOANAqO%1vQdO1G3ZOOQO1G3Z1G3ZO%2RQ`O1G3ZOOQS7+)k7+)kOOQO7+(y7+(yO%2ZQ`O,5=tO%2`Q`O,5=tOOQ#uANA}ANA}O%2kQ`O1G0pOOQ#uG27bG27bOOQ#uG27eG27eO%4QQ`O<<J{O>ZQaO<<J{OOQO<<KP<<KPO%5gQ`O<<KQOOQO,5>V,5>VO%6|Q`O,5>VOOQO-E;i-E;iO%7RQ`O1G4mOOQSG26OG26OO$+vQpOG26OO0aQ`OG26OO%7ZQdOG26YO*kQaOG26YOOQO7+'o7+'oO!&]QdO7+'oO!&]QdO7+'mOOQO7+'q7+'qOOQO7+'m7+'mO%7kQ`OLD+tO%8zQ`O'#FOO%9UQ`O'#IYO!&]QdO'#HqO%;RQaO,5<mOOQO,5<m,5<mO!&]QdOG26uOOQ#uG26uG26uO%=QQaO7+(XOOQTANAfANAfO%=bQ`OANAfO%=gQ`O1G2xOOQO7+(j7+(jOOQ#uG27]G27]O%=nQ`OG27]OOQO7+(u7+(uO%=sQ`O7+(uO!&]QdO7+(uOOQO1G3`1G3`O%={Q`O1G3`O%>QQ`OAN@gOOQO1G3q1G3qOOQSLD+jLD+jO$+vQpOLD+jO%?gQdOLD+tOOQO<<KZ<<KZOOQO<<KX<<KXO%?wQ`O,5<nO%?|Q`O,5<oOOQP,5>],5>]OOQP-E;o-E;oOOQO1G2X1G2XOOQ#uLD,aLD,aOOQTG27QG27QO!&]QdOLD,wO!&]QdO<<LaOOQO<<La<<LaOOQO7+(z7+(zOOQS!$( U!$( UOOQS1G2Y1G2YOOQS1G2Z1G2ZO%@UQdO1G2ZOOQ#u!$(!c!$(!cOOQOANA{ANA{OOQS7+'u7+'uO%@aQ`O'#E|O%@aQ`O'#E|O%@fQ`O,5;hO%@kQdO,5<cO%BgQaO,5;OO*kQaO1G0jO%BnQaO'#FwO#._QaO'#GUO#._QaO'#GXO#._QaO,5;qO#._QaO,5;qO#._QaO,5;qO#._QaO,5;qO#._QaO,5;qO#._QaO,5;qO#._QaO,5;qO#._QaO,5;qO#._QaO,5;qO#._QaO,5;qO#._QaO,5;qO#._QaO,5;qO#._QaO,5;qO#._QaO,5;qO#._QaO,5;qO#._QaO,5;qO%BuQdO'#I[O%DeQdO'#I[O#._QaO'#EbO#._QaO'#I[O%FgQaO,5:xO#._QaO,5;nO#._QaO,5;pO%FnQdO,5<PO%HjQdO,5<QO%JfQdO,5<RO%LbQdO,5<SO%N^QdO,5<SO%NtQdO,5<VO&!pQdO,5<sO#._QaO1G0YO&$lQdO1G1]O&&hQdO1G1]O&(dQdO1G1]O&*`QdO1G1]O&,[QdO1G1]O&.WQdO1G1]O&0SQdO1G1]O&2OQdO1G1]O&3zQdO1G1]O&5vQdO1G1]O&7rQdO1G1]O&9nQdO1G1]O&;jQdO1G1]O&=fQdO1G1]O&?bQdO1G1]O&A^QdO,5:|O&CYQdO,5>vO&EUQdO1G0dO#._QaO1G0dO&GQQdO1G1YO&H|QdO1G1[O#._QaO1G1|O#._QaO7+%tO&JxQdO7+%tO&LtQdO7+&OO#._QaO7+'hO&NpQdO7+'hO'!lQdO<<I`O'$hQdO<<KSO#._QaO<<KSO#._QaOAN@nO'&dQdOAN@nO'(`QdOG26YO#._QaOG26YO'*[QdOLD+tO',WQaO,5;OO'.VQaO1G0jO'0RQdO'#IVO'0fQeO'#FUO'4fQeO'#FUO#._QaO'#FeO'.VQaO'#FeO#._QaO'#FfO'.VQaO'#FfO#._QaO'#FgO'.VQaO'#FgO#._QaO'#FhO'.VQaO'#FhO#._QaO'#FhO'.VQaO'#FhO#._QaO'#FkO'.VQaO'#FkO'8lQaO,5:nO'8sQ`O,5<bO'8{Q`O1G0YO'.VQaO1G0}O':_Q`O1G1|O':gQ`O7+'hO':oQpO7+'hO':wQpO<<KSO';PQpOAN@nO';XQaO'#FwO'.VQaO'#GUO'.VQaO'#GXO'.VQaO,5;qO'.VQaO,5;qO'.VQaO,5;qO'.VQaO,5;qO'.VQaO,5;qO'.VQaO,5;qO'.VQaO,5;qO'.VQaO,5;qO'.VQaO,5;qO'.VQaO,5;qO'.VQaO,5;qO'.VQaO,5;qO'.VQaO,5;qO'.VQaO,5;qO'.VQaO,5;qO'.VQaO,5;qO'.VQaO'#EbO'.VQaO'#I[O'=WQaO,5:xO'.VQaO,5;nO'.VQaO,5;pO'?VQdO,5<PO'AXQdO,5<QO'CZQdO,5<RO'E]QdO,5<SO'G_QdO,5<SO'G{QdO,5<VO'I}QdO,5<sO'.VQaO1G0YO'LPQdO1G1]O'NRQdO1G1]O(!TQdO1G1]O($VQdO1G1]O(&XQdO1G1]O((ZQdO1G1]O(*]QdO1G1]O(,_QdO1G1]O(.aQdO1G1]O(0cQdO1G1]O(2eQdO1G1]O(4gQdO1G1]O(6iQdO1G1]O(8kQdO1G1]O(:mQdO1G1]O(<oQdO,5:|O(>qQdO,5>vO(@sQdO1G0dO'.VQaO1G0dO(BuQdO1G1YO(DwQdO1G1[O'.VQaO1G1|O'.VQaO7+%tO(FyQdO7+%tO(H{QdO7+&OO'.VQaO7+'hO(J}QdO7+'hO(MPQdO<<I`O) RQdO<<KSO'.VQaO<<KSO'.VQaOAN@nO)#TQdOAN@nO)%VQdOG26YO'.VQaOG26YO)'XQdOLD+tO))ZQaO,5;OO#._QaO1G0jO))bQ`O'#FvO))jQpO,5;cO))rQ`O,5<bO!%]Q`O,5<bO!%]Q`O1G1|O0aQ`O1G1|O0aQ`O7+'hO0aQ`O<<KSO))zQdO,5<cO)+|QdO'#I[O)-{QdO'#IVO).fQaO,5:nO).mQ`O,5<bO).uQ`O1G0YO)0XQ`O1G1|O)0aQ`O7+'hO)0iQpO7+'hO)0qQpO<<KSO)0yQpOAN@nO0aQ`O'#EwO9yQaO'#FeO9yQaO'#FfO9yQaO'#FgO9yQaO'#FhO9yQaO'#FhO9yQaO'#FkO)1RQaO'#FwO9yQaO'#GUO9yQaO'#GXO9yQaO,5;qO9yQaO,5;qO9yQaO,5;qO9yQaO,5;qO9yQaO,5;qO9yQaO,5;qO9yQaO,5;qO9yQaO,5;qO9yQaO,5;qO9yQaO,5;qO9yQaO,5;qO9yQaO,5;qO9yQaO,5;qO9yQaO,5;qO9yQaO,5;qO9yQaO,5;qO)1YQ`O'#FlO*kQaO'#EbO*kQaO'#I[O)1bQaO,5:xO9yQaO,5;nO9yQaO,5;pO)1iQdO,5<PO)3eQdO,5<QO)5aQdO,5<RO)7]QdO,5<SO)9XQdO,5<SO)9oQdO,5<VO);kQdO,5<cO)=gQdO,5<sO)?cQ`O'#IuO)@xQ`O'#IXO9yQaO1G0YO)B_QdO1G1]O)DZQdO1G1]O)FVQdO1G1]O)HRQdO1G1]O)I}QdO1G1]O)KyQdO1G1]O)MuQdO1G1]O* qQdO1G1]O*#mQdO1G1]O*%iQdO1G1]O*'eQdO1G1]O*)aQdO1G1]O*+]QdO1G1]O*-XQdO1G1]O*/TQdO1G1]O*1PQaO,5;OO*1WQdO,5:|O*1hQdO,5>vO*1xQaO'#HcO*2YQ`O,5>uO*2bQdO1G0dO9yQaO1G0dO*4^QdO1G1YO*6YQdO1G1[O9yQaO1G1|O>ZQaO'#HvO*8UQ`O,5=ZO*8^QaO'#HaO*8hQ`O,5>sO9yQaO7+%tO*8pQdO7+%tO*:lQ`O1G0jO>ZQaO1G0jO*<RQdO7+&OO9yQaO7+'hO*=}QdO7+'hO*?yQ`O,5>bO*A`Q`O,5={O*BuQdO<<I`O*DqQ`O7+&UO*FWQdO<<KSO9yQaO<<KSO9yQaOAN@nO*HSQdOAN@nO*JOQdOG26YO9yQaOG26YO*KzQdOLD+tO*MvQaO,5;OO9yQaO1G0jO*M}QdO'#I[O*NhQ`O'#FvO*NpQ`O,5<bO!%]Q`O,5<bO!%]Q`O1G1|O0aQ`O1G1|O0aQ`O7+'hO0aQ`O<<KSO*NxQdO'#IVO+ cQeO'#FUO+!PQaO'#FUO+#xQaO'#FUO+%eQaO'#FUO>ZQaO'#FeO>ZQaO'#FfO>ZQaO'#FgO>ZQaO'#FhO>ZQaO'#FhO>ZQaO'#FkO+'^QaO'#FwO>ZQaO'#GUO>ZQaO'#GXO+'eQaO,5:nO>ZQaO,5;qO>ZQaO,5;qO>ZQaO,5;qO>ZQaO,5;qO>ZQaO,5;qO>ZQaO,5;qO>ZQaO,5;qO>ZQaO,5;qO>ZQaO,5;qO>ZQaO,5;qO>ZQaO,5;qO>ZQaO,5;qO>ZQaO,5;qO>ZQaO,5;qO>ZQaO,5;qO>ZQaO,5;qO+'lQ`O'#I[O$8_QaO'#EbO+)UQaOG26YO$8_QaO'#I[O++QQ`O'#IZO++YQaO,5:xO>ZQaO,5;nO>ZQaO,5;pO++aQ`O,5<PO+,|Q`O,5<QO+.iQ`O,5<RO+0UQ`O,5<SO+1qQ`O,5<SO+3^Q`O,5<VO+4yQ`O,5<bO+5RQ`O,5<cO+6nQ`O,5<sO+8ZQ`O1G0YO>ZQaO1G0YO+9mQ`O1G1]O+;YQ`O1G1]O+<uQ`O1G1]O+>bQ`O1G1]O+?}Q`O1G1]O+AjQ`O1G1]O+CVQ`O1G1]O+DrQ`O1G1]O+F_Q`O1G1]O+GzQ`O1G1]O+IgQ`O1G1]O+KSQ`O1G1]O+LoQ`O1G1]O+N[Q`O1G1]O, wQ`O1G1]O,#dQ`O1G0dO>ZQaO1G0dO,%PQ`O1G1YO,&lQ`O1G1[O,(XQ`O1G1|O>ZQaO1G1|O>ZQaO7+%tO,(aQ`O7+%tO,)|Q`O7+&OO>ZQaO7+'hO,+iQ`O7+'hO,+qQ`O7+'hO,-^QpO7+'hO,-fQ`O<<I`O,/RQ`O<<KSO,0nQpO<<KSO>ZQaO<<KSO>ZQaOAN@nO,0vQ`OAN@nO,2cQpOAN@nO,2kQ`OG26YO>ZQaOG26YO,4WQ`OLD+tO,5sQaO,5;OO>ZQaO1G0jO,5zQ`O'#I[O$8_QaO'#FeO$8_QaO'#FfO$8_QaO'#FgO$8_QaO'#FhO$8_QaO'#FhO+)UQaO'#FhO$8_QaO'#FkO,6XQaO'#FwO,6`QaO'#FwO$8_QaO'#GUO+)UQaO'#GUO$8_QaO'#GXO$8_QaO,5;qO+)UQaO,5;qO$8_QaO,5;qO+)UQaO,5;qO$8_QaO,5;qO+)UQaO,5;qO$8_QaO,5;qO+)UQaO,5;qO$8_QaO,5;qO+)UQaO,5;qO$8_QaO,5;qO+)UQaO,5;qO$8_QaO,5;qO+)UQaO,5;qO$8_QaO,5;qO+)UQaO,5;qO$8_QaO,5;qO+)UQaO,5;qO$8_QaO,5;qO+)UQaO,5;qO$8_QaO,5;qO+)UQaO,5;qO$8_QaO,5;qO+)UQaO,5;qO$8_QaO,5;qO+)UQaO,5;qO$8_QaO,5;qO+)UQaO,5;qO$8_QaO,5;qO+)UQaO,5;qO$8_QaO,5;qO+)UQaO,5;qO,8_Q`O'#FlO>ZQaO'#EbO>ZQaO'#I[O,8gQaO,5:xO,8nQaO,5:xO$8_QaO,5;nO+)UQaO,5;nO$8_QaO,5;pO,:mQ`O,5<PO,<YQ`O,5<QO,=uQ`O,5<RO,?bQ`O,5<SO,@}Q`O,5<SO,BjQ`O,5<SO,CyQ`O,5<VO,EfQ`O,5<cO%7kQ`O,5<cO,GRQ`O,5<sO$8_QaO1G0YO+)UQaO1G0YO,HnQ`O1G1]O,JZQ`O1G1]O,KjQ`O1G1]O,MVQ`O1G1]O,NfQ`O1G1]O-!RQ`O1G1]O-#bQ`O1G1]O-$}Q`O1G1]O-&^Q`O1G1]O-'yQ`O1G1]O-)YQ`O1G1]O-*uQ`O1G1]O-,UQ`O1G1]O--qQ`O1G1]O-/QQ`O1G1]O-0mQ`O1G1]O-1|Q`O1G1]O-3iQ`O1G1]O-4xQ`O1G1]O-6eQ`O1G1]O-7tQ`O1G1]O-9aQ`O1G1]O-:pQ`O1G1]O-<]Q`O1G1]O-=lQ`O1G1]O-?XQ`O1G1]O-@hQ`O1G1]O-BTQ`O1G1]O-CdQ`O1G1]O-EPQ`O1G1]O-F`Q`O,5:|O-G{Q`O,5>vO-IhQ`O1G0dO-KTQ`O1G0dO$8_QaO1G0dO+)UQaO1G0dO-LdQ`O1G1YO-NPQ`O1G1YO. `Q`O1G1[O$8_QaO1G1|O$8_QaO7+%tO+)UQaO7+%tO.!{Q`O7+%tO.$hQ`O7+%tO.%wQ`O7+&OO.'dQ`O7+&OO$8_QaO7+'hO.(sQ`O7+'hO.*`Q`O<<I`O.+{Q`O<<I`O.-[Q`O<<KSO$8_QaO<<KSO$8_QaOAN@nO..wQ`OAN@nO.0dQ`OG26YO$8_QaOG26YO.2PQ`OLD+tO.3lQaO,5;OO.3sQaO,5;OO$8_QaO1G0jO+)UQaO1G0jO.5rQ`O'#I[O.7UQ`O'#I[O.:kQ`O'#IVO.:{Q`O'#FvO.;TQaO,5:nO.;[Q`O,5<bO.;dQ`O,5<bO!%]Q`O,5<bO.;lQ`O1G0YO.=OQ`O,5:|O.>kQ`O,5>vO.@WQ`O1G1|O!%]Q`O1G1|O0aQ`O1G1|O0aQ`O7+'hO.@`Q`O7+'hO.@hQpO7+'hO.@pQpO<<KSO0aQ`O<<KSO.@xQpOAN@nO.AQQ`O'#IVO.AbQ`O'#IVO.CXQaO,5:nO.C`QaO,5:nO.CgQ`O,5<bO.CoQ`O7+'hO.CwQ`O1G0YO.EZQ`O1G0YO.FmQ`O1G1|O.FuQ`O7+'hO.F}QpO7+'hO.GVQpOAN@nO.G_QpO<<KSO.GgQpOAN@nO.GoQ`O'#FvO.GwQ`O'#FlO.HPQ`O,5<bO!%]Q`O,5<bO!%]Q`O1G1|O0aQ`O1G1|O0aQ`O7+'hO0aQ`O<<KSO.HXQ`O'#FvO.HaQ`O,5<bO.HiQ`O,5<bO!%]Q`O,5<bO!%]Q`O1G1|O!%]Q`O1G1|O0aQ`O1G1|O0aQ`O<<KSO0aQ`O7+'hO0aQ`O<<KSO.HqQ`O'#FlO.HyQ`O'#FlO.IRQ`O'#Fl",
    stateData: ".Ih~O!eOS!fOS&uOS!hQQ~O!jTO&vRO~OPgOQ|OS!cOU^OW}OX!XO[!bO]mO^!_O_!WOa![Ob!SOc!]Ol!fOn!cOpwOq!TOr!UOtuOu!iOv!VOw!POykOzkO}!dO!O`O!P]O!Q!gO!RxO!S}O!UpO!VlO!WlO!X!YO!Y!QO!ZzO![!eO!]!ZO!^!^O!_!hO!a!`O!b!RO!djO!nWO!pXO!tYO!z[O#X_O#chO#eaO#fbO#qeO$ToO$]nO$^oO$aqO$drO$yyO$z!OO$|}O$}}O%U|O'f{O~O!h!mO~O&vRO!j!iX&o!iX&s!iX~O!j!pO~O!e!qO!f!qO!h!mO&s!tO&u!qO~PhO!o!vO~PhOT'UX{'UX!T'UX!c'UX!n'UX!p'UX!w'UX!z'UX#T'UX#X'UX#a'UX#b'UX#q#rX#t'UX#z'UX#{'UX#|'UX#}'UX$O'UX$Q'UX$R'UX$S'UX$T'UX$U'UX$V'UX$W'UX$y'UX&r'UX~O!r!xO~P&sOT#TO{#RO!T#UO!c#VO!n#cO!p!{O!w!yO!z!}O#T#QO#X!zO#a!|O#b!|O#t#PO#z#SO#{#WO#|#XO#}#YO$O#ZO$Q#]O$R#^O$S#_O$T#`O$U#aO$V#bO$W#bO$y#dO&r#cO~OPgOQ|OU^OW}O]mOpwOt#hOykOzkO!O`O!P]O!RxO!S}O!UpO!VlO!WlO!ZzO!djO!t#gO!z[O#X_O#chO#eaO#fbO#qeO$ToO$]nO$^oO$aqO$yyO$z!OO$|}O$}}O%U|O'f{O~O!z[O~O!z#kO~OP6[OQ|OU^OW}O]6_Op=XOt#hOy6]Oz6]O!O`O!P]O!R6cO!S}O!U6bO!V6^O!W6^O!Z6eO!d8eO!t#gO!z[O#T#oO#V#nO#X_O#chO#eaO#fbO#qeO$T6aO$]6`O$^6aO$aqO$y6dO$z!OO$|}O$}}O%U|O'f{O#Y&}P~O#O#sO~P-UO!z#tO~O#c#vO#eaO#fbO~O#q#xO~O!t#yO~OU$PO!S$PO!t$OO!w#}O#q2WO~OT&yX{&yX!T&yX!c&yX!n&yX!p&yX!w&yX!z&yX#T&yX#X&yX#a&yX#b&yX#t&yX#z&yX#{&yX#|&yX#}&yX$O&yX$Q&yX$R&yX$S&yX$T&yX$U&yX$V&yX$W&yX$y&yX&r&yX!y&yX!o&yX~O#u$RO#w$SO~P0rOP6[OQ|OU^OW}O]6_Op=XOt#hOy6]Oz6]O!O`O!P]O!R6cO!S}O!U6bO!V6^O!W6^O!Z6eO!d8eO!t#gO!z[O#X_O#chO#eaO#fbO#qeO$T6aO$]6`O$^6aO$aqO$y6dO$z!OO$|}O$}}O%U|O'f{OT#xX{#xX!T#xX!c#xX!n#xX!p#xX!w#xX#a#xX#b#xX#t#xX#z#xX#{#xX#|#xX#}#xX$O#xX$Q#xX$R#xX$S#xX$U#xX$V#xX$W#xX&r#xX!y#xX!o#xX~Os$UO#T6xO#V6wO~P2yO!t#gO#qeO~OS$fO[$eO^$bOl$gOn$fOt$aO!a$cO$drO~O!t$kO!z$hO#T$jO~Op$mOt$lO#c$nO~O!z$hO#T$rO~O[$tO~P*kOR$zO!p$yO#c$xO#f$yO&p$zO~O'e$|O~P8lO!z%RO~O!z%TO~O!t%VO~O!n#cO&r#cO~P*kO!pXO~O!z%_O~OP6[OQ|OU^OW}O]6_Op=XOt#hOy6]Oz6]O!O`O!P]O!R6cO!S}O!U6bO!V6^O!W6^O!Z6eO!d8eO!t#gO!z[O#X_O#chO#eaO#fbO#qeO$T6aO$]6`O$^6aO$aqO$y6dO$z!OO$|}O$}}O%U|O'f{O~O!z%cO~O!t%dO~O^$bO~O!t%hO~O[$eO~O!t%iO~O!t%jO~O!t%kO~O!pXO!t#gO#qeO~O^%sOt%sO!p%qO!t#gO#q%oO~O!j%wO&s%wO&vRO~O&s%zO~PhO!o%{O~PhOPgOQ|OU^OW}O]8kOp=xOt#hOy8iOz8iO!O`O!P]O!R8oO!S}O!U8nO!V8jO!W8jO!Z8qO!d8hO!t#gO!z[O#X_O#chO#eaO#fbO#qeO$T8mO$]8lO$^8mO$aqO$y8pO$z!OO$|}O$}}O%U|O'f{O~O!r%}O~P>ZO#Y&PO~P>ZO!p&SO!t&RO#c&RO~OPgOQ|OU^OW}O]8kOp=xOt#hOy8iOz8iO!O`O!P]O!R8oO!S}O!U8nO!V8jO!W8jO!Z8qO!d8hO!t&VO!z[O#V&WO#X_O#chO#eaO#fbO#qeO$T8mO$]8lO$^8mO$aqO$y8pO$z!OO$|}O$}}O%U|O'f{O~O!y'RP~PATO!t&[O#c&[O~OT#TO{#RO!T#UO!c#VO!p!{O!w!yO!z!}O#T#QO#X!zO#a!|O#b!|O#t#PO#z#SO#{#WO#|#XO#}#YO$O#ZO$Q#]O$R#^O$S#_O$T#`O$U#aO$V#bO$W#bO$y#dO~O!y&mO~PCvO!y'UX#O'UX#P'UX#Y'UX!o'UXV'UX!r'UX#u'UX#w'UXx'UX~P&sO!z$hO#T&nO~Op$mOt$lO~O!p&oO~O#O&rO#T;cO#V;bO!y&}P~P9yOT6hO{6fO!T6iO!c6jO!p!{O!w8rO!z!}O#T#QO#X!zO#a!|O#b!|O#t#PO#z6gO#{6kO#|6lO#}6mO$O6nO$Q6pO$R6qO$S6rO$T6sO$U6tO$V6uO$W6uO$y#dO#O'OX#Y'OX~O#P&sO~PGXO#O&vO#Y&}X~O#Y&xO~O#O&}O!y'PP~P9yO!o'OO~PCvO!n#pa!p#pa#T#pa#q#rX&r#pa!y#pa#P#pax#pa~OT#pa{#pa!T#pa!c#pa!w#pa!z#pa#X#pa#a#pa#b#pa#t#pa#z#pa#{#pa#|#pa#}#pa$O#pa$Q#pa$R#pa$S#pa$T#pa$U#pa$V#pa$W#pa$y#pa#O#pa#Y#pa!o#paV#pa!r#pa#u#pa#w#pa~PIuO!t'QO~O!y'TO#m'RO~O!y'UX#m'UX#q#rX#T'UX#V'UX#c'UX!p'UX#P'UXx'UX!n'UX&r'UX~O#T'XO~P*kO!n$Xa&r$Xa!y$Xa!o$Xa~PCvO!n$Ya&r$Ya!y$Ya!o$Ya~PCvO!n$Za&r$Za!y$Za!o$Za~PCvO!n$[a&r$[a!y$[a!o$[a~PCvO!p!{O!z!}O#X!zO#a!|O#b!|O#t#PO$y#dOT$[a!T$[a!c$[a!n$[a!w$[a#T$[a#z$[a#{$[a#|$[a#}$[a$O$[a$Q$[a$R$[a$S$[a$T$[a$U$[a$V$[a$W$[a&r$[a!y$[a!o$[a~O{#RO~P! OO!n$_a&r$_a!y$_a!o$_a~PCvO!z!}O#O$fX#Y$fX~O#O']O#Y'YX~O#Y'_O~O!t$kO#T'`O~O^'bO~O!t'dO~O['eO~O!t'fO~O!a'lO#T'jO#V'kO#c'iO$drO!y'WP~P0aO!_'rO!pXO!r'qO~O!t'tO!z$hO~O!z$hO#T'vO~O!z$hO#T'xO~O#u'yO!n$rX#O$rX&r$rX~O#O'zO!n'aX&r'aX~O!n#cO&r#cO~O!r(OO#P'}O~O!n$ka&r$ka!y$ka!o$ka~PCvOm(QOx(RO!p(SO!z!}O~O!p!{O!z!}O#X!zO#a!|O#b!|O#t#PO~OT$xa{$xa!T$xa!c$xa!n$xa!w$xa#T$xa#z$xa#{$xa#|$xa#}$xa$O$xa$Q$xa$R$xa$S$xa$T$xa$U$xa$V$xa$W$xa$y$xa&r$xa!y$xa#O$xa#P$xa#Y$xa!o$xa!r$xaV$xa#u$xa#w$xa~P!']O!n${a&r${a!y${a!o${a~PCvO#X(ZO#a(XO#b(XO&q(YOR&fX!p&fX#c&fX#f&fX&p&fX'e&fX~O'e(^O~P8lO!r(_O~PhO!p(bO!r(cO~O!r(_O&r(fO~PhO!b(jO~O!n(kO~P9yOZ(vOo(wO~O!t(yO~OT6hO{6fO!T6iO!c6jO!w8rO#O(zO#T#QO#z6gO#{6kO#|6lO#}6mO$O6nO$Q6pO$R6qO$S6rO$T6sO$U6tO$V6uO$W6uO$y#dO!n'iX&r'iX~P!']O#u)OO~O#O)PO!n'_X&r'_X~Om(QOx(RO!p(SO~Om(QO!p(SO~Ox(RO!p)YO!r)]O~O!n#cO!pXO&r#cO~O!p%qO!t#yO~OV)cO#O)aO!n'jX&r'jX~O^)eOt)eO!t#gO#qeO~O!p%qO!t#gO#q)jO~OT6hO{6fO!T6iO!c6jO!w8rO#O)kO#T#QO#z6gO#{6kO#|6lO#}6mO$O6nO$Q6pO$R6qO$S6rO$T6sO$U6tO$V6uO$W6uO$y#dO!n&{X&r&{X#P&{X~P!']O!j)nO&s)nO~OT8uO{8sO!T8vO!c8wO!r)oO!w=YO#T#QO#z8tO#{8xO#|8yO#}8zO$O8{O$Q8}O$R9OO$S9PO$T9QO$U9RO$V9SO$W9SO$y#dO~P!']OT8uO{8sO!T8vO!c8wO!w=YO#T#QO#Y)qO#z8tO#{8xO#|8yO#}8zO$O8{O$Q8}O$R9OO$S9PO$T9QO$U9RO$V9SO$W9SO$y#dO~P!']O!o)qO~PCvOT8uO{8sO!T8vO!c8wO!w=YO#T#QO#z8tO#{8xO#|8yO#}8zO$O8{O$Q8}O$R9OO$S9PO$T9QO$U9RO$V9SO$W9SO$y#dO!y'SX#O'SX~P!']OT'UX{'UX!T'UX!c'UX!p'UX!w'UX!z'UX#T'UX#X'UX#a'UX#b'UX#q#rX#t'UX#z'UX#{'UX#|'UX#}'UX$O'UX$Q'UX$R'UX$S'UX$T'UX$U'UX$V'UX$W'UX$y'UX~O!r)sO!y'UX#O'UX~P!5}O!y#jX#O#jX~P>ZO#O)uO!y'RX~O!y)wO~O$y#dOT#yi{#yi!T#yi!c#yi!n#yi!w#yi#T#yi#z#yi#{#yi#|#yi#}#yi$O#yi$Q#yi$R#yi$S#yi$T#yi$U#yi$V#yi$W#yi&r#yi!y#yi#O#yi#P#yi#Y#yi!o#yi!r#yiV#yi#u#yi#w#yi~P!']O{#RO#T#QO#z#SO#{#WO#|#XO#}#YO$O#ZO$Q#]O$R#^O$S#_O$T#`O$U#aO$V#bO$W#bO$y#dOT#yi!T#yi!c#yi!n#yi!w#yi&r#yi!y#yi!o#yi~P!']O{#RO!w!yO#T#QO#z#SO#{#WO#|#XO#}#YO$O#ZO$Q#]O$R#^O$S#_O$T#`O$U#aO$V#bO$W#bO$y#dOT#yi!T#yi!c#yi!n#yi&r#yi!y#yi!o#yi~P!']OT#TO{#RO!c#VO!w!yO#T#QO#z#SO#{#WO#|#XO#}#YO$O#ZO$Q#]O$R#^O$S#_O$T#`O$U#aO$V#bO$W#bO$y#dO!T#yi!n#yi&r#yi!y#yi!o#yi~P!']OT#TO{#RO!w!yO#T#QO#z#SO#{#WO#|#XO#}#YO$O#ZO$Q#]O$R#^O$S#_O$T#`O$U#aO$V#bO$W#bO$y#dO!T#yi!c#yi!n#yi&r#yi!y#yi!o#yi~P!']O{#RO#T#QO#|#XO#}#YO$O#ZO$Q#]O$R#^O$S#_O$T#`O$U#aO$V#bO$W#bO$y#dOT#yi!T#yi!c#yi!n#yi!w#yi#z#yi#{#yi&r#yi!y#yi!o#yi~P!']O{#RO#T#QO#}#YO$O#ZO$Q#]O$R#^O$S#_O$T#`O$U#aO$V#bO$W#bO$y#dOT#yi!T#yi!c#yi!n#yi!w#yi#z#yi#{#yi#|#yi&r#yi!y#yi!o#yi~P!']O{#RO#T#QO$O#ZO$Q#]O$R#^O$S#_O$T#`O$U#aO$V#bO$W#bO$y#dOT#yi!T#yi!c#yi!n#yi!w#yi#z#yi#{#yi#|#yi#}#yi&r#yi!y#yi!o#yi~P!']O{#RO#T#QO$Q#]O$R#^O$S#_O$T#`O$U#aO$V#bO$W#bO$y#dOT#yi!T#yi!c#yi!n#yi!w#yi#z#yi#{#yi#|#yi#}#yi$O#yi&r#yi!y#yi!o#yi~P!']O{#RO$Q#]O$R#^O$S#_O$T#`O$U#aO$V#bO$W#bO$y#dOT#yi!T#yi!c#yi!n#yi!w#yi#T#yi#z#yi#{#yi#|#yi#}#yi$O#yi&r#yi!y#yi!o#yi~P!']O{#RO$R#^O$S#_O$T#`O$U#aO$V#bO$W#bO$y#dOT#yi!T#yi!c#yi!n#yi!w#yi#T#yi#z#yi#{#yi#|#yi#}#yi$O#yi$Q#yi&r#yi!y#yi!o#yi~P!']O{#RO$S#_O$T#`O$U#aO$V#bO$W#bO$y#dOT#yi!T#yi!c#yi!n#yi!w#yi#T#yi#z#yi#{#yi#|#yi#}#yi$O#yi$Q#yi$R#yi&r#yi!y#yi!o#yi~P!']O{#RO$T#`O$V#bO$W#bO$y#dOT#yi!T#yi!c#yi!n#yi!w#yi#T#yi#z#yi#{#yi#|#yi#}#yi$O#yi$Q#yi$R#yi$S#yi$U#yi&r#yi!y#yi!o#yi~P!']O{#RO$V#bO$W#bO$y#dOT#yi!T#yi!c#yi!n#yi!w#yi#T#yi#z#yi#{#yi#|#yi#}#yi$O#yi$Q#yi$R#yi$S#yi$T#yi$U#yi&r#yi!y#yi!o#yi~P!']O{#RO$S#_O$T#`O$V#bO$W#bO$y#dOT#yi!T#yi!c#yi!n#yi!w#yi#T#yi#z#yi#{#yi#|#yi#}#yi$O#yi$Q#yi$R#yi$U#yi&r#yi!y#yi!o#yi~P!']O{#RO$W#bO$y#dOT#yi!T#yi!c#yi!n#yi!w#yi#T#yi#z#yi#{#yi#|#yi#}#yi$O#yi$Q#yi$R#yi$S#yi$T#yi$U#yi$V#yi&r#yi!y#yi!o#yi~P!']O`)xO~P9yO!y){O~O#T*OO~P9yOT6hO{6fO!T6iO!c6jO!w8rO#T#QO#z6gO#{6kO#|6lO#}6mO$O6nO$Q6pO$R6qO$S6rO$T6sO$U6tO$V6uO$W6uO$y#dO#O#Ua#Y#Ua#P#Ua!n#Ua&r#Ua!y#Ua!o#UaV#Ua!r#Ua~P!']OT6hO{6fO!T6iO!c6jO!w8rO#T#QO#z6gO#{6kO#|6lO#}6mO$O6nO$Q6pO$R6qO$S6rO$T6sO$U6tO$V6uO$W6uO$y#dO#O'Oa#Y'Oa#P'Oa!n'Oa&r'Oa!y'Oa!o'OaV'Oa!r'Oa~P!']O#T#oO#V#nO#O&VX#Y&VX~P9yO#O&vO#Y&}a~O#Y*RO~OT6hO{6fO!T6iO!c6jO!w8rO#O*TO#P*SO#T#QO#z6gO#{6kO#|6lO#}6mO$O6nO$Q6pO$R6qO$S6rO$T6sO$U6tO$V6uO$W6uO$y#dO!y'PX~P!']O#O*TO!y'PX~O!y*VO~O!n#pi!p#pi#T#pi#q#rX&r#pi!y#pi#P#pix#pi~OT#pi{#pi!T#pi!c#pi!w#pi!z#pi#X#pi#a#pi#b#pi#t#pi#z#pi#{#pi#|#pi#}#pi$O#pi$Q#pi$R#pi$S#pi$T#pi$U#pi$V#pi$W#pi$y#pi#O#pi#Y#pi!o#piV#pi!r#pi#u#pi#w#pi~P#+PO#m'RO!y#la#T#la#V#la#c#la!p#la#P#lax#la!n#la&r#la~OPgOQ|OU^OW}O]3}Op5wOt#hOy3yOz3yO!O`O!P]O!R2]O!S}O!U4TO!V3{O!W3{O!Z2_O!d3wO!t#gO!z[O#X_O#chO#eaO#fbO#qeO$T4RO$]4PO$^4RO$aqO$y2^O$z!OO$|}O$}}O%U|O'f{O~O#m#pa#V#pa#c#pa~PIuO{#RO!w!yO#T#QO#z#SO#{#WO#|#XO#}#YO$O#ZO$Q#]O$R#^O$S#_O$T#`O$U#aO$V#bO$W#bO$y#dOT#Qi!T#Qi!c#Qi!n#Qi&r#Qi!y#Qi!o#Qi~P!']O{#RO!w!yO#T#QO#z#SO#{#WO#|#XO#}#YO$O#ZO$Q#]O$R#^O$S#_O$T#`O$U#aO$V#bO$W#bO$y#dOT#vi!T#vi!c#vi!n#vi&r#vi!y#vi!o#vi~P!']O!n#xi&r#xi!y#xi!o#xi~PCvO!t#gO#qeO#O&]X#Y&]X~O#O']O#Y'Ya~O!t'tO~O!t*fO~Ox(RO!p)YO!r*iO~O#T*kO#V*lO#c*jO#m'RO~O#T*kO#V*lO#c*jO$drO~P0aO#u*nO!y$cX#O$cX~O#V*lO#c*jO~O#c*oO~O#c*qO~P0aO#O*rO!y'WX~O!y*tO~O!z*vO~O!_*zO!pXO!r*yO~O!r*|O!p'bi!n'bi&r'bi~O!r+PO#P+OO~O#c$nO!n&dX#O&dX&r&dX~O#O'zO!n'aa&r'aa~OT$ki{$ki!T$ki!c$ki!n$ki!p$ki!w$ki!z$ki#T$ki#X$ki#a$ki#b$ki#t$ki#u#ga#w#ga#z$ki#{$ki#|$ki#}$ki$O$ki$Q$ki$R$ki$S$ki$T$ki$U$ki$V$ki$W$ki$y$ki&r$ki!y$ki#O$ki#P$ki#Y$ki!o$ki!r$kiV$ki~OS+]O^+`On+]Ot$aO!_+cO!`+]O!a+]O!o+gO#c$nO$aqO$drO~P0aO!t+kO~O#X+mO#a+lO#b+lO~O!t+oO#c+oO$|+oO%S+nO~O!o+pO~PCvOd%WXe%WXi%WXk%WXg%WXh%WXf%WX~PhOd+tOe+rOP%ViQ%ViS%ViU%ViW%ViX%Vi[%Vi]%Vi^%Vi_%Via%Vib%Vic%Vil%Vin%Vip%Viq%Vir%Vit%Viu%Viv%Viw%Viy%Viz%Vi}%Vi!O%Vi!P%Vi!Q%Vi!R%Vi!S%Vi!U%Vi!V%Vi!W%Vi!X%Vi!Y%Vi!Z%Vi![%Vi!]%Vi!^%Vi!_%Vi!a%Vi!b%Vi!d%Vi!n%Vi!p%Vi!t%Vi!z%Vi#X%Vi#c%Vi#e%Vi#f%Vi#q%Vi$T%Vi$]%Vi$^%Vi$a%Vi$d%Vi$y%Vi$z%Vi$|%Vi$}%Vi%U%Vi&o%Vi'f%Vi&s%Vi!o%Vii%Vik%Vig%Vih%ViY%Vi`%Vij%Vif%Vi~Od+xOe+uOi+wO~OY+yO`+zO!o+}O~OY+yO`+zOj%]X~Oj,PO~Ok,QO~O!n,SO~P9yO!n,UO~Og,VO~OT6hOV,WO{6fO!T6iO!c6jO!w8rO#T#QO#z6gO#{6kO#|6lO#}6mO$O6nO$Q6pO$R6qO$S6rO$T6sO$U6tO$V6uO$W6uO$y#dO~P!']Oh,XO~O!z,YO~OZ(vOo(wOP%kiQ%kiS%kiU%kiW%kiX%ki[%ki]%ki^%ki_%kia%kib%kic%kil%kin%kip%kiq%kir%kit%kiu%kiv%kiw%kiy%kiz%ki}%ki!O%ki!P%ki!Q%ki!R%ki!S%ki!U%ki!V%ki!W%ki!X%ki!Y%ki!Z%ki![%ki!]%ki!^%ki!_%ki!a%ki!b%ki!d%ki!n%ki!p%ki!t%ki!z%ki#X%ki#c%ki#e%ki#f%ki#q%ki$T%ki$]%ki$^%ki$a%ki$d%ki$y%ki$z%ki$|%ki$}%ki%U%ki&o%ki'f%ki&s%ki!o%kid%kie%kii%kik%kig%kih%kiY%ki`%kij%kif%ki~O#u,^O~O#O(zO!n%ca&r%ca~O!y,aO~O!t%dO!n&cX#O&cX&r&cX~O#O)PO!n'_a&r'_a~OS+]OY,hOn+]Ot$aO!_+cO!`+]O!a+]O$aqO$drO~O!o,kO~P#J|O!p)YO~O!p%qO!t'QO~O!t#gO#qeO!n&mX#O&mX&r&mX~O#O)aO!n'ja&r'ja~O!t,qO~OV,rO!o%{X#O%{X~O#O,tO!o'kX~O!o,vO~O!n&TX#O&TX&r&TX#P&TX~P9yO#O)kO!n&{a&r&{a#P&{a~O{#RO#T#QO#z#SO#{#WO#|#XO#}#YO$O#ZO$Q#]O$R#^O$S#_O$T#`O$U#aO$V#bO$W#bO$y#dOT!vq!T!vq!c!vq!n!vq!w!vq&r!vq!y!vq!o!vq~P!']O!o,{O~PCvOT8uO{8sO!T8vO!c8wO!w=YO#T#QO#z8tO#{8xO#|8yO#}8zO$O8{O$Q8}O$R9OO$S9PO$T9QO$U9RO$V9SO$W9SO$y#dO!y#ja#O#ja~P!']O!y&XX#O&XX~PATO#O)uO!y'Ra~O#P-PO~O#O-QO!o&zX~O!o-SO~O!y-TO~OT6hO{6fO!T6iO!c6jO!w8rO#T#QO#z6gO#{6kO#|6lO#}6mO$O6nO$Q6pO$R6qO$S6rO$T6sO$U6tO$V6uO$W6uO$y#dO#O#Wi#Y#Wi~P!']O!y&WX#O&WX~P9yO#O*TO!y'Pa~O!y-ZO~OT#kq{#kq!T#kq!c#kq!n#kq!w#kq#T#kq#u#kq#w#kq#z#kq#{#kq#|#kq#}#kq$O#kq$Q#kq$R#kq$S#kq$T#kq$U#kq$V#kq$W#kq$y#kq&r#kq!y#kq#O#kq#P#kq#Y#kq!o#kq!r#kqV#kq~P!']O#m#pi#V#pi#c#pi~P#+PO{#RO!w!yO#T#QO#z#SO#{#WO#|#XO#}#YO$O#ZO$Q#]O$R#^O$S#_O$T#`O$U#aO$V#bO$W#bO$y#dOT#Qq!T#Qq!c#Qq!n#Qq&r#Qq!y#Qq!o#Qq~P!']O#u-cO!y$ca#O$ca~O#V-eO#c-dO~O#c-fO~O#T-gO#V-eO#c-dO#m'RO~O#c-iO#m'RO~O#u-jO!y$ha#O$ha~O!a'lO#T'jO#V'kO#c'iO$drO!y&^X#O&^X~P0aO#O*rO!y'Wa~O!pXO#m'RO~O#T-oO#c-nO!y'ZP~O!pXO!r-qO~O!r-tO!p'bq!n'bq&r'bq~O!_-vO!pXO!r-qO~O!r-zO#P-yO~OT6hO{6fO!T6iO!c6jO!w8rO#T#QO#z6gO#{6kO#|6lO#}6mO$O6nO$Q6pO$R6qO$S6rO$T6sO$U6tO$V6uO$W6uO$y#dO!n$ri#O$ri&r$ri~P!']O!n$jq&r$jq!y$jq!o$jq~PCvO#P-yO#m'RO~O#O-{Ox'[X!p'[X!n'[X&r'[X~O#c$nO#m'RO~OS+]O^.QOn+]Ot$aO!`+]O!a+]O#c$nO$aqO$drO~P0aOS+]O^.QOn+]Ot$aO!`+]O!a+]O#c$nO$aqO~P0aOS+]O^+`On+]Ot$aO!_+cO!`+]O!a+]O!o.YO#c$nO$aqO$drO~P0aO!t.]O~O!t.^O#c.^O$|.^O%S+nO~O$|._O~O#Y.`O~Od%Wae%Wai%Wak%Wag%Wah%Waf%Wa~PhOd.cOe+rOP%VqQ%VqS%VqU%VqW%VqX%Vq[%Vq]%Vq^%Vq_%Vqa%Vqb%Vqc%Vql%Vqn%Vqp%Vqq%Vqr%Vqt%Vqu%Vqv%Vqw%Vqy%Vqz%Vq}%Vq!O%Vq!P%Vq!Q%Vq!R%Vq!S%Vq!U%Vq!V%Vq!W%Vq!X%Vq!Y%Vq!Z%Vq![%Vq!]%Vq!^%Vq!_%Vq!a%Vq!b%Vq!d%Vq!n%Vq!p%Vq!t%Vq!z%Vq#X%Vq#c%Vq#e%Vq#f%Vq#q%Vq$T%Vq$]%Vq$^%Vq$a%Vq$d%Vq$y%Vq$z%Vq$|%Vq$}%Vq%U%Vq&o%Vq'f%Vq&s%Vq!o%Vqi%Vqk%Vqg%Vqh%VqY%Vq`%Vqj%Vqf%Vq~Od.hOe+uOi.gO~O!r(_O~OP6[OQ|OU^OW}O]:eOp>QOt#hOy:cOz:cO!O`O!P]O!R:jO!S}O!U:iO!V:dO!W:dO!Z:nO!d8fO!t#gO!z[O#X_O#chO#eaO#fbO#qeO$T:gO$]:fO$^:gO$aqO$y:lO$z!OO$|}O$}}O%U|O'f{O~O!n.kO!r.kO~OY+yO`+zO!o.mO~OY+yO`+zOj%]a~O!y.qO~P>ZO!n.sO~O!n.sO~P9yOQ|OW}O!S}O$|}O$}}O%U|O'f{O~OT6hO{6fO!T6iO!c6jO!w8rO#T#QO#z6gO#{6kO#|6lO#}6mO$O6nO$Q6pO$R6qO$S6rO$T6sO$U6tO$V6uO$W6uO$y#dO!n&ja#O&ja&r&ja~P!']OT6hO{6fO!T6iO!c6jO!w8rO#T#QO#z6gO#{6kO#|6lO#}6mO$O6nO$Q6pO$R6qO$S6rO$T6sO$U6tO$V6uO$W6uO$y#dO!n$pi#O$pi&r$pi~P!']OS+]On+]Ot$aO!`+]O!a+]O$aqO$drO~OY/OO~P$?[OS+]On+]Ot$aO!`+]O!a+]O$aqO~O!t/PO~O!o/RO~P#J|Ox(RO!p)YO#m'RO~OV/UO!n&ma#O&ma&r&ma~O#O)aO!n'ji&r'ji~O!t/WO~OV/XO!o%{a#O%{a~O^/ZOt/ZO!t#gO#qeO!o&nX#O&nX~O#O,tO!o'ka~OT6hO{6fO!T6iO!c6jO!w8rO#T#QO#z6gO#{6kO#|6lO#}6mO$O6nO$Q6pO$R6qO$S6rO$T6sO$U6tO$V6uO$W6uO$y#dO!n&Ta#O&Ta&r&Ta#P&Ta~P!']O{#RO#T#QO#z#SO#{#WO#|#XO#}#YO$O#ZO$Q#]O$R#^O$S#_O$T#`O$U#aO$V#bO$W#bO$y#dOT!vy!T!vy!c!vy!n!vy!w!vy&r!vy!y!vy!o!vy~P!']OT8uO{8sO!T8vO!c8wO!w=YO#T#QO#z8tO#{8xO#|8yO#}8zO$O8{O$Q8}O$R9OO$S9PO$T9QO$U9RO$V9SO$W9SO$y#dO!y#ii#O#ii~P!']O`)xO!o&UX#O&UX~P9yO#O-QO!o&za~OT6hO{6fO!T6iO!c6jO!w8rO#T#QO#z6gO#{6kO#|6lO#}6mO$O6nO$Q6pO$R6qO$S6rO$T6sO$U6tO$V6uO$W6uO$y#dO#O#Wq#Y#Wq~P!']OT8uO{8sO!T8vO!c8wO!w=YO#T#QO#z8tO#{8xO#|8yO#}8zO$O8{O$Q8}O$R9OO$S9PO$T9QO$U9RO$V9SO$W9SO$y#dO!y#]i#O#]i~P!']OT6hO{6fO!T6iO!c6jO!w8rO#P/bO#T#QO#z6gO#{6kO#|6lO#}6mO$O6nO$Q6pO$R6qO$S6rO$T6sO$U6tO$V6uO$W6uO$y#dO!y&Wa#O&Wa~P!']O#u/hO!y$ci#O$ci~O#c/iO~O#V/kO#c/jO~OT8uO{8sO!T8vO!c8wO!w=YO#T#QO#z8tO#{8xO#|8yO#}8zO$O8{O$Q8}O$R9OO$S9PO$T9QO$U9RO$V9SO$W9SO$y#dO!y$ci#O$ci~P!']O#u/lO!y$hi#O$hi~O#O/nO!y'ZX~O#c/pO~O!y/qO~O!pXO!r/tO~O#m'RO!p'by!n'by&r'by~O!n$jy&r$jy!y$jy!o$jy~PCvO#P/wO#m'RO~O!t#gO#qeOx&`X!p&`X#O&`X!n&`X&r&`X~O#O-{Ox'[a!p'[a!n'[a&r'[a~OU$PO^0PO!S$PO!t$OO!w#}O#c$nO#q2WO~P$?zO!n#cO!p0UO&r#cO~O#Y0XO~Oi0^O~OT:sO{:oO!T:uO!c:wO!n0_O!r0_O!w=lO#T#QO#z:qO#{:yO#|:{O#}:}O$O;PO$Q;TO$R;VO$S;XO$T;ZO$U;]O$V;_O$W;_O$y#dO~P!']OY%[a`%[a!o%[aj%[a~PhO!y0aO~O!y0aO~P>ZO!n0cO~OT6hO{6fO!T6iO!c6jO!w8rO!y0eO#P0dO#T#QO#z6gO#{6kO#|6lO#}6mO$O6nO$Q6pO$R6qO$S6rO$T6sO$U6tO$V6uO$W6uO$y#dO~P!']O!y0eO~O!y0fO#c0gO#m'RO~O!y0hO~O!t0iO~O!n#cO#u0kO&r#cO~O!t0lO~O#O)aO!n'jq&r'jq~O!t0mO~OV0nO!o%|X#O%|X~OT:sO{:oO!T:uO!c:wO!w=lO#T#QO#z:qO#{:yO#|:{O#}:}O$O;PO$Q;TO$R;VO$S;XO$T;ZO$U;]O$V;_O$W;_O$y#dO!o!}i#O!}i~P!']OT8uO{8sO!T8vO!c8wO!w=YO#T#QO#z8tO#{8xO#|8yO#}8zO$O8{O$Q8}O$R9OO$S9PO$T9QO$U9RO$V9SO$W9SO$y#dO!y$cq#O$cq~P!']O#u0uO!y$cq#O$cq~O#c0vO~OT8uO{8sO!T8vO!c8wO!w=YO#T#QO#z8tO#{8xO#|8yO#}8zO$O8{O$Q8}O$R9OO$S9PO$T9QO$U9RO$V9SO$W9SO$y#dO!y$hq#O$hq~P!']O#T0yO#c0xO!y&_X#O&_X~O#O/nO!y'Za~O#m'RO!p'b!R!n'b!R&r'b!R~O!pXO!r1OO~O!n$j!R&r$j!R!y$j!R!o$j!R~PCvO#P1QO#m'RO~OP6[OU^O]9VOp>ROt#hOy9VOz9VO!O`O!P]O!R:kO!U9VO!V9VO!W9VO!Z9VO!d8gO!o1]O!t1XO!z[O#X_O#chO#eaO#fbO#qeO$T:hO$]9VO$^:hO$aqO$y:mO$z!OO~P$;qOi1^O~OY%Zi`%Zi!o%Zij%Zi~PhOY%[i`%[i!o%[ij%[i~PhO!y1aO~O!y1aO~P>ZO!y1dO~O!n#cO#u1hO&r#cO~O$|1iO%U1iO~O!t1jO~OV1kO!o%|a#O%|a~OT8uO{8sO!T8vO!c8wO!w=YO#T#QO#z8tO#{8xO#|8yO#}8zO$O8{O$Q8}O$R9OO$S9PO$T9QO$U9RO$V9SO$W9SO$y#dO!y#^i#O#^i~P!']OT8uO{8sO!T8vO!c8wO!w=YO#T#QO#z8tO#{8xO#|8yO#}8zO$O8{O$Q8}O$R9OO$S9PO$T9QO$U9RO$V9SO$W9SO$y#dO!y$cy#O$cy~P!']OT8uO{8sO!T8vO!c8wO!w=YO#T#QO#z8tO#{8xO#|8yO#}8zO$O8{O$Q8}O$R9OO$S9PO$T9QO$U9RO$V9SO$W9SO$y#dO!y$hy#O$hy~P!']O#c1mO~O#O/nO!y'Zi~O!n$j!Z&r$j!Z!y$j!Z!o$j!Z~PCvOT:tO{:pO!T:vO!c:xO!w=mO#T#QO#z:rO#{:zO#|:|O#};OO$O;QO$Q;UO$R;WO$S;YO$T;[O$U;^O$V;`O$W;`O$y#dO~P!']OV1tO|1sO~P!5}OV1tO|1sOT&|X{&|X!T&|X!c&|X!p&|X!w&|X!z&|X#T&|X#X&|X#a&|X#b&|X#t&|X#u&|X#w&|X#z&|X#{&|X#|&|X#}&|X$O&|X$Q&|X$R&|X$S&|X$T&|X$U&|X$V&|X$W&|X$y&|X~OP6[OU^O]9VOp>ROt#hOy9VOz9VO!O`O!P]O!R:kO!U9VO!V9VO!W9VO!Z9VO!d8gO!o1wO!t1XO!z[O#X_O#chO#eaO#fbO#qeO$T:hO$]9VO$^:hO$aqO$y:mO$z!OO~P$;qOY%Zq`%Zq!o%Zqj%Zq~PhO!y1yO~O!y%fi~PCvOf1zO~O$|1{O%U1{O~O!t1}O~OT8uO{8sO!T8vO!c8wO!w=YO#T#QO#z8tO#{8xO#|8yO#}8zO$O8{O$Q8}O$R9OO$S9PO$T9QO$U9RO$V9SO$W9SO$y#dO!y$c!R#O$c!R~P!']O!n$j!c&r$j!c!y$j!c!o$j!c~PCvO!t2PO~O!a2RO!t2QO~O!t2UO!n$wi&r$wi~O!t'VO~O!t*[O~OT2bO{2`O!T2cO!c2dO!w4VO#T#QO#z2aO#{2eO#|2fO#}2gO$O2hO$Q2jO$R2kO$S2lO$T2mO$U2nO$V2oO$W2oO$y#dO!n$ka#u$ka#w$ka&r$ka!y$ka!o$ka!r$ka#Y$ka#O$ka~P!']O#T2[O~P*kO[$tO~P#._OT6hO{6fO!T6iO!c6jO!w8rO#P2ZO#T#QO#z6gO#{6kO#|6lO#}6mO$O6nO$Q6pO$R6qO$S6rO$T6sO$U6tO$V6uO$W6uO$y#dO!n'OX&r'OX!y'OX!o'OX~P!']OT4eO{4cO!T4fO!c4gO!w6SO#P3tO#T#QO#z4dO#{4hO#|4iO#}4jO$O4kO$Q4mO$R4nO$S4oO$T4pO$U4qO$V4rO$W4rO$y#dO#O'OX#Y'OX#u'OX#w'OX!n'OX&r'OX!y'OX!o'OXV'OX!r'OX~P!']O#T3cO~P#._OT2bO{2`O!T2cO!c2dO!w4VO#T#QO#z2aO#{2eO#|2fO#}2gO$O2hO$Q2jO$R2kO$S2lO$T2mO$U2nO$V2oO$W2oO$y#dO!n$Xa#u$Xa#w$Xa&r$Xa!y$Xa!o$Xa!r$Xa#Y$Xa#O$Xa~P!']OT2bO{2`O!T2cO!c2dO!w4VO#T#QO#z2aO#{2eO#|2fO#}2gO$O2hO$Q2jO$R2kO$S2lO$T2mO$U2nO$V2oO$W2oO$y#dO!n$Ya#u$Ya#w$Ya&r$Ya!y$Ya!o$Ya!r$Ya#Y$Ya#O$Ya~P!']OT2bO{2`O!T2cO!c2dO!w4VO#T#QO#z2aO#{2eO#|2fO#}2gO$O2hO$Q2jO$R2kO$S2lO$T2mO$U2nO$V2oO$W2oO$y#dO!n$Za#u$Za#w$Za&r$Za!y$Za!o$Za!r$Za#Y$Za#O$Za~P!']OT2bO{2`O!T2cO!c2dO!w4VO#T#QO#z2aO#{2eO#|2fO#}2gO$O2hO$Q2jO$R2kO$S2lO$T2mO$U2nO$V2oO$W2oO$y#dO!n$[a#u$[a#w$[a&r$[a!y$[a!o$[a!r$[a#Y$[a#O$[a~P!']O{2`O#u$[a#w$[a!r$[a#Y$[a#O$[a~P! OOT2bO{2`O!T2cO!c2dO!w4VO#T#QO#z2aO#{2eO#|2fO#}2gO$O2hO$Q2jO$R2kO$S2lO$T2mO$U2nO$V2oO$W2oO$y#dO!n$_a#u$_a#w$_a&r$_a!y$_a!o$_a!r$_a#Y$_a#O$_a~P!']OT2bO{2`O!T2cO!c2dO!w4VO#T#QO#z2aO#{2eO#|2fO#}2gO$O2hO$Q2jO$R2kO$S2lO$T2mO$U2nO$V2oO$W2oO$y#dO!n${a#u${a#w${a&r${a!y${a!o${a!r${a#Y${a#O${a~P!']O{2`O#T#QO#z2aO#{2eO#|2fO#}2gO$O2hO$Q2jO$R2kO$S2lO$T2mO$U2nO$V2oO$W2oO$y#dOT#yi!T#yi!c#yi!n#yi!w#yi#u#yi#w#yi&r#yi!y#yi!o#yi!r#yi#Y#yi#O#yi~P!']O{2`O!w4VO#T#QO#z2aO#{2eO#|2fO#}2gO$O2hO$Q2jO$R2kO$S2lO$T2mO$U2nO$V2oO$W2oO$y#dOT#yi!T#yi!c#yi!n#yi#u#yi#w#yi&r#yi!y#yi!o#yi!r#yi#Y#yi#O#yi~P!']OT2bO{2`O!c2dO!w4VO#T#QO#z2aO#{2eO#|2fO#}2gO$O2hO$Q2jO$R2kO$S2lO$T2mO$U2nO$V2oO$W2oO$y#dO!T#yi!n#yi#u#yi#w#yi&r#yi!y#yi!o#yi!r#yi#Y#yi#O#yi~P!']OT2bO{2`O!w4VO#T#QO#z2aO#{2eO#|2fO#}2gO$O2hO$Q2jO$R2kO$S2lO$T2mO$U2nO$V2oO$W2oO$y#dO!T#yi!c#yi!n#yi#u#yi#w#yi&r#yi!y#yi!o#yi!r#yi#Y#yi#O#yi~P!']O{2`O#T#QO#|2fO#}2gO$O2hO$Q2jO$R2kO$S2lO$T2mO$U2nO$V2oO$W2oO$y#dOT#yi!T#yi!c#yi!n#yi!w#yi#u#yi#w#yi#z#yi#{#yi&r#yi!y#yi!o#yi!r#yi#Y#yi#O#yi~P!']O{2`O#T#QO#}2gO$O2hO$Q2jO$R2kO$S2lO$T2mO$U2nO$V2oO$W2oO$y#dOT#yi!T#yi!c#yi!n#yi!w#yi#u#yi#w#yi#z#yi#{#yi#|#yi&r#yi!y#yi!o#yi!r#yi#Y#yi#O#yi~P!']O{2`O#T#QO$O2hO$Q2jO$R2kO$S2lO$T2mO$U2nO$V2oO$W2oO$y#dOT#yi!T#yi!c#yi!n#yi!w#yi#u#yi#w#yi#z#yi#{#yi#|#yi#}#yi&r#yi!y#yi!o#yi!r#yi#Y#yi#O#yi~P!']O{2`O#T#QO$Q2jO$R2kO$S2lO$T2mO$U2nO$V2oO$W2oO$y#dOT#yi!T#yi!c#yi!n#yi!w#yi#u#yi#w#yi#z#yi#{#yi#|#yi#}#yi$O#yi&r#yi!y#yi!o#yi!r#yi#Y#yi#O#yi~P!']O{2`O$Q2jO$R2kO$S2lO$T2mO$U2nO$V2oO$W2oO$y#dOT#yi!T#yi!c#yi!n#yi!w#yi#T#yi#u#yi#w#yi#z#yi#{#yi#|#yi#}#yi$O#yi&r#yi!y#yi!o#yi!r#yi#Y#yi#O#yi~P!']O{2`O$R2kO$S2lO$T2mO$U2nO$V2oO$W2oO$y#dOT#yi!T#yi!c#yi!n#yi!w#yi#T#yi#u#yi#w#yi#z#yi#{#yi#|#yi#}#yi$O#yi$Q#yi&r#yi!y#yi!o#yi!r#yi#Y#yi#O#yi~P!']O{2`O$S2lO$T2mO$U2nO$V2oO$W2oO$y#dOT#yi!T#yi!c#yi!n#yi!w#yi#T#yi#u#yi#w#yi#z#yi#{#yi#|#yi#}#yi$O#yi$Q#yi$R#yi&r#yi!y#yi!o#yi!r#yi#Y#yi#O#yi~P!']O{2`O$T2mO$V2oO$W2oO$y#dOT#yi!T#yi!c#yi!n#yi!w#yi#T#yi#u#yi#w#yi#z#yi#{#yi#|#yi#}#yi$O#yi$Q#yi$R#yi$S#yi$U#yi&r#yi!y#yi!o#yi!r#yi#Y#yi#O#yi~P!']O{2`O$V2oO$W2oO$y#dOT#yi!T#yi!c#yi!n#yi!w#yi#T#yi#u#yi#w#yi#z#yi#{#yi#|#yi#}#yi$O#yi$Q#yi$R#yi$S#yi$T#yi$U#yi&r#yi!y#yi!o#yi!r#yi#Y#yi#O#yi~P!']O{2`O$S2lO$T2mO$V2oO$W2oO$y#dOT#yi!T#yi!c#yi!n#yi!w#yi#T#yi#u#yi#w#yi#z#yi#{#yi#|#yi#}#yi$O#yi$Q#yi$R#yi$U#yi&r#yi!y#yi!o#yi!r#yi#Y#yi#O#yi~P!']O{2`O$W2oO$y#dOT#yi!T#yi!c#yi!n#yi!w#yi#T#yi#u#yi#w#yi#z#yi#{#yi#|#yi#}#yi$O#yi$Q#yi$R#yi$S#yi$T#yi$U#yi$V#yi&r#yi!y#yi!o#yi!r#yi#Y#yi#O#yi~P!']OT2bO{2`O!T2cO!c2dO!w4VO#T#QO#z2aO#{2eO#|2fO#}2gO$O2hO$Q2jO$R2kO$S2lO$T2mO$U2nO$V2oO$W2oO$y#dO!n#Ua#u#Ua#w#Ua&r#Ua!y#Ua!o#Ua!r#Ua#Y#Ua#O#Ua~P!']OT2bO{2`O!T2cO!c2dO!w4VO#T#QO#z2aO#{2eO#|2fO#}2gO$O2hO$Q2jO$R2kO$S2lO$T2mO$U2nO$V2oO$W2oO$y#dO!n'Oa#u'Oa#w'Oa&r'Oa!y'Oa!o'Oa!r'Oa#Y'Oa#O'Oa~P!']O{2`O!w4VO#T#QO#z2aO#{2eO#|2fO#}2gO$O2hO$Q2jO$R2kO$S2lO$T2mO$U2nO$V2oO$W2oO$y#dOT#Qi!T#Qi!c#Qi!n#Qi#u#Qi#w#Qi&r#Qi!y#Qi!o#Qi!r#Qi#Y#Qi#O#Qi~P!']O{2`O!w4VO#T#QO#z2aO#{2eO#|2fO#}2gO$O2hO$Q2jO$R2kO$S2lO$T2mO$U2nO$V2oO$W2oO$y#dOT#vi!T#vi!c#vi!n#vi#u#vi#w#vi&r#vi!y#vi!o#vi!r#vi#Y#vi#O#vi~P!']OT2bO{2`O!T2cO!c2dO!w4VO#T#QO#z2aO#{2eO#|2fO#}2gO$O2hO$Q2jO$R2kO$S2lO$T2mO$U2nO$V2oO$W2oO$y#dO!n#xi#u#xi#w#xi&r#xi!y#xi!o#xi!r#xi#Y#xi#O#xi~P!']O{2`O#T#QO#z2aO#{2eO#|2fO#}2gO$O2hO$Q2jO$R2kO$S2lO$T2mO$U2nO$V2oO$W2oO$y#dOT!vq!T!vq!c!vq!n!vq!w!vq#u!vq#w!vq&r!vq!y!vq!o!vq!r!vq#Y!vq#O!vq~P!']O{2`O!w4VO#T#QO#z2aO#{2eO#|2fO#}2gO$O2hO$Q2jO$R2kO$S2lO$T2mO$U2nO$V2oO$W2oO$y#dOT#Qq!T#Qq!c#Qq!n#Qq#u#Qq#w#Qq&r#Qq!y#Qq!o#Qq!r#Qq#Y#Qq#O#Qq~P!']OT2bO{2`O!T2cO!c2dO!w4VO#T#QO#z2aO#{2eO#|2fO#}2gO$O2hO$Q2jO$R2kO$S2lO$T2mO$U2nO$V2oO$W2oO$y#dO!n$jq#u$jq#w$jq&r$jq!y$jq!o$jq!r$jq#Y$jq#O$jq~P!']O{2`O#T#QO#z2aO#{2eO#|2fO#}2gO$O2hO$Q2jO$R2kO$S2lO$T2mO$U2nO$V2oO$W2oO$y#dOT!vy!T!vy!c!vy!n!vy!w!vy#u!vy#w!vy&r!vy!y!vy!o!vy!r!vy#Y!vy#O!vy~P!']OT2bO{2`O!T2cO!c2dO!w4VO#T#QO#z2aO#{2eO#|2fO#}2gO$O2hO$Q2jO$R2kO$S2lO$T2mO$U2nO$V2oO$W2oO$y#dO!n$jy#u$jy#w$jy&r$jy!y$jy!o$jy!r$jy#Y$jy#O$jy~P!']OT2bO{2`O!T2cO!c2dO!w4VO#T#QO#z2aO#{2eO#|2fO#}2gO$O2hO$Q2jO$R2kO$S2lO$T2mO$U2nO$V2oO$W2oO$y#dO!n$j!R#u$j!R#w$j!R&r$j!R!y$j!R!o$j!R!r$j!R#Y$j!R#O$j!R~P!']OT2bO{2`O!T2cO!c2dO!w4VO#T#QO#z2aO#{2eO#|2fO#}2gO$O2hO$Q2jO$R2kO$S2lO$T2mO$U2nO$V2oO$W2oO$y#dO!n$j!Z#u$j!Z#w$j!Z&r$j!Z!y$j!Z!o$j!Z!r$j!Z#Y$j!Z#O$j!Z~P!']OT2bO{2`O!T2cO!c2dO!w4VO#T#QO#z2aO#{2eO#|2fO#}2gO$O2hO$Q2jO$R2kO$S2lO$T2mO$U2nO$V2oO$W2oO$y#dO!n$j!c#u$j!c#w$j!c&r$j!c!y$j!c!o$j!c!r$j!c#Y$j!c#O$j!c~P!']OP6[OU^O]4OOp8]Ot#hOy3zOz3zO!O`O!P]O!R4`O!U4UO!V3|O!W3|O!Z4bO!d3xO!t#gO!z[O#T3uO#X_O#chO#eaO#fbO#qeO$T4SO$]4QO$^4SO$aqO$y4aO$z!OO~P$;qOP6[OU^O]4OOp8]Ot#hOy3zOz3zO!O`O!P]O!R4`O!U4UO!V3|O!W3|O!Z4bO!d3xO!t#gO!z[O#X_O#chO#eaO#fbO#qeO$T4SO$]4QO$^4SO$aqO$y4aO$z!OO~P$;qO#u2tO#w2uO!r&yX#Y&yX#O&yX~P0rOP6[OU^O]4OOp8]Os2vOt#hOy3zOz3zO!O`O!P]O!R4`O!U4UO!V3|O!W3|O!Z4bO!d3xO!t#gO!z[O#T2sO#V2rO#X_O#chO#eaO#fbO#qeO$T4SO$]4QO$^4SO$aqO$y4aO$z!OOT#xX{#xX!T#xX!c#xX!n#xX!p#xX!w#xX#a#xX#b#xX#t#xX#u#xX#w#xX#z#xX#{#xX#|#xX#}#xX$O#xX$Q#xX$R#xX$S#xX$U#xX$V#xX$W#xX&r#xX!y#xX!o#xX!r#xX#Y#xX#O#xX~P$;qOP6[OU^O]4OOp8]Os4wOt#hOy3zOz3zO!O`O!P]O!R4`O!U4UO!V3|O!W3|O!Z4bO!d3xO!t#gO!z[O#T4tO#V4sO#X_O#chO#eaO#fbO#qeO$T4SO$]4QO$^4SO$aqO$y4aO$z!OOT#xX{#xX!T#xX!c#xX!p#xX!w#xX#O#xX#P#xX#Y#xX#a#xX#b#xX#t#xX#u#xX#w#xX#z#xX#{#xX#|#xX#}#xX$O#xX$Q#xX$R#xX$S#xX$U#xX$V#xX$W#xX!n#xX&r#xX!y#xX!o#xXV#xX!r#xX~P$;qO!r3OO~P>ZO!r5|O#P3fO~OT8uO{8sO!T8vO!c8wO!r3gO!w=YO#T#QO#z8tO#{8xO#|8yO#}8zO$O8{O$Q8}O$R9OO$S9PO$T9QO$U9RO$V9SO$W9SO$y#dO~P!']O!r5}O#P3jO~O!r6OO#P3nO~O#P3nO#m'RO~O#P3oO#m'RO~O#P3rO#m'RO~OP6[OU^O[$tO]4OOp8]Ot#hOy3zOz3zO!O`O!P]O!R4`O!U4UO!V3|O!W3|O!Z4bO!d3xO!t#gO!z[O#X_O#chO#eaO#fbO#qeO$T4SO$]4QO$^4SO$aqO$y4aO$z!OO~P$;qOP6[OU^O]4OOp8]Ot#hOy3zOz3zO!O`O!P]O!R4`O!U4UO!V3|O!W3|O!Z4bO!d3xO!t#gO!z[O#T5dO#X_O#chO#eaO#fbO#qeO$T4SO$]4QO$^4SO$aqO$y4aO$z!OO~P$;qOT4eO{4cO!T4fO!c4gO!w6SO#T#QO#z4dO#{4hO#|4iO#}4jO$O4kO$Q4mO$R4nO$S4oO$T4pO$U4qO$V4rO$W4rO$y#dO#O$Xa#P$Xa#Y$Xa#u$Xa#w$Xa!n$Xa&r$Xa!y$Xa!o$XaV$Xa!r$Xa~P!']OT4eO{4cO!T4fO!c4gO!w6SO#T#QO#z4dO#{4hO#|4iO#}4jO$O4kO$Q4mO$R4nO$S4oO$T4pO$U4qO$V4rO$W4rO$y#dO#O$Ya#P$Ya#Y$Ya#u$Ya#w$Ya!n$Ya&r$Ya!y$Ya!o$YaV$Ya!r$Ya~P!']OT4eO{4cO!T4fO!c4gO!w6SO#T#QO#z4dO#{4hO#|4iO#}4jO$O4kO$Q4mO$R4nO$S4oO$T4pO$U4qO$V4rO$W4rO$y#dO#O$Za#P$Za#Y$Za#u$Za#w$Za!n$Za&r$Za!y$Za!o$ZaV$Za!r$Za~P!']OT4eO{4cO!T4fO!c4gO!w6SO#T#QO#z4dO#{4hO#|4iO#}4jO$O4kO$Q4mO$R4nO$S4oO$T4pO$U4qO$V4rO$W4rO$y#dO#O$[a#P$[a#Y$[a#u$[a#w$[a!n$[a&r$[a!y$[a!o$[aV$[a!r$[a~P!']O{4cO#O$[a#P$[a#Y$[a#u$[a#w$[aV$[a!r$[a~P! OOT4eO{4cO!T4fO!c4gO!w6SO#T#QO#z4dO#{4hO#|4iO#}4jO$O4kO$Q4mO$R4nO$S4oO$T4pO$U4qO$V4rO$W4rO$y#dO#O$_a#P$_a#Y$_a#u$_a#w$_a!n$_a&r$_a!y$_a!o$_aV$_a!r$_a~P!']OT4eO{4cO!T4fO!c4gO!w6SO#T#QO#z4dO#{4hO#|4iO#}4jO$O4kO$Q4mO$R4nO$S4oO$T4pO$U4qO$V4rO$W4rO$y#dO#O${a#P${a#Y${a#u${a#w${a!n${a&r${a!y${a!o${aV${a!r${a~P!']O{4cO#T#QO#z4dO#{4hO#|4iO#}4jO$O4kO$Q4mO$R4nO$S4oO$T4pO$U4qO$V4rO$W4rO$y#dOT#yi!T#yi!c#yi!w#yi#O#yi#P#yi#Y#yi#u#yi#w#yi!n#yi&r#yi!y#yi!o#yiV#yi!r#yi~P!']O{4cO!w6SO#T#QO#z4dO#{4hO#|4iO#}4jO$O4kO$Q4mO$R4nO$S4oO$T4pO$U4qO$V4rO$W4rO$y#dOT#yi!T#yi!c#yi#O#yi#P#yi#Y#yi#u#yi#w#yi!n#yi&r#yi!y#yi!o#yiV#yi!r#yi~P!']OT4eO{4cO!c4gO!w6SO#T#QO#z4dO#{4hO#|4iO#}4jO$O4kO$Q4mO$R4nO$S4oO$T4pO$U4qO$V4rO$W4rO$y#dO!T#yi#O#yi#P#yi#Y#yi#u#yi#w#yi!n#yi&r#yi!y#yi!o#yiV#yi!r#yi~P!']OT4eO{4cO!w6SO#T#QO#z4dO#{4hO#|4iO#}4jO$O4kO$Q4mO$R4nO$S4oO$T4pO$U4qO$V4rO$W4rO$y#dO!T#yi!c#yi#O#yi#P#yi#Y#yi#u#yi#w#yi!n#yi&r#yi!y#yi!o#yiV#yi!r#yi~P!']O{4cO#T#QO#|4iO#}4jO$O4kO$Q4mO$R4nO$S4oO$T4pO$U4qO$V4rO$W4rO$y#dOT#yi!T#yi!c#yi!w#yi#O#yi#P#yi#Y#yi#u#yi#w#yi#z#yi#{#yi!n#yi&r#yi!y#yi!o#yiV#yi!r#yi~P!']O{4cO#T#QO#}4jO$O4kO$Q4mO$R4nO$S4oO$T4pO$U4qO$V4rO$W4rO$y#dOT#yi!T#yi!c#yi!w#yi#O#yi#P#yi#Y#yi#u#yi#w#yi#z#yi#{#yi#|#yi!n#yi&r#yi!y#yi!o#yiV#yi!r#yi~P!']O{4cO#T#QO$O4kO$Q4mO$R4nO$S4oO$T4pO$U4qO$V4rO$W4rO$y#dOT#yi!T#yi!c#yi!w#yi#O#yi#P#yi#Y#yi#u#yi#w#yi#z#yi#{#yi#|#yi#}#yi!n#yi&r#yi!y#yi!o#yiV#yi!r#yi~P!']O{4cO#T#QO$Q4mO$R4nO$S4oO$T4pO$U4qO$V4rO$W4rO$y#dOT#yi!T#yi!c#yi!w#yi#O#yi#P#yi#Y#yi#u#yi#w#yi#z#yi#{#yi#|#yi#}#yi$O#yi!n#yi&r#yi!y#yi!o#yiV#yi!r#yi~P!']O{4cO$Q4mO$R4nO$S4oO$T4pO$U4qO$V4rO$W4rO$y#dOT#yi!T#yi!c#yi!w#yi#O#yi#P#yi#T#yi#Y#yi#u#yi#w#yi#z#yi#{#yi#|#yi#}#yi$O#yi!n#yi&r#yi!y#yi!o#yiV#yi!r#yi~P!']O{4cO$R4nO$S4oO$T4pO$U4qO$V4rO$W4rO$y#dOT#yi!T#yi!c#yi!w#yi#O#yi#P#yi#T#yi#Y#yi#u#yi#w#yi#z#yi#{#yi#|#yi#}#yi$O#yi$Q#yi!n#yi&r#yi!y#yi!o#yiV#yi!r#yi~P!']O{4cO$S4oO$T4pO$U4qO$V4rO$W4rO$y#dOT#yi!T#yi!c#yi!w#yi#O#yi#P#yi#T#yi#Y#yi#u#yi#w#yi#z#yi#{#yi#|#yi#}#yi$O#yi$Q#yi$R#yi!n#yi&r#yi!y#yi!o#yiV#yi!r#yi~P!']O{4cO$T4pO$V4rO$W4rO$y#dOT#yi!T#yi!c#yi!w#yi#O#yi#P#yi#T#yi#Y#yi#u#yi#w#yi#z#yi#{#yi#|#yi#}#yi$O#yi$Q#yi$R#yi$S#yi$U#yi!n#yi&r#yi!y#yi!o#yiV#yi!r#yi~P!']O{4cO$V4rO$W4rO$y#dOT#yi!T#yi!c#yi!w#yi#O#yi#P#yi#T#yi#Y#yi#u#yi#w#yi#z#yi#{#yi#|#yi#}#yi$O#yi$Q#yi$R#yi$S#yi$T#yi$U#yi!n#yi&r#yi!y#yi!o#yiV#yi!r#yi~P!']O{4cO$S4oO$T4pO$V4rO$W4rO$y#dOT#yi!T#yi!c#yi!w#yi#O#yi#P#yi#T#yi#Y#yi#u#yi#w#yi#z#yi#{#yi#|#yi#}#yi$O#yi$Q#yi$R#yi$U#yi!n#yi&r#yi!y#yi!o#yiV#yi!r#yi~P!']O{4cO$W4rO$y#dOT#yi!T#yi!c#yi!w#yi#O#yi#P#yi#T#yi#Y#yi#u#yi#w#yi#z#yi#{#yi#|#yi#}#yi$O#yi$Q#yi$R#yi$S#yi$T#yi$U#yi$V#yi!n#yi&r#yi!y#yi!o#yiV#yi!r#yi~P!']OT4eO{4cO!T4fO!c4gO!w6SO#T#QO#z4dO#{4hO#|4iO#}4jO$O4kO$Q4mO$R4nO$S4oO$T4pO$U4qO$V4rO$W4rO$y#dO#O#Ua#P#Ua#Y#Ua#u#Ua#w#Ua!n#Ua&r#Ua!y#Ua!o#UaV#Ua!r#Ua~P!']OT4eO{4cO!T4fO!c4gO!w6SO#T#QO#z4dO#{4hO#|4iO#}4jO$O4kO$Q4mO$R4nO$S4oO$T4pO$U4qO$V4rO$W4rO$y#dO#O'Oa#P'Oa#Y'Oa#u'Oa#w'Oa!n'Oa&r'Oa!y'Oa!o'OaV'Oa!r'Oa~P!']O{4cO!w6SO#T#QO#z4dO#{4hO#|4iO#}4jO$O4kO$Q4mO$R4nO$S4oO$T4pO$U4qO$V4rO$W4rO$y#dOT#Qi!T#Qi!c#Qi#O#Qi#P#Qi#Y#Qi#u#Qi#w#Qi!n#Qi&r#Qi!y#Qi!o#QiV#Qi!r#Qi~P!']O{4cO!w6SO#T#QO#z4dO#{4hO#|4iO#}4jO$O4kO$Q4mO$R4nO$S4oO$T4pO$U4qO$V4rO$W4rO$y#dOT#vi!T#vi!c#vi#O#vi#P#vi#Y#vi#u#vi#w#vi!n#vi&r#vi!y#vi!o#viV#vi!r#vi~P!']OT4eO{4cO!T4fO!c4gO!w6SO#T#QO#z4dO#{4hO#|4iO#}4jO$O4kO$Q4mO$R4nO$S4oO$T4pO$U4qO$V4rO$W4rO$y#dO#O#xi#P#xi#Y#xi#u#xi#w#xi!n#xi&r#xi!y#xi!o#xiV#xi!r#xi~P!']O{4cO#T#QO#z4dO#{4hO#|4iO#}4jO$O4kO$Q4mO$R4nO$S4oO$T4pO$U4qO$V4rO$W4rO$y#dOT!vq!T!vq!c!vq!w!vq#O!vq#P!vq#Y!vq#u!vq#w!vq!n!vq&r!vq!y!vq!o!vqV!vq!r!vq~P!']O{4cO!w6SO#T#QO#z4dO#{4hO#|4iO#}4jO$O4kO$Q4mO$R4nO$S4oO$T4pO$U4qO$V4rO$W4rO$y#dOT#Qq!T#Qq!c#Qq#O#Qq#P#Qq#Y#Qq#u#Qq#w#Qq!n#Qq&r#Qq!y#Qq!o#QqV#Qq!r#Qq~P!']OT4eO{4cO!T4fO!c4gO!w6SO#T#QO#z4dO#{4hO#|4iO#}4jO$O4kO$Q4mO$R4nO$S4oO$T4pO$U4qO$V4rO$W4rO$y#dO#O$jq#P$jq#Y$jq#u$jq#w$jq!n$jq&r$jq!y$jq!o$jqV$jq!r$jq~P!']O{4cO#T#QO#z4dO#{4hO#|4iO#}4jO$O4kO$Q4mO$R4nO$S4oO$T4pO$U4qO$V4rO$W4rO$y#dOT!vy!T!vy!c!vy!w!vy#O!vy#P!vy#Y!vy#u!vy#w!vy!n!vy&r!vy!y!vy!o!vyV!vy!r!vy~P!']OT4eO{4cO!T4fO!c4gO!w6SO#T#QO#z4dO#{4hO#|4iO#}4jO$O4kO$Q4mO$R4nO$S4oO$T4pO$U4qO$V4rO$W4rO$y#dO#O$jy#P$jy#Y$jy#u$jy#w$jy!n$jy&r$jy!y$jy!o$jyV$jy!r$jy~P!']OT4eO{4cO!T4fO!c4gO!w6SO#T#QO#z4dO#{4hO#|4iO#}4jO$O4kO$Q4mO$R4nO$S4oO$T4pO$U4qO$V4rO$W4rO$y#dO#O$j!R#P$j!R#Y$j!R#u$j!R#w$j!R!n$j!R&r$j!R!y$j!R!o$j!RV$j!R!r$j!R~P!']OT4eO{4cO!T4fO!c4gO!w6SO#T#QO#z4dO#{4hO#|4iO#}4jO$O4kO$Q4mO$R4nO$S4oO$T4pO$U4qO$V4rO$W4rO$y#dO#O$j!Z#P$j!Z#Y$j!Z#u$j!Z#w$j!Z!n$j!Z&r$j!Z!y$j!Z!o$j!ZV$j!Z!r$j!Z~P!']OT4eO{4cO!T4fO!c4gO!w6SO#T#QO#z4dO#{4hO#|4iO#}4jO$O4kO$Q4mO$R4nO$S4oO$T4pO$U4qO$V4rO$W4rO$y#dO#O$j!c#P$j!c#Y$j!c#u$j!c#w$j!c!n$j!c&r$j!c!y$j!c!o$j!cV$j!c!r$j!c~P!']O#T5vO~P#._O!z$hO#T5zO~O!y4YO#m'RO~O!z$hO#T5{O~OT4eO{4cO!T4fO!c4gO!w6SO#T#QO#z4dO#{4hO#|4iO#}4jO$O4kO$Q4mO$R4nO$S4oO$T4pO$U4qO$V4rO$W4rO$y#dO#O$ka#P$ka#Y$ka#u$ka#w$ka!n$ka&r$ka!y$ka!o$kaV$ka!r$ka~P!']OT4eO{4cO!T4fO!c4gO!w6SO#P5uO#T#QO#z4dO#{4hO#|4iO#}4jO$O4kO$Q4mO$R4nO$S4oO$T4pO$U4qO$V4rO$W4rO$y#dO!n'OX#u'OX#w'OX&r'OX!y'OX!o'OX!r'OX#Y'OX#O'OX~P!']O#u4uO#w4vO#O&yX#P&yX#Y&yXV&yX!r&yX~P0rO!r5PO~P>ZO!r8aO#P5gO~OT8uO{8sO!T8vO!c8wO!r5hO!w=YO#T#QO#z8tO#{8xO#|8yO#}8zO$O8{O$Q8}O$R9OO$S9PO$T9QO$U9RO$V9SO$W9SO$y#dO~P!']O!r8bO#P5kO~O!r8cO#P5oO~O#P5oO#m'RO~O#P5pO#m'RO~O#P5sO#m'RO~O[$tO~P9yOp5yOt$lO~O#T7nO~P9yOT6hO{6fO!T6iO!c6jO!w8rO#T#QO#z6gO#{6kO#|6lO#}6mO$O6nO$Q6pO$R6qO$S6rO$T6sO$U6tO$V6uO$W6uO$y#dO#O$Xa#P$Xa#Y$Xa!n$Xa&r$Xa!y$Xa!o$XaV$Xa!r$Xa~P!']OT6hO{6fO!T6iO!c6jO!w8rO#T#QO#z6gO#{6kO#|6lO#}6mO$O6nO$Q6pO$R6qO$S6rO$T6sO$U6tO$V6uO$W6uO$y#dO#O$Ya#P$Ya#Y$Ya!n$Ya&r$Ya!y$Ya!o$YaV$Ya!r$Ya~P!']OT6hO{6fO!T6iO!c6jO!w8rO#T#QO#z6gO#{6kO#|6lO#}6mO$O6nO$Q6pO$R6qO$S6rO$T6sO$U6tO$V6uO$W6uO$y#dO#O$Za#P$Za#Y$Za!n$Za&r$Za!y$Za!o$ZaV$Za!r$Za~P!']OT6hO{6fO!T6iO!c6jO!w8rO#T#QO#z6gO#{6kO#|6lO#}6mO$O6nO$Q6pO$R6qO$S6rO$T6sO$U6tO$V6uO$W6uO$y#dO#O$[a#P$[a#Y$[a!n$[a&r$[a!y$[a!o$[aV$[a!r$[a~P!']O{6fO#O$[a#P$[a#Y$[aV$[a!r$[a~P! OOT6hO{6fO!T6iO!c6jO!w8rO#T#QO#z6gO#{6kO#|6lO#}6mO$O6nO$Q6pO$R6qO$S6rO$T6sO$U6tO$V6uO$W6uO$y#dO#O$_a#P$_a#Y$_a!n$_a&r$_a!y$_a!o$_aV$_a!r$_a~P!']OT6hO{6fO!T6iO!c6jO!w8rO#T#QO#z6gO#{6kO#|6lO#}6mO$O6nO$Q6pO$R6qO$S6rO$T6sO$U6tO$V6uO$W6uO$y#dO#O$ka#P$ka#Y$ka!n$ka&r$ka!y$ka!o$kaV$ka!r$ka~P!']OT6hO{6fO!T6iO!c6jO!w8rO#T#QO#z6gO#{6kO#|6lO#}6mO$O6nO$Q6pO$R6qO$S6rO$T6sO$U6tO$V6uO$W6uO$y#dO#O${a#P${a#Y${a!n${a&r${a!y${a!o${aV${a!r${a~P!']OT8uO{8sO!T8vO!c8wO!w=YO#O7rO#T#QO#z8tO#{8xO#|8yO#}8zO$O8{O$Q8}O$R9OO$S9PO$T9QO$U9RO$V9SO$W9SO$y#dO!y'iX~P!']OT8uO{8sO!T8vO!c8wO!w=YO#O7tO#T#QO#z8tO#{8xO#|8yO#}8zO$O8{O$Q8}O$R9OO$S9PO$T9QO$U9RO$V9SO$W9SO$y#dO!y&{X~P!']O{6fO#T#QO#z6gO#{6kO#|6lO#}6mO$O6nO$Q6pO$R6qO$S6rO$T6sO$U6tO$V6uO$W6uO$y#dOT#yi!T#yi!c#yi!w#yi#O#yi#P#yi#Y#yi!n#yi&r#yi!y#yi!o#yiV#yi!r#yi~P!']O{6fO!w8rO#T#QO#z6gO#{6kO#|6lO#}6mO$O6nO$Q6pO$R6qO$S6rO$T6sO$U6tO$V6uO$W6uO$y#dOT#yi!T#yi!c#yi#O#yi#P#yi#Y#yi!n#yi&r#yi!y#yi!o#yiV#yi!r#yi~P!']OT6hO{6fO!c6jO!w8rO#T#QO#z6gO#{6kO#|6lO#}6mO$O6nO$Q6pO$R6qO$S6rO$T6sO$U6tO$V6uO$W6uO$y#dO!T#yi#O#yi#P#yi#Y#yi!n#yi&r#yi!y#yi!o#yiV#yi!r#yi~P!']OT6hO{6fO!w8rO#T#QO#z6gO#{6kO#|6lO#}6mO$O6nO$Q6pO$R6qO$S6rO$T6sO$U6tO$V6uO$W6uO$y#dO!T#yi!c#yi#O#yi#P#yi#Y#yi!n#yi&r#yi!y#yi!o#yiV#yi!r#yi~P!']O{6fO#T#QO#|6lO#}6mO$O6nO$Q6pO$R6qO$S6rO$T6sO$U6tO$V6uO$W6uO$y#dOT#yi!T#yi!c#yi!w#yi#O#yi#P#yi#Y#yi#z#yi#{#yi!n#yi&r#yi!y#yi!o#yiV#yi!r#yi~P!']O{6fO#T#QO#}6mO$O6nO$Q6pO$R6qO$S6rO$T6sO$U6tO$V6uO$W6uO$y#dOT#yi!T#yi!c#yi!w#yi#O#yi#P#yi#Y#yi#z#yi#{#yi#|#yi!n#yi&r#yi!y#yi!o#yiV#yi!r#yi~P!']O{6fO#T#QO$O6nO$Q6pO$R6qO$S6rO$T6sO$U6tO$V6uO$W6uO$y#dOT#yi!T#yi!c#yi!w#yi#O#yi#P#yi#Y#yi#z#yi#{#yi#|#yi#}#yi!n#yi&r#yi!y#yi!o#yiV#yi!r#yi~P!']O{6fO#T#QO$Q6pO$R6qO$S6rO$T6sO$U6tO$V6uO$W6uO$y#dOT#yi!T#yi!c#yi!w#yi#O#yi#P#yi#Y#yi#z#yi#{#yi#|#yi#}#yi$O#yi!n#yi&r#yi!y#yi!o#yiV#yi!r#yi~P!']O{6fO$Q6pO$R6qO$S6rO$T6sO$U6tO$V6uO$W6uO$y#dOT#yi!T#yi!c#yi!w#yi#O#yi#P#yi#T#yi#Y#yi#z#yi#{#yi#|#yi#}#yi$O#yi!n#yi&r#yi!y#yi!o#yiV#yi!r#yi~P!']O{6fO$R6qO$S6rO$T6sO$U6tO$V6uO$W6uO$y#dOT#yi!T#yi!c#yi!w#yi#O#yi#P#yi#T#yi#Y#yi#z#yi#{#yi#|#yi#}#yi$O#yi$Q#yi!n#yi&r#yi!y#yi!o#yiV#yi!r#yi~P!']O{6fO$S6rO$T6sO$U6tO$V6uO$W6uO$y#dOT#yi!T#yi!c#yi!w#yi#O#yi#P#yi#T#yi#Y#yi#z#yi#{#yi#|#yi#}#yi$O#yi$Q#yi$R#yi!n#yi&r#yi!y#yi!o#yiV#yi!r#yi~P!']O{6fO$T6sO$V6uO$W6uO$y#dOT#yi!T#yi!c#yi!w#yi#O#yi#P#yi#T#yi#Y#yi#z#yi#{#yi#|#yi#}#yi$O#yi$Q#yi$R#yi$S#yi$U#yi!n#yi&r#yi!y#yi!o#yiV#yi!r#yi~P!']O{6fO$V6uO$W6uO$y#dOT#yi!T#yi!c#yi!w#yi#O#yi#P#yi#T#yi#Y#yi#z#yi#{#yi#|#yi#}#yi$O#yi$Q#yi$R#yi$S#yi$T#yi$U#yi!n#yi&r#yi!y#yi!o#yiV#yi!r#yi~P!']O{6fO$S6rO$T6sO$V6uO$W6uO$y#dOT#yi!T#yi!c#yi!w#yi#O#yi#P#yi#T#yi#Y#yi#z#yi#{#yi#|#yi#}#yi$O#yi$Q#yi$R#yi$U#yi!n#yi&r#yi!y#yi!o#yiV#yi!r#yi~P!']O{6fO$W6uO$y#dOT#yi!T#yi!c#yi!w#yi#O#yi#P#yi#T#yi#Y#yi#z#yi#{#yi#|#yi#}#yi$O#yi$Q#yi$R#yi$S#yi$T#yi$U#yi$V#yi!n#yi&r#yi!y#yi!o#yiV#yi!r#yi~P!']O#T7yO~P>ZO!n#Ua&r#Ua!y#Ua!o#Ua~PCvO!n'Oa&r'Oa!y'Oa!o'Oa~PCvO#T;cO#V;bO!y&VX#O&VX~P9yO#O7kO!y&}a~O{6fO!w8rO#T#QO#z6gO#{6kO#|6lO#}6mO$O6nO$Q6pO$R6qO$S6rO$T6sO$U6tO$V6uO$W6uO$y#dOT#Qi!T#Qi!c#Qi#O#Qi#P#Qi#Y#Qi!n#Qi&r#Qi!y#Qi!o#QiV#Qi!r#Qi~P!']O{6fO!w8rO#T#QO#z6gO#{6kO#|6lO#}6mO$O6nO$Q6pO$R6qO$S6rO$T6sO$U6tO$V6uO$W6uO$y#dOT#vi!T#vi!c#vi#O#vi#P#vi#Y#vi!n#vi&r#vi!y#vi!o#viV#vi!r#vi~P!']OT6hO{6fO!T6iO!c6jO!w8rO#T#QO#z6gO#{6kO#|6lO#}6mO$O6nO$Q6pO$R6qO$S6rO$T6sO$U6tO$V6uO$W6uO$y#dO#O#xi#P#xi#Y#xi!n#xi&r#xi!y#xi!o#xiV#xi!r#xi~P!']O#O7rO!y%ca~O!y&TX#O&TX~P>ZO#O7tO!y&{a~O{6fO#T#QO#z6gO#{6kO#|6lO#}6mO$O6nO$Q6pO$R6qO$S6rO$T6sO$U6tO$V6uO$W6uO$y#dOT!vq!T!vq!c!vq!w!vq#O!vq#P!vq#Y!vq!n!vq&r!vq!y!vq!o!vqV!vq!r!vq~P!']OT8uO{8sO!T8vO!c8wO!w=YO#T#QO#z8tO#{8xO#|8yO#}8zO$O8{O$Q8}O$R9OO$S9PO$T9QO$U9RO$V9SO$W9SO$y#dO!y#Wi#O#Wi~P!']O{6fO!w8rO#T#QO#z6gO#{6kO#|6lO#}6mO$O6nO$Q6pO$R6qO$S6rO$T6sO$U6tO$V6uO$W6uO$y#dOT#Qq!T#Qq!c#Qq#O#Qq#P#Qq#Y#Qq!n#Qq&r#Qq!y#Qq!o#QqV#Qq!r#Qq~P!']OT6hO{6fO!T6iO!c6jO!w8rO#T#QO#z6gO#{6kO#|6lO#}6mO$O6nO$Q6pO$R6qO$S6rO$T6sO$U6tO$V6uO$W6uO$y#dO#O$jq#P$jq#Y$jq!n$jq&r$jq!y$jq!o$jqV$jq!r$jq~P!']OT8uO{8sO!T8vO!c8wO!w=YO#T#QO#z8tO#{8xO#|8yO#}8zO$O8{O$Q8}O$R9OO$S9PO$T9QO$U9RO$V9SO$W9SO$y#dO!y&ja#O&ja~P!']OT8uO{8sO!T8vO!c8wO!w=YO#T#QO#z8tO#{8xO#|8yO#}8zO$O8{O$Q8}O$R9OO$S9PO$T9QO$U9RO$V9SO$W9SO$y#dO!y&Ta#O&Ta~P!']O{6fO#T#QO#z6gO#{6kO#|6lO#}6mO$O6nO$Q6pO$R6qO$S6rO$T6sO$U6tO$V6uO$W6uO$y#dOT!vy!T!vy!c!vy!w!vy#O!vy#P!vy#Y!vy!n!vy&r!vy!y!vy!o!vyV!vy!r!vy~P!']OT8uO{8sO!T8vO!c8wO!w=YO#T#QO#z8tO#{8xO#|8yO#}8zO$O8{O$Q8}O$R9OO$S9PO$T9QO$U9RO$V9SO$W9SO$y#dO!y#Wq#O#Wq~P!']OT6hO{6fO!T6iO!c6jO!w8rO#T#QO#z6gO#{6kO#|6lO#}6mO$O6nO$Q6pO$R6qO$S6rO$T6sO$U6tO$V6uO$W6uO$y#dO#O$jy#P$jy#Y$jy!n$jy&r$jy!y$jy!o$jyV$jy!r$jy~P!']OT6hO{6fO!T6iO!c6jO!w8rO#T#QO#z6gO#{6kO#|6lO#}6mO$O6nO$Q6pO$R6qO$S6rO$T6sO$U6tO$V6uO$W6uO$y#dO#O$j!R#P$j!R#Y$j!R!n$j!R&r$j!R!y$j!R!o$j!RV$j!R!r$j!R~P!']OT6hO{6fO!T6iO!c6jO!w8rO#T#QO#z6gO#{6kO#|6lO#}6mO$O6nO$Q6pO$R6qO$S6rO$T6sO$U6tO$V6uO$W6uO$y#dO#O$j!Z#P$j!Z#Y$j!Z!n$j!Z&r$j!Z!y$j!Z!o$j!ZV$j!Z!r$j!Z~P!']OT6hO{6fO!T6iO!c6jO!w8rO#T#QO#z6gO#{6kO#|6lO#}6mO$O6nO$Q6pO$R6qO$S6rO$T6sO$U6tO$V6uO$W6uO$y#dO#O$j!c#P$j!c#Y$j!c!n$j!c&r$j!c!y$j!c!o$j!cV$j!c!r$j!c~P!']O#T8ZO~P9yO#P8YO!n'OX&r'OX!y'OX!o'OXV'OX!r'OX~PGXO!z$hO#T8_O~O!z$hO#T8`O~O#u6yO#w6zO#O&yX#P&yX#Y&yXV&yX!r&yX~P0rOs6{O#T#oO#V#nO#O#xX#P#xX#Y#xXV#xX!r#xX~P2yOs;hO#T9WO#V9UOT#xX{#xX!T#xX!c#xX!n#xX!p#xX!r#xX!w#xX#a#xX#b#xX#t#xX#z#xX#{#xX#|#xX#}#xX$O#xX$Q#xX$R#xX$S#xX$U#xX$V#xX$W#xX!o#xX#O#xX~P9yOs9VO#T9VO#V9VOT#xX{#xX!T#xX!c#xX!p#xX!w#xX#a#xX#b#xX#t#xX#z#xX#{#xX#|#xX#}#xX$O#xX$Q#xX$R#xX$S#xX$U#xX$V#xX$W#xX~P9yOs9[O#T;cO#V;bOT#xX{#xX!T#xX!c#xX!p#xX!r#xX!w#xX#a#xX#b#xX#t#xX#z#xX#{#xX#|#xX#}#xX$O#xX$Q#xX$R#xX$S#xX$U#xX$V#xX$W#xX#Y#xX!y#xX#O#xX~P9yO[$tO~P>ZO!r7WO~P>ZOT6hO{6fO!T6iO!c6jO!w8rO#P7hO#T#QO#z6gO#{6kO#|6lO#}6mO$O6nO$Q6pO$R6qO$S6rO$T6sO$U6tO$V6uO$W6uO$y#dO!y'OX#O'OX~P!']OP6[OU^O]9VOp>ROt#hOy9VOz9VO!O`O!P]O!R:kO!U9VO!V9VO!W9VO!Z9VO!d8gO!t#gO!z[O#X_O#chO#eaO#fbO#qeO$T:hO$]9VO$^:hO$aqO$y:mO$z!OO~P$;qO#O7kO!y&}X~O#T9xO~P>ZOT8uO{8sO!T8vO!c8wO!w=YO#T#QO#z8tO#{8xO#|8yO#}8zO$O8{O$Q8}O$R9OO$S9PO$T9QO$U9RO$V9SO$W9SO$y#dO!r$Xa#Y$Xa!y$Xa#O$Xa~P!']OT8uO{8sO!T8vO!c8wO!w=YO#T#QO#z8tO#{8xO#|8yO#}8zO$O8{O$Q8}O$R9OO$S9PO$T9QO$U9RO$V9SO$W9SO$y#dO!r$Ya#Y$Ya!y$Ya#O$Ya~P!']OT8uO{8sO!T8vO!c8wO!w=YO#T#QO#z8tO#{8xO#|8yO#}8zO$O8{O$Q8}O$R9OO$S9PO$T9QO$U9RO$V9SO$W9SO$y#dO!r$Za#Y$Za!y$Za#O$Za~P!']OT8uO{8sO!T8vO!c8wO!w=YO#T#QO#z8tO#{8xO#|8yO#}8zO$O8{O$Q8}O$R9OO$S9PO$T9QO$U9RO$V9SO$W9SO$y#dO!r$[a#Y$[a!y$[a#O$[a~P!']O{8sO$y#dOT$[a!T$[a!c$[a!r$[a!w$[a#T$[a#z$[a#{$[a#|$[a#}$[a$O$[a$Q$[a$R$[a$S$[a$T$[a$U$[a$V$[a$W$[a#Y$[a!y$[a#O$[a~P!']OT8uO{8sO!T8vO!c8wO!w=YO#T#QO#z8tO#{8xO#|8yO#}8zO$O8{O$Q8}O$R9OO$S9PO$T9QO$U9RO$V9SO$W9SO$y#dO!r$_a#Y$_a!y$_a#O$_a~P!']O!r=cO#P7qO~OT8uO{8sO!T8vO!c8wO!w=YO#T#QO#z8tO#{8xO#|8yO#}8zO$O8{O$Q8}O$R9OO$S9PO$T9QO$U9RO$V9SO$W9SO$y#dO!r$ka#Y$ka!y$ka#O$ka~P!']OT8uO{8sO!T8vO!c8wO!w=YO#T#QO#z8tO#{8xO#|8yO#}8zO$O8{O$Q8}O$R9OO$S9PO$T9QO$U9RO$V9SO$W9SO$y#dO!r${a#Y${a!y${a#O${a~P!']OT8uO{8sO!T8vO!c8wO!r7vO!w=YO#T#QO#z8tO#{8xO#|8yO#}8zO$O8{O$Q8}O$R9OO$S9PO$T9QO$U9RO$V9SO$W9SO$y#dO~P!']O{8sO#T#QO#z8tO#{8xO#|8yO#}8zO$O8{O$Q8}O$R9OO$S9PO$T9QO$U9RO$V9SO$W9SO$y#dOT#yi!T#yi!c#yi!r#yi!w#yi#Y#yi!y#yi#O#yi~P!']O{8sO!w=YO#T#QO#z8tO#{8xO#|8yO#}8zO$O8{O$Q8}O$R9OO$S9PO$T9QO$U9RO$V9SO$W9SO$y#dOT#yi!T#yi!c#yi!r#yi#Y#yi!y#yi#O#yi~P!']OT8uO{8sO!c8wO!w=YO#T#QO#z8tO#{8xO#|8yO#}8zO$O8{O$Q8}O$R9OO$S9PO$T9QO$U9RO$V9SO$W9SO$y#dO!T#yi!r#yi#Y#yi!y#yi#O#yi~P!']OT8uO{8sO!w=YO#T#QO#z8tO#{8xO#|8yO#}8zO$O8{O$Q8}O$R9OO$S9PO$T9QO$U9RO$V9SO$W9SO$y#dO!T#yi!c#yi!r#yi#Y#yi!y#yi#O#yi~P!']O{8sO#T#QO#|8yO#}8zO$O8{O$Q8}O$R9OO$S9PO$T9QO$U9RO$V9SO$W9SO$y#dOT#yi!T#yi!c#yi!r#yi!w#yi#z#yi#{#yi#Y#yi!y#yi#O#yi~P!']O{8sO#T#QO#}8zO$O8{O$Q8}O$R9OO$S9PO$T9QO$U9RO$V9SO$W9SO$y#dOT#yi!T#yi!c#yi!r#yi!w#yi#z#yi#{#yi#|#yi#Y#yi!y#yi#O#yi~P!']O{8sO#T#QO$O8{O$Q8}O$R9OO$S9PO$T9QO$U9RO$V9SO$W9SO$y#dOT#yi!T#yi!c#yi!r#yi!w#yi#z#yi#{#yi#|#yi#}#yi#Y#yi!y#yi#O#yi~P!']O{8sO#T#QO$Q8}O$R9OO$S9PO$T9QO$U9RO$V9SO$W9SO$y#dOT#yi!T#yi!c#yi!r#yi!w#yi#z#yi#{#yi#|#yi#}#yi$O#yi#Y#yi!y#yi#O#yi~P!']O{8sO$Q8}O$R9OO$S9PO$T9QO$U9RO$V9SO$W9SO$y#dOT#yi!T#yi!c#yi!r#yi!w#yi#T#yi#z#yi#{#yi#|#yi#}#yi$O#yi#Y#yi!y#yi#O#yi~P!']O{8sO$R9OO$S9PO$T9QO$U9RO$V9SO$W9SO$y#dOT#yi!T#yi!c#yi!r#yi!w#yi#T#yi#z#yi#{#yi#|#yi#}#yi$O#yi$Q#yi#Y#yi!y#yi#O#yi~P!']O{8sO$S9PO$T9QO$U9RO$V9SO$W9SO$y#dOT#yi!T#yi!c#yi!r#yi!w#yi#T#yi#z#yi#{#yi#|#yi#}#yi$O#yi$Q#yi$R#yi#Y#yi!y#yi#O#yi~P!']O{8sO$T9QO$V9SO$W9SO$y#dOT#yi!T#yi!c#yi!r#yi!w#yi#T#yi#z#yi#{#yi#|#yi#}#yi$O#yi$Q#yi$R#yi$S#yi$U#yi#Y#yi!y#yi#O#yi~P!']O{8sO$V9SO$W9SO$y#dOT#yi!T#yi!c#yi!r#yi!w#yi#T#yi#z#yi#{#yi#|#yi#}#yi$O#yi$Q#yi$R#yi$S#yi$T#yi$U#yi#Y#yi!y#yi#O#yi~P!']O{8sO$S9PO$T9QO$V9SO$W9SO$y#dOT#yi!T#yi!c#yi!r#yi!w#yi#T#yi#z#yi#{#yi#|#yi#}#yi$O#yi$Q#yi$R#yi$U#yi#Y#yi!y#yi#O#yi~P!']O{8sO$W9SO$y#dOT#yi!T#yi!c#yi!r#yi!w#yi#T#yi#z#yi#{#yi#|#yi#}#yi$O#yi$Q#yi$R#yi$S#yi$T#yi$U#yi$V#yi#Y#yi!y#yi#O#yi~P!']O{8sO!w=YO#T#QO#z8tO#{8xO#|8yO#}8zO$O8{O$Q8}O$R9OO$S9PO$T9QO$U9RO$V9SO$W9SO$y#dOT#Qi!T#Qi!c#Qi!r#Qi#Y#Qi!y#Qi#O#Qi~P!']O{8sO!w=YO#T#QO#z8tO#{8xO#|8yO#}8zO$O8{O$Q8}O$R9OO$S9PO$T9QO$U9RO$V9SO$W9SO$y#dOT#vi!T#vi!c#vi!r#vi#Y#vi!y#vi#O#vi~P!']OT8uO{8sO!T8vO!c8wO!w=YO#T#QO#z8tO#{8xO#|8yO#}8zO$O8{O$Q8}O$R9OO$S9PO$T9QO$U9RO$V9SO$W9SO$y#dO!r#xi#Y#xi!y#xi#O#xi~P!']O!r=dO#P7{O~O{8sO#T#QO#z8tO#{8xO#|8yO#}8zO$O8{O$Q8}O$R9OO$S9PO$T9QO$U9RO$V9SO$W9SO$y#dOT!vq!T!vq!c!vq!r!vq!w!vq#Y!vq!y!vq#O!vq~P!']O{8sO!w=YO#T#QO#z8tO#{8xO#|8yO#}8zO$O8{O$Q8}O$R9OO$S9PO$T9QO$U9RO$V9SO$W9SO$y#dOT#Qq!T#Qq!c#Qq!r#Qq#Y#Qq!y#Qq#O#Qq~P!']O!r=hO#P8SO~OT8uO{8sO!T8vO!c8wO!w=YO#T#QO#z8tO#{8xO#|8yO#}8zO$O8{O$Q8}O$R9OO$S9PO$T9QO$U9RO$V9SO$W9SO$y#dO!r$jq#Y$jq!y$jq#O$jq~P!']O#P8SO#m'RO~O{8sO#T#QO#z8tO#{8xO#|8yO#}8zO$O8{O$Q8}O$R9OO$S9PO$T9QO$U9RO$V9SO$W9SO$y#dOT!vy!T!vy!c!vy!r!vy!w!vy#Y!vy!y!vy#O!vy~P!']OT8uO{8sO!T8vO!c8wO!w=YO#T#QO#z8tO#{8xO#|8yO#}8zO$O8{O$Q8}O$R9OO$S9PO$T9QO$U9RO$V9SO$W9SO$y#dO!r$jy#Y$jy!y$jy#O$jy~P!']O#P8TO#m'RO~OT8uO{8sO!T8vO!c8wO!w=YO#T#QO#z8tO#{8xO#|8yO#}8zO$O8{O$Q8}O$R9OO$S9PO$T9QO$U9RO$V9SO$W9SO$y#dO!r$j!R#Y$j!R!y$j!R#O$j!R~P!']O#P8WO#m'RO~OT8uO{8sO!T8vO!c8wO!w=YO#T#QO#z8tO#{8xO#|8yO#}8zO$O8{O$Q8}O$R9OO$S9PO$T9QO$U9RO$V9SO$W9SO$y#dO!r$j!Z#Y$j!Z!y$j!Z#O$j!Z~P!']OT8uO{8sO!T8vO!c8wO!w=YO#T#QO#z8tO#{8xO#|8yO#}8zO$O8{O$Q8}O$R9OO$S9PO$T9QO$U9RO$V9SO$W9SO$y#dO!r$j!c#Y$j!c!y$j!c#O$j!c~P!']O#T:aO~P>ZO#P:`O!r'OX!y'OX~PGXO[$tO~P$8_OP6[OU^O[$tO]9VOp>ROt#hOy9VOz9VO!O`O!P]O!R:kO!U9VO!V9VO!W9VO!Z9VO!d8gO!t#gO!z[O#X_O#chO#eaO#fbO#qeO$T:hO$]9VO$^:hO$aqO$y:mO$z!OO~P$;qOp8^Ot$lO~O#T<iO~P$8_OP6[OU^O]9VOp>ROt#hOy9VOz9VO!O`O!P]O!R:kO!U9VO!V9VO!W9VO!Z9VO!d8gO!t#gO!z[O#T<jO#X_O#chO#eaO#fbO#qeO$T:hO$]9VO$^:hO$aqO$y:mO$z!OO~P$;qOT:sO{:oO!T:uO!c:wO!w=lO#T#QO#z:qO#{:yO#|:{O#}:}O$O;PO$Q;TO$R;VO$S;XO$T;ZO$U;]O$V;_O$W;_O$y#dO!n$Xa!r$Xa!o$Xa#O$Xa~P!']OT:sO{:oO!T:uO!c:wO!w=lO#T#QO#z:qO#{:yO#|:{O#}:}O$O;PO$Q;TO$R;VO$S;XO$T;ZO$U;]O$V;_O$W;_O$y#dO!n$Ya!r$Ya!o$Ya#O$Ya~P!']OT:sO{:oO!T:uO!c:wO!w=lO#T#QO#z:qO#{:yO#|:{O#}:}O$O;PO$Q;TO$R;VO$S;XO$T;ZO$U;]O$V;_O$W;_O$y#dO!n$Za!r$Za!o$Za#O$Za~P!']OT:sO{:oO!T:uO!c:wO!w=lO#T#QO#z:qO#{:yO#|:{O#}:}O$O;PO$Q;TO$R;VO$S;XO$T;ZO$U;]O$V;_O$W;_O$y#dO!n$[a!r$[a!o$[a#O$[a~P!']O{:oO$y#dOT$[a!T$[a!c$[a!n$[a!r$[a!w$[a#T$[a#z$[a#{$[a#|$[a#}$[a$O$[a$Q$[a$R$[a$S$[a$T$[a$U$[a$V$[a$W$[a!o$[a#O$[a~P!']O{:pO$y#dOT$[a!T$[a!c$[a!w$[a#T$[a#z$[a#{$[a#|$[a#}$[a$O$[a$Q$[a$R$[a$S$[a$T$[a$U$[a$V$[a$W$[a~P!']OT:sO{:oO!T:uO!c:wO!w=lO#T#QO#z:qO#{:yO#|:{O#}:}O$O;PO$Q;TO$R;VO$S;XO$T;ZO$U;]O$V;_O$W;_O$y#dO!n$_a!r$_a!o$_a#O$_a~P!']OT:sO{:oO!T:uO!c:wO!w=lO#T#QO#z:qO#{:yO#|:{O#}:}O$O;PO$Q;TO$R;VO$S;XO$T;ZO$U;]O$V;_O$W;_O$y#dO!n$ka!r$ka!o$ka#O$ka~P!']OT:sO{:oO!T:uO!c:wO!w=lO#T#QO#z:qO#{:yO#|:{O#}:}O$O;PO$Q;TO$R;VO$S;XO$T;ZO$U;]O$V;_O$W;_O$y#dO!n${a!r${a!o${a#O${a~P!']O{:oO#T#QO#z:qO#{:yO#|:{O#}:}O$O;PO$Q;TO$R;VO$S;XO$T;ZO$U;]O$V;_O$W;_O$y#dOT#yi!T#yi!c#yi!n#yi!r#yi!w#yi!o#yi#O#yi~P!']O{:pO#T#QO#z:rO#{:zO#|:|O#};OO$O;QO$Q;UO$R;WO$S;YO$T;[O$U;^O$V;`O$W;`O$y#dOT#yi!T#yi!c#yi!w#yi~P!']O{:oO!w=lO#T#QO#z:qO#{:yO#|:{O#}:}O$O;PO$Q;TO$R;VO$S;XO$T;ZO$U;]O$V;_O$W;_O$y#dOT#yi!T#yi!c#yi!n#yi!r#yi!o#yi#O#yi~P!']O{:pO!w=mO#T#QO#z:rO#{:zO#|:|O#};OO$O;QO$Q;UO$R;WO$S;YO$T;[O$U;^O$V;`O$W;`O$y#dOT#yi!T#yi!c#yi~P!']OT:sO{:oO!c:wO!w=lO#T#QO#z:qO#{:yO#|:{O#}:}O$O;PO$Q;TO$R;VO$S;XO$T;ZO$U;]O$V;_O$W;_O$y#dO!T#yi!n#yi!r#yi!o#yi#O#yi~P!']OT:tO{:pO!c:xO!w=mO#T#QO#z:rO#{:zO#|:|O#};OO$O;QO$Q;UO$R;WO$S;YO$T;[O$U;^O$V;`O$W;`O$y#dO!T#yi~P!']OT:sO{:oO!w=lO#T#QO#z:qO#{:yO#|:{O#}:}O$O;PO$Q;TO$R;VO$S;XO$T;ZO$U;]O$V;_O$W;_O$y#dO!T#yi!c#yi!n#yi!r#yi!o#yi#O#yi~P!']OT:tO{:pO!w=mO#T#QO#z:rO#{:zO#|:|O#};OO$O;QO$Q;UO$R;WO$S;YO$T;[O$U;^O$V;`O$W;`O$y#dO!T#yi!c#yi~P!']O{:oO#T#QO#|:{O#}:}O$O;PO$Q;TO$R;VO$S;XO$T;ZO$U;]O$V;_O$W;_O$y#dOT#yi!T#yi!c#yi!n#yi!r#yi!w#yi#z#yi#{#yi!o#yi#O#yi~P!']O{:pO#T#QO#|:|O#};OO$O;QO$Q;UO$R;WO$S;YO$T;[O$U;^O$V;`O$W;`O$y#dOT#yi!T#yi!c#yi!w#yi#z#yi#{#yi~P!']O{:oO#T#QO#}:}O$O;PO$Q;TO$R;VO$S;XO$T;ZO$U;]O$V;_O$W;_O$y#dOT#yi!T#yi!c#yi!n#yi!r#yi!w#yi#z#yi#{#yi#|#yi!o#yi#O#yi~P!']O{:pO#T#QO#};OO$O;QO$Q;UO$R;WO$S;YO$T;[O$U;^O$V;`O$W;`O$y#dOT#yi!T#yi!c#yi!w#yi#z#yi#{#yi#|#yi~P!']O{:oO#T#QO$O;PO$Q;TO$R;VO$S;XO$T;ZO$U;]O$V;_O$W;_O$y#dOT#yi!T#yi!c#yi!n#yi!r#yi!w#yi#z#yi#{#yi#|#yi#}#yi!o#yi#O#yi~P!']O{:pO#T#QO$O;QO$Q;UO$R;WO$S;YO$T;[O$U;^O$V;`O$W;`O$y#dOT#yi!T#yi!c#yi!w#yi#z#yi#{#yi#|#yi#}#yi~P!']O{:oO#T#QO$Q;TO$R;VO$S;XO$T;ZO$U;]O$V;_O$W;_O$y#dOT#yi!T#yi!c#yi!n#yi!r#yi!w#yi#z#yi#{#yi#|#yi#}#yi$O#yi!o#yi#O#yi~P!']O{:pO#T#QO$Q;UO$R;WO$S;YO$T;[O$U;^O$V;`O$W;`O$y#dOT#yi!T#yi!c#yi!w#yi#z#yi#{#yi#|#yi#}#yi$O#yi~P!']O{:oO$Q;TO$R;VO$S;XO$T;ZO$U;]O$V;_O$W;_O$y#dOT#yi!T#yi!c#yi!n#yi!r#yi!w#yi#T#yi#z#yi#{#yi#|#yi#}#yi$O#yi!o#yi#O#yi~P!']O{:pO$Q;UO$R;WO$S;YO$T;[O$U;^O$V;`O$W;`O$y#dOT#yi!T#yi!c#yi!w#yi#T#yi#z#yi#{#yi#|#yi#}#yi$O#yi~P!']O{:oO$R;VO$S;XO$T;ZO$U;]O$V;_O$W;_O$y#dOT#yi!T#yi!c#yi!n#yi!r#yi!w#yi#T#yi#z#yi#{#yi#|#yi#}#yi$O#yi$Q#yi!o#yi#O#yi~P!']O{:pO$R;WO$S;YO$T;[O$U;^O$V;`O$W;`O$y#dOT#yi!T#yi!c#yi!w#yi#T#yi#z#yi#{#yi#|#yi#}#yi$O#yi$Q#yi~P!']O{:oO$S;XO$T;ZO$U;]O$V;_O$W;_O$y#dOT#yi!T#yi!c#yi!n#yi!r#yi!w#yi#T#yi#z#yi#{#yi#|#yi#}#yi$O#yi$Q#yi$R#yi!o#yi#O#yi~P!']O{:pO$S;YO$T;[O$U;^O$V;`O$W;`O$y#dOT#yi!T#yi!c#yi!w#yi#T#yi#z#yi#{#yi#|#yi#}#yi$O#yi$Q#yi$R#yi~P!']O{:oO$T;ZO$V;_O$W;_O$y#dOT#yi!T#yi!c#yi!n#yi!r#yi!w#yi#T#yi#z#yi#{#yi#|#yi#}#yi$O#yi$Q#yi$R#yi$S#yi$U#yi!o#yi#O#yi~P!']O{:pO$T;[O$V;`O$W;`O$y#dOT#yi!T#yi!c#yi!w#yi#T#yi#z#yi#{#yi#|#yi#}#yi$O#yi$Q#yi$R#yi$S#yi$U#yi~P!']O{:oO$V;_O$W;_O$y#dOT#yi!T#yi!c#yi!n#yi!r#yi!w#yi#T#yi#z#yi#{#yi#|#yi#}#yi$O#yi$Q#yi$R#yi$S#yi$T#yi$U#yi!o#yi#O#yi~P!']O{:pO$V;`O$W;`O$y#dOT#yi!T#yi!c#yi!w#yi#T#yi#z#yi#{#yi#|#yi#}#yi$O#yi$Q#yi$R#yi$S#yi$T#yi$U#yi~P!']O{:oO$S;XO$T;ZO$V;_O$W;_O$y#dOT#yi!T#yi!c#yi!n#yi!r#yi!w#yi#T#yi#z#yi#{#yi#|#yi#}#yi$O#yi$Q#yi$R#yi$U#yi!o#yi#O#yi~P!']O{:pO$S;YO$T;[O$V;`O$W;`O$y#dOT#yi!T#yi!c#yi!w#yi#T#yi#z#yi#{#yi#|#yi#}#yi$O#yi$Q#yi$R#yi$U#yi~P!']O{:oO$W;_O$y#dOT#yi!T#yi!c#yi!n#yi!r#yi!w#yi#T#yi#z#yi#{#yi#|#yi#}#yi$O#yi$Q#yi$R#yi$S#yi$T#yi$U#yi$V#yi!o#yi#O#yi~P!']O{:pO$W;`O$y#dOT#yi!T#yi!c#yi!w#yi#T#yi#z#yi#{#yi#|#yi#}#yi$O#yi$Q#yi$R#yi$S#yi$T#yi$U#yi$V#yi~P!']OT8uO{8sO!T8vO!c8wO!w=YO#T#QO#z8tO#{8xO#|8yO#}8zO$O8{O$Q8}O$R9OO$S9PO$T9QO$U9RO$V9SO$W9SO$y#dO!y#Ua#O#Ua!r#Ua#Y#Ua~P!']OT8uO{8sO!T8vO!c8wO!w=YO#T#QO#z8tO#{8xO#|8yO#}8zO$O8{O$Q8}O$R9OO$S9PO$T9QO$U9RO$V9SO$W9SO$y#dO!y'Oa#O'Oa!r'Oa#Y'Oa~P!']O{:oO!w=lO#T#QO#z:qO#{:yO#|:{O#}:}O$O;PO$Q;TO$R;VO$S;XO$T;ZO$U;]O$V;_O$W;_O$y#dOT#Qi!T#Qi!c#Qi!n#Qi!r#Qi!o#Qi#O#Qi~P!']O{:pO!w=mO#T#QO#z:rO#{:zO#|:|O#};OO$O;QO$Q;UO$R;WO$S;YO$T;[O$U;^O$V;`O$W;`O$y#dOT#Qi!T#Qi!c#Qi~P!']O{:oO!w=lO#T#QO#z:qO#{:yO#|:{O#}:}O$O;PO$Q;TO$R;VO$S;XO$T;ZO$U;]O$V;_O$W;_O$y#dOT#vi!T#vi!c#vi!n#vi!r#vi!o#vi#O#vi~P!']O{:pO!w=mO#T#QO#z:rO#{:zO#|:|O#};OO$O;QO$Q;UO$R;WO$S;YO$T;[O$U;^O$V;`O$W;`O$y#dOT#vi!T#vi!c#vi~P!']OT:sO{:oO!T:uO!c:wO!w=lO#T#QO#z:qO#{:yO#|:{O#}:}O$O;PO$Q;TO$R;VO$S;XO$T;ZO$U;]O$V;_O$W;_O$y#dO!n#xi!r#xi!o#xi#O#xi~P!']O{:oO#T#QO#z:qO#{:yO#|:{O#}:}O$O;PO$Q;TO$R;VO$S;XO$T;ZO$U;]O$V;_O$W;_O$y#dOT!vq!T!vq!c!vq!n!vq!r!vq!w!vq!o!vq#O!vq~P!']O{:pO#T#QO#z:rO#{:zO#|:|O#};OO$O;QO$Q;UO$R;WO$S;YO$T;[O$U;^O$V;`O$W;`O$y#dOT!vq!T!vq!c!vq!w!vq~P!']O{:oO!w=lO#T#QO#z:qO#{:yO#|:{O#}:}O$O;PO$Q;TO$R;VO$S;XO$T;ZO$U;]O$V;_O$W;_O$y#dOT#Qq!T#Qq!c#Qq!n#Qq!r#Qq!o#Qq#O#Qq~P!']O{:pO!w=mO#T#QO#z:rO#{:zO#|:|O#};OO$O;QO$Q;UO$R;WO$S;YO$T;[O$U;^O$V;`O$W;`O$y#dOT#Qq!T#Qq!c#Qq~P!']OT:sO{:oO!T:uO!c:wO!w=lO#T#QO#z:qO#{:yO#|:{O#}:}O$O;PO$Q;TO$R;VO$S;XO$T;ZO$U;]O$V;_O$W;_O$y#dO!n$jq!r$jq!o$jq#O$jq~P!']O{:oO#T#QO#z:qO#{:yO#|:{O#}:}O$O;PO$Q;TO$R;VO$S;XO$T;ZO$U;]O$V;_O$W;_O$y#dOT!vy!T!vy!c!vy!n!vy!r!vy!w!vy!o!vy#O!vy~P!']O{:pO#T#QO#z:rO#{:zO#|:|O#};OO$O;QO$Q;UO$R;WO$S;YO$T;[O$U;^O$V;`O$W;`O$y#dOT!vy!T!vy!c!vy!w!vy~P!']OT:sO{:oO!T:uO!c:wO!w=lO#T#QO#z:qO#{:yO#|:{O#}:}O$O;PO$Q;TO$R;VO$S;XO$T;ZO$U;]O$V;_O$W;_O$y#dO!n$jy!r$jy!o$jy#O$jy~P!']OT:sO{:oO!T:uO!c:wO!w=lO#T#QO#z:qO#{:yO#|:{O#}:}O$O;PO$Q;TO$R;VO$S;XO$T;ZO$U;]O$V;_O$W;_O$y#dO!n$j!R!r$j!R!o$j!R#O$j!R~P!']OT:sO{:oO!T:uO!c:wO!w=lO#T#QO#z:qO#{:yO#|:{O#}:}O$O;PO$Q;TO$R;VO$S;XO$T;ZO$U;]O$V;_O$W;_O$y#dO!n$j!Z!r$j!Z!o$j!Z#O$j!Z~P!']OT:sO{:oO!T:uO!c:wO!w=lO#T#QO#z:qO#{:yO#|:{O#}:}O$O;PO$Q;TO$R;VO$S;XO$T;ZO$U;]O$V;_O$W;_O$y#dO!n$j!c!r$j!c!o$j!c#O$j!c~P!']O#T=SO~P$8_OP6[OU^O]9VOp>ROt#hOy9VOz9VO!O`O!P]O!R:kO!U9VO!V9VO!W9VO!Z9VO!d8gO!t#gO!z[O#T=TO#X_O#chO#eaO#fbO#qeO$T:hO$]9VO$^:hO$aqO$y:mO$z!OO~P$;qOT6hO{6fO!T6iO!c6jO!w8rO#P=RO#T#QO#z6gO#{6kO#|6lO#}6mO$O6nO$Q6pO$R6qO$S6rO$T6sO$U6tO$V6uO$W6uO$y#dO~P!']OT6hO{6fO!T6iO!c6jO!w8rO#P=QO#T#QO#z6gO#{6kO#|6lO#}6mO$O6nO$Q6pO$R6qO$S6rO$T6sO$U6tO$V6uO$W6uO$y#dO!n'OX!r'OX!o'OX#O'OX~P!']OT&yX{&yX!T&yX!c&yX!p&yX!r&yX!w&yX!z&yX#T&yX#X&yX#a&yX#b&yX#t&yX#z&yX#{&yX#|&yX#}&yX$O&yX$Q&yX$R&yX$S&yX$T&yX$U&yX$V&yX$W&yX$y&yX#O&yX~O#u9YO#w9ZO#Y&yX!y&yX~P.8tO!z$hO#T=]O~O!r9gO~P>ZO!z$hO#T=bO~O!r=}O#P9|O~OT8uO{8sO!T8vO!c8wO!r9}O!w=YO#T#QO#z8tO#{8xO#|8yO#}8zO$O8{O$Q8}O$R9OO$S9PO$T9QO$U9RO$V9SO$W9SO$y#dO~P!']OT:sO{:oO!T:uO!c:wO!w=lO#T#QO#z:qO#{:yO#|:{O#}:}O$O;PO$Q;TO$R;VO$S;XO$T;ZO$U;]O$V;_O$W;_O$y#dO!n#Ua!r#Ua!o#Ua#O#Ua~P!']OT:sO{:oO!T:uO!c:wO!w=lO#T#QO#z:qO#{:yO#|:{O#}:}O$O;PO$Q;TO$R;VO$S;XO$T;ZO$U;]O$V;_O$W;_O$y#dO!n'Oa!r'Oa!o'Oa#O'Oa~P!']O!r>OO#P:QO~O!r>PO#P:XO~O#P:XO#m'RO~O#P:YO#m'RO~O#P:^O#m'RO~O#u;dO#w;fO!n&yX!o&yX~P.8tO#u;eO#w;gOT&yX{&yX!T&yX!c&yX!p&yX!w&yX!z&yX#T&yX#X&yX#a&yX#b&yX#t&yX#z&yX#{&yX#|&yX#}&yX$O&yX$Q&yX$R&yX$S&yX$T&yX$U&yX$V&yX$W&yX$y&yX~O!r;sO~P>ZO!r;tO~P>ZO!r>WO#P<nO~O!r>XO#P9VO~OT8uO{8sO!T8vO!c8wO!r<oO!w=YO#T#QO#z8tO#{8xO#|8yO#}8zO$O8{O$Q8}O$R9OO$S9PO$T9QO$U9RO$V9SO$W9SO$y#dO~P!']OT8uO{8sO!T8vO!c8wO!r<pO!w=YO#T#QO#z8tO#{8xO#|8yO#}8zO$O8{O$Q8}O$R9OO$S9PO$T9QO$U9RO$V9SO$W9SO$y#dO~P!']O!r>YO#P<uO~O!r>ZO#P<zO~O#P<zO#m'RO~O#P9VO#m'RO~O#P<{O#m'RO~O#P=OO#m'RO~O!z$hO#T={O~Op=ZOt$lO~O!z$hO#T=|O~O!z$hO#T>TO~O!z$hO#T>UO~O!z$hO#T>VO~Op=zOt$lO~Op>SOt$lO~Op>ROt$lO~O$}$U$|$d!e$V#c%U#f'f!t#e~",
    goto: "%&u'lPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPPP'mP'tPP'z'}PPP(gP'}P'}*YP*YPP2V:i:lPP*Y:rBoPBrPBrPP:rCRCUCY:r:rPPPC]PP:rK]!$R!$R:r!$VP!$V!$V!%TP!.[!7oPP*YP*Y*YPPPPP!?nPPPPPPP*Y*Y*Y*YPP*Y*YP!EX!F}P!GR!Gu!F}!F}!G{*Y*Y!HU!Hh!I^!J[!J`!J[!Jk!Jy!Jy!KR!KU!KU*YPP*YPP!KY#%W#%W#%[P#%bP'}#%f'}#&O#&R#&R#&X'}#&['}'}#&b#&e'}#&n#&q'}'}'}'}'}#&t'}'}'}'}'}'}'}'}'}#&w!J}'}'}#'Z#'k#'n'}'}P#'q#'x#(O#(k#(u#({#)V#)^#)d#*`#4T#5P#5V#5]#5g#5m#5s#6X#6_#6e#6k#6q#6w#6}#7X#7c#7i#7o#7yPPPPPPPP#8P#8T#8y#Mz#M}#NX$(b$(n$)T$)Z$)^$)a$)g$,T$5r$>Z$>^$>d$>g$>j$>s$>w$?T$?g$Bg$Bz$Cw$KwPP%%u%%y%&V%&l%&rQ!nQT!qV!rQUOR%x!mRVO}!jPVX!S!l!r!s!w$}%P%S%U(_+q+t.a.c.k0_0`0h1`|!jPVX!S!l!r!s!w$}%P%S%U(_+q+t.a.c.k0_0`0h1`Q%^!ZQ%g!aQ%m!gQ'c$dQ'p$iQ)^%lQ*x'sQ,[(wU-m*u*w*}Q.V+bQ.z,ZS/s-r-sQ0S.RS0|/r/vQ1U0QQ1n0}R2O1o0u!OPVX[_bjklmnopxyz!S!W!X!Y!]!i!l!r!s!w!y!z!{!}#R#S#T#U#V#W#X#Y#Z#[#]#^#_#`#a#b#k#n#o#s#t$R$S$U$y$}%P%R%S%T%U%c%}&S&W&o&r&s&v&}'T'X'y'}(_(k(z)O)k)o)s)u*O*S*T*n+O+q+t+y,S,U,W-P-Q-c-j-y.a.c.k.s/b/h/l/w0U0_0`0c0d0h0u1Q1[1`2Z2[2]2^2_2`2a2b2c2d2e2f2g2h2i2j2k2l2m2n2o2r2s2t2u2v3O3c3f3g3j3n3o3r3t3u3w3x3y3z3{3|3}4O4P4Q4R4S4T4U4V4Y4`4a4b4c4d4e4f4g4h4i4j4k4l4m4n4o4p4q4r4s4t4u4v4w5P5d5g5h5k5o5p5s5u5v6S6]6^6_6`6a6b6c6d6e6f6g6h6i6j6k6l6m6n6o6p6q6r6s6t6u6w6x6y6z6{7W7h7k7n7q7r7t7v7y7{8S8T8W8Y8Z8e8f8g8h8i8j8k8l8m8n8o8p8q8r8s8t8u8v8w8x8y8z8{8|8}9O9P9Q9R9S9U9V9W9Y9Z9[9g9x9|9}:Q:X:Y:^:`:a:c:d:e:f:g:h:i:j:k:l:m:n:o:p:q:r:s:t:u:v:w:x:y:z:{:|:};O;P;Q;R;S;T;U;V;W;X;Y;Z;[;];^;_;`;b;c;d;e;f;g;h;s;t<i<j<n<o<p<u<z<{=O=Q=R=S=T=Y=l=m0t!OPVX[_bjklmnopxyz!S!W!X!Y!]!i!l!r!s!w!y!z!{!}#R#S#T#U#V#W#X#Y#Z#[#]#^#_#`#a#b#k#n#o#s#t$R$S$U$y$}%P%R%S%T%U%c%}&S&W&o&r&s&v&}'T'X'y'}(_(k(z)O)k)o)s)u*O*S*T*n+O+q+t+y,S,U,W-P-Q-c-j-y.a.c.k.s/b/h/l/w0U0_0`0c0d0h0u1Q1[1`2Z2[2]2^2_2`2a2b2c2d2e2f2g2h2i2j2k2l2m2n2o2r2s2t2u2v3O3c3f3g3j3n3o3r3t3u3w3x3y3z3{3|3}4O4P4Q4R4S4T4U4V4Y4`4a4b4c4d4e4f4g4h4i4j4k4l4m4n4o4p4q4r4s4t4u4v4w5P5d5g5h5k5o5p5s5u5v6S6]6^6_6`6a6b6c6d6e6f6g6h6i6j6k6l6m6n6o6p6q6r6s6t6u6w6x6y6z6{7W7h7k7n7q7r7t7v7y7{8S8T8W8Y8Z8e8f8g8h8i8j8k8l8m8n8o8p8q8r8s8t8u8v8w8x8y8z8{8|8}9O9P9Q9R9S9U9V9W9Y9Z9[9g9x9|9}:Q:X:Y:^:`:a:c:d:e:f:g:h:i:j:k:l:m:n:o:p:q:r:s:t:u:v:w:x:y:z:{:|:};O;P;Q;R;S;T;U;V;W;X;Y;Z;[;];^;_;`;b;c;d;e;f;g;h;s;t<i<j<n<o<p<u<z<{=O=Q=R=S=T=Y=l=mQ#j]Q$}!PQ%O!QQ%P!RQ,R(jQ.a+rR.e+uR&p#jQ)y&oR/`-Q0uhPVX[_bjklmnopxyz!S!W!X!Y!]!i!l!r!s!w!y!z!{!}#R#S#T#U#V#W#X#Y#Z#[#]#^#_#`#a#b#k#n#o#s#t$R$S$U$y$}%P%R%S%T%U%c%}&S&W&o&r&s&v&}'T'X'y'}(_(k(z)O)k)o)s)u*O*S*T*n+O+q+t+y,S,U,W-P-Q-c-j-y.a.c.k.s/b/h/l/w0U0_0`0c0d0h0u1Q1[1`2Z2[2]2^2_2`2a2b2c2d2e2f2g2h2i2j2k2l2m2n2o2r2s2t2u2v3O3c3f3g3j3n3o3r3t3u3w3x3y3z3{3|3}4O4P4Q4R4S4T4U4V4Y4`4a4b4c4d4e4f4g4h4i4j4k4l4m4n4o4p4q4r4s4t4u4v4w5P5d5g5h5k5o5p5s5u5v6S6]6^6_6`6a6b6c6d6e6f6g6h6i6j6k6l6m6n6o6p6q6r6s6t6u6w6x6y6z6{7W7h7k7n7q7r7t7v7y7{8S8T8W8Y8Z8e8f8g8h8i8j8k8l8m8n8o8p8q8r8s8t8u8v8w8x8y8z8{8|8}9O9P9Q9R9S9U9V9W9Y9Z9[9g9x9|9}:Q:X:Y:^:`:a:c:d:e:f:g:h:i:j:k:l:m:n:o:p:q:r:s:t:u:v:w:x:y:z:{:|:};O;P;Q;R;S;T;U;V;W;X;Y;Z;[;];^;_;`;b;c;d;e;f;g;h;s;t<i<j<n<o<p<u<z<{=O=Q=R=S=T=Y=l=mR#l^k#p_j#k#s&r&v3w3x7k8e8f8g8hR#u`T&{#t&}R-X*T0thPVX[_bjklmnopxyz!S!W!X!Y!]!i!l!r!s!w!y!z!{!}#R#S#T#U#V#W#X#Y#Z#[#]#^#_#`#a#b#k#n#o#s#t$R$S$U$y$}%P%R%S%T%U%c%}&S&W&o&r&s&v&}'T'X'y'}(_(k(z)O)k)o)s)u*O*S*T*n+O+q+t+y,S,U,W-P-Q-c-j-y.a.c.k.s/b/h/l/w0U0_0`0c0d0h0u1Q1[1`2Z2[2]2^2_2`2a2b2c2d2e2f2g2h2i2j2k2l2m2n2o2r2s2t2u2v3O3c3f3g3j3n3o3r3t3u3w3x3y3z3{3|3}4O4P4Q4R4S4T4U4V4Y4`4a4b4c4d4e4f4g4h4i4j4k4l4m4n4o4p4q4r4s4t4u4v4w5P5d5g5h5k5o5p5s5u5v6S6]6^6_6`6a6b6c6d6e6f6g6h6i6j6k6l6m6n6o6p6q6r6s6t6u6w6x6y6z6{7W7h7k7n7q7r7t7v7y7{8S8T8W8Y8Z8e8f8g8h8i8j8k8l8m8n8o8p8q8r8s8t8u8v8w8x8y8z8{8|8}9O9P9Q9R9S9U9V9W9Y9Z9[9g9x9|9}:Q:X:Y:^:`:a:c:d:e:f:g:h:i:j:k:l:m:n:o:p:q:r:s:t:u:v:w:x:y:z:{:|:};O;P;Q;R;S;T;U;V;W;X;Y;Z;[;];^;_;`;b;c;d;e;f;g;h;s;t<i<j<n<o<p<u<z<{=O=Q=R=S=T=Y=l=mR#va-r#OZ#f#m#w$V$W$X$Y$Z$[$u$v%W%Y%[%`%t%|&O&Q&U&]&^&_&`&a&b&c&d&e&f&g&h&i&j&k&l&t&u&z'W'Y'Z([(o)p)r)t)}*Z*]+R+U,_,b,x,z,|-U-V-W-h-w.j.v/_/g/m/x0q0t0w1P1W1c1l1p2p2q2w2x2y2z2{2|2}3P3Q3R3S3T3U3V3W3X3Y3Z3[3]3^3_3`3a3b3d3e3h3i3k3l3m3p3q3s4X4x4y4z4{4|4}5O5Q5R5S5T5U5V5W5X5Y5Z5[5]5^5_5`5a5b5c5e5f5i5j5l5m5n5q5r5t6Q6U6|6}7O7P7Q7R7T7U7V7X7Y7Z7[7]7^7_7`7a7b7c7d7e7f7g7i7j7m7o7p7w7x7z7|7}8O8P8Q8R8U8V8X8[9T9]9^9_9`9a9b9e9f9h9i9j9k9l9m9n9o9p9q9r9s9t9u9v9w9y9z:O:P:S:U:V:Z:]:_:b;i;j;k;l;m;n;o;r;u;v;w;x;y;z;{;|;}<O<P<Q<R<S<T<U<V<W<X<Y<Z<[<]<^<_<`<a<b<c<d<e<f<g<h<k<l<m<q<r<s<t<v<w<x<y<|<}=P=U=V=^=_=`=p=qQ'[$]Y(P$s7S9d;p;qS(T2Y6PR(W$tT&X!})u!w$Qg#}$h'R'h'l'q(O(S)]*i*r*y*|+P+[+_+f,Y-q-t-z.P/t1O5|5}6O6[8a8b8c=c=d=h=}>O>P>W>X>Y>Z3ZfPVX[_bgjklmnoprxyz!S!W!X!Y!]!g!h!i!l!r!s!w!y!z!{!}#R#S#T#U#V#W#X#Y#Z#[#]#^#_#`#a#b#k#n#o#s#t#}$R$S$U$h$y$}%P%R%S%T%U%c%q%s%}&S&W&o&r&s&v&}'R'T'X']'h'l'q'y'}(O(Q(R(S(_(k(z)O)])a)e)k)o)s)u*O*S*T*i*n*r*y*|+O+P+[+_+c+f+q+t+y,S,U,W,Y,t-P-Q-c-j-q-t-y-z-{.P.a.c.k.s/Z/b/h/l/t/w0U0_0`0c0d0h0u1O1Q1[1`2Z2[2]2^2_2`2a2b2c2d2e2f2g2h2i2j2k2l2m2n2o2r2s2t2u2v3O3c3f3g3j3n3o3r3t3u3w3x3y3z3{3|3}4O4P4Q4R4S4T4U4V4Y4`4a4b4c4d4e4f4g4h4i4j4k4l4m4n4o4p4q4r4s4t4u4v4w5P5d5g5h5k5o5p5s5u5v5|5}6O6S6[6]6^6_6`6a6b6c6d6e6f6g6h6i6j6k6l6m6n6o6p6q6r6s6t6u6w6x6y6z6{7W7h7k7n7q7r7t7v7y7{8S8T8W8Y8Z8a8b8c8e8f8g8h8i8j8k8l8m8n8o8p8q8r8s8t8u8v8w8x8y8z8{8|8}9O9P9Q9R9S9U9V9W9Y9Z9[9g9x9|9}:Q:X:Y:^:`:a:c:d:e:f:g:h:i:j:k:l:m:n:o:p:q:r:s:t:u:v:w:x:y:z:{:|:};O;P;Q;R;S;T;U;V;W;X;Y;Z;[;];^;_;`;b;c;d;e;f;g;h;s;t<i<j<n<o<p<u<z<{=O=Q=R=S=T=Y=c=d=h=l=m=}>O>P>W>X>Y>Z3scPVX[_bdegjklmnoprxyz!S!W!X!Y!]!g!h!i!l!r!s!w!y!z!{!}#R#S#T#U#V#W#X#Y#Z#[#]#^#_#`#a#b#k#n#o#s#t#{#}$R$S$U$h$y$}%P%R%S%T%U%c%n%o%q%s%}&S&W&o&r&s&v&}'R'T'X']'h'l'q'y'}(O(Q(R(S(_(k(z)O)])`)a)e)i)j)k)o)s)u*O*S*T*i*n*r*y*|+O+P+[+_+c+f+q+t+y,S,U,W,Y,t,w-P-Q-c-j-q-t-y-z-{.P.a.c.k.s/Z/b/h/l/t/w0U0_0`0c0d0h0u1O1Q1[1`2V2W2X2Z2[2]2^2_2`2a2b2c2d2e2f2g2h2i2j2k2l2m2n2o2r2s2t2u2v3O3c3f3g3j3n3o3r3t3u3w3x3y3z3{3|3}4O4P4Q4R4S4T4U4V4Y4`4a4b4c4d4e4f4g4h4i4j4k4l4m4n4o4p4q4r4s4t4u4v4w5P5d5g5h5k5o5p5s5u5v5|5}6O6S6[6]6^6_6`6a6b6c6d6e6f6g6h6i6j6k6l6m6n6o6p6q6r6s6t6u6w6x6y6z6{7W7h7k7n7q7r7t7v7y7{8S8T8W8Y8Z8a8b8c8e8f8g8h8i8j8k8l8m8n8o8p8q8r8s8t8u8v8w8x8y8z8{8|8}9O9P9Q9R9S9U9V9W9Y9Z9[9g9x9|9}:Q:X:Y:^:`:a:c:d:e:f:g:h:i:j:k:l:m:n:o:p:q:r:s:t:u:v:w:x:y:z:{:|:};O;P;Q;R;S;T;U;V;W;X;Y;Z;[;];^;_;`;b;c;d;e;f;g;h;s;t<i<j<n<o<p<u<z<{=O=Q=R=S=T=Y=c=d=h=l=m=}>O>P>W>X>Y>Z0phPVX[_bjklmnopxyz!S!W!X!Y!]!i!l!r!s!w!y!z!{!}#R#S#T#U#V#W#X#Y#Z#[#]#^#_#`#a#b#k#n#o#s#t$R$S$U$y$}%P%R%S%T%U%c%}&S&W&o&r&s&v&}'T'X'y'}(_(k(z)O)k)o)s)u*O*S*T*n+O+q+t+y,S,U,W-P-Q-c-j-y.a.c.k.s/b/h/l/w0_0`0c0d0h0u1Q1`2Z2[2]2^2_2`2a2b2c2d2e2f2g2h2i2j2k2l2m2n2o2r2s2t2u2v3O3c3f3g3j3n3o3r3t3u3w3x3y3z3{3|3}4O4P4Q4R4S4T4U4V4Y4`4a4b4c4d4e4f4g4h4i4j4k4l4m4n4o4p4q4r4s4t4u4v4w5P5d5g5h5k5o5p5s5u5v6S6]6^6_6`6a6b6c6d6e6f6g6h6i6j6k6l6m6n6o6p6q6r6s6t6u6w6x6y6z6{7W7h7k7n7q7r7t7v7y7{8S8T8W8Y8Z8e8f8g8h8i8j8k8l8m8n8o8p8q8r8s8t8u8v8w8x8y8z8{8|8}9O9P9Q9R9S9U9V9W9Y9Z9[9g9x9|9}:Q:X:Y:^:`:a:c:d:e:f:g:h:i:j:k:l:m:n:o:p:q:r:s:t:u:v:w:x:y:z:{:|:};O;P;Q;R;S;T;U;V;W;X;Y;Z;[;];^;_;`;b;c;d;e;f;g;h;s;t<i<j<n<o<p<u<z<{=O=Q=R=S=T=Y=l=mT1Y0U1[!n#[Z#f#w$V$W$X$Y$[$s$v%W%Y%[&Q&^&_&`&a&b&c&d&e'W'Y'Z([)p)r*]+U,z-w/x1P1c1p7i7j!Y2i2Y2w2x2y2z2|2}3P3Q3R3S3T3U3V3W3`3a3b3d3e3h3i3k3l3m3p3q3s!^4l2q4x4y4z4{4}5O5Q5R5S5T5U5V5W5X5a5b5c5e5f5i5j5l5m5n5q5r5t6P6Q#Q6o#m%`%t&t&u&z(o)}+R,_,b,x-U-W.v2p6|6}7O7P7R7S7T7X7Y7Z7[7]7^7_7`7m7o7p7w7z7|8P8R8U8V8X8[9T:b=U=V#^8|%|&O&U)t,|-V-h/g/m0q0t0w1l4X6U7U7V7x7}8O8Q9]9^9_9`9b9d9e9f9h9i9j9k9l9m9n9o9w9y9z:O:P:S:U:V:Z:]:_<e<f=^=p=q!^;R.j/_;i;j;k;l;o;p;r;u;w;y;{;}<P<R<T<g<k<m<q<s<v<w<y<|<}=P=_=`o;S1W;q;v;x;z;|<O<Q<S<U<h<l<r<t<xS$iu#hQ$qwU's$j$l&nQ'u$kS'w$m$rQ*{'tQ*}'vQ+Q'xQ4W5wS4Z5y5zQ4[5{Q6T8]S6V8^8_Q6W8`Q9c=XS9{=Z=]Q:R=bQ=[=xS=a=z={Q=e=|Q=n>QS=o>R>US=r>S>TR=s>VT'm$h*r!csPVXt!S!l!r!s!w$h$}%P%S%U'h(S(_)Y*r+[+f+q+t,f,j.a.c.k0_0`0h1`Q$^rR*_']Q*w'rQ-s*zR/v-vQ(V$tQ)U%hQ)W%iQ*e'dQ+j(WR-`*fQ(U$tQ)T%hQ)[%kS*d'd)UQ*h'fS+i(V(WS-_*e*fQ.[+jQ/S,lQ/d-`R/f-bQ(T$tQ)S%hQ)V%iQ)X%jU*c'd)T)UU+h(U(V(WQ,e)WU-^*d*e*fS.Z+i+jS/c-_-`Q0W.[R0r/dT+d(S+f[%e!_$b'b+`.Q0PR,c)Pb$ov(S+Z+[+_+f.O.P0OR+S'zS+d(S+fT,i)Y,jR0V.WT1Z0U1[0w|PVX[_bjklmnopxyz!S!W!X!Y!]!i!l!r!s!w!y!z!{!}#R#S#T#U#V#W#X#Y#Z#[#]#^#_#`#a#b#k#n#o#s#t$R$S$U$y$}%P%R%S%T%U%c%}&S&W&o&r&s&v&}'T'X'y'}(_(k(z)O)k)o)s)u*O*S*T*n+O+q+t+y,S,U,W,^-P-Q-c-j-y.a.c.k.s/b/h/l/w0U0_0`0c0d0h0u1Q1[1`2Z2[2]2^2_2`2a2b2c2d2e2f2g2h2i2j2k2l2m2n2o2r2s2t2u2v3O3c3f3g3j3n3o3r3t3u3w3x3y3z3{3|3}4O4P4Q4R4S4T4U4V4Y4`4a4b4c4d4e4f4g4h4i4j4k4l4m4n4o4p4q4r4s4t4u4v4w5P5d5g5h5k5o5p5s5u5v6S6]6^6_6`6a6b6c6d6e6f6g6h6i6j6k6l6m6n6o6p6q6r6s6t6u6w6x6y6z6{7W7h7k7n7q7r7t7v7y7{8S8T8W8Y8Z8e8f8g8h8i8j8k8l8m8n8o8p8q8r8s8t8u8v8w8x8y8z8{8|8}9O9P9Q9R9S9U9V9W9Y9Z9[9g9x9|9}:Q:X:Y:^:`:a:c:d:e:f:g:h:i:j:k:l:m:n:o:p:q:r:s:t:u:v:w:x:y:z:{:|:};O;P;Q;R;S;T;U;V;W;X;Y;Z;[;];^;_;`;b;c;d;e;f;g;h;s;t<i<j<n<o<p<u<z<{=O=Q=R=S=T=Y=l=mT$x{${Q+o(ZR.^+mT$z{${Q(a$}Q(i%PQ(n%SQ(q%UQ.i+xQ0[.eQ0].hR1f0hR(d%OX+{(b(c+|,OR(e%OX(g%P%S%U0hR%S!T_%a!]%R(k,S,U.s0cR%U!UR.w,WR,Z(vQ)Z%kS*g'f)[S-a*h,lS/e-b/SR0s/fQ%r!hU)_%n%o%sU,n)`)i)jR/^,wR)f%qR/[,tSSO!mR!oSQ!rVR%y!rQ!lPS!sV!rQ!wX[%v!l!s!w+q0`1`Q+q(_Q0`.kR1`0_Q)l%tS,y)l7uR7u7VQ-R)yR/a-RQ&w#qS*Q&w7lR7l9XS*U&z&{R-Y*UQ)v&YR-O)v!l'S#|'g*m*p*u+V+Z,l-b-r-u-x.O.y/r/u/y0O0}1o4]4^4_5x6X6Y6Z:T:W:[=f=g=i=t=u=v=wR*Y'S1^dPVX[_bjklmnoprxyz!S!W!X!Y!]!g!i!l!r!s!w!y!z!{!}#R#S#T#U#V#W#X#Y#Z#[#]#^#_#`#a#b#k#n#o#s#t$R$S$U$y$}%P%R%S%T%U%c%q%}&S&W&o&r&s&v&}'T'X']'y'}(Q(R(_(k(z)O)a)e)k)o)s)u*O*S*T*n+O+c+q+t+y,S,U,W,t-P-Q-c-j-y-{.a.c.k.s/Z/b/h/l/w0U0_0`0c0d0h0u1Q1[1`2Z2[2]2^2_2`2a2b2c2d2e2f2g2h2i2j2k2l2m2n2o2r2s2t2u2v3O3c3f3g3j3n3o3r3t3u3w3x3y3z3{3|3}4O4P4Q4R4S4T4U4V4Y4`4a4b4c4d4e4f4g4h4i4j4k4l4m4n4o4p4q4r4s4t4u4v4w5P5d5g5h5k5o5p5s5u5v6S6]6^6_6`6a6b6c6d6e6f6g6h6i6j6k6l6m6n6o6p6q6r6s6t6u6w6x6y6z6{7W7h7k7n7q7r7t7v7y7{8S8T8W8Y8Z8e8f8g8h8i8j8k8l8m8n8o8p8q8r8s8t8u8v8w8x8y8z8{8|8}9O9P9Q9R9S9U9V9W9Y9Z9[9g9x9|9}:Q:X:Y:^:`:a:c:d:e:f:g:h:i:j:k:l:m:n:o:p:q:r:s:t:u:v:w:x:y:z:{:|:};O;P;Q;R;S;T;U;V;W;X;Y;Z;[;];^;_;`;b;c;d;e;f;g;h;s;t<i<j<n<o<p<u<z<{=O=Q=R=S=T=Y=l=m`#zd#{%n)`)i,w2V2XQ#{eQ%n!hQ)`%oQ)i%sQ,w)j!v2Vg#}$h'R'h'l'q(O(S)]*i*r*y*|+P+[+_+f,Y-q-t-z.P/t1O5|5}6O6[8a8b8c=c=d=h=}>O>P>W>X>Y>ZR2X2W|tPVX!S!l!r!s!w$}%P%S%U(_+q+t.a.c.k0_0`0h1`W$`t'h+[,fS'h$h*rS+[(S+fT,f)Y,jQ'^$^R*`'^Q*s'nR-l*sQ/o-nS0z/o0{R0{/pQ-|+WR/{-|Q+f(SR.X+fS+_(S+fS,g)Y,jQ.P+[W.S+_,g.P.}R.},fQ)Q%eR,d)QQ'{$oR+T'{Q1[0UR1v1[Q${{R(]${Q+s(`R.b+sQ+v(aR.f+vQ+|(bQ,O(cT.l+|,OQ({%`S,`({7sR7s7UQ(x%^R,](xQ,j)YR/Q,jQ)b%pS,p)b/VR/V,qQ,u)fR/],uT!uV!rj!kPVX!l!r!s!w(_+q.k0_0`1`Q%Q!SQ(`$}W(g%P%S%U0hQ.d+tQ0Y.aR0Z.c|ZPVX!S!l!r!s!w$}%P%S%U(_+q+t.a.c.k0_0`0h1`Q#f[U#m_#s&vQ#wbQ$VkQ$WlQ$XmQ$YnQ$ZoQ$[pQ$sx^$uy2^4a6d8p:l:mQ$vzQ%W!WQ%Y!XQ%[!YW%`!]%R(k,UU%t!i&o-QQ%|!yQ&O!zQ&Q!{S&U!})u^&]#R2`4c6f8s:o:pQ&^#SQ&_#TQ&`#UQ&a#VQ&b#WQ&c#XQ&d#YQ&e#ZQ&f#[Q&g#]Q&h#^Q&i#_Q&j#`Q&k#aQ&l#bQ&t#nQ&u#oS&z#t&}Q'W$RQ'Y$SQ'Z$UQ([$yQ(o%TQ)p%}Q)r&SQ)t&WQ)}&sS*Z'T4YQ*]'X^*^2Z3t5u8Y:`=Q=RQ+R'yQ+U'}Q,_(zQ,b)OQ,x)kQ,z)oQ,|)sQ-U*OQ-V*SQ-W*T^-[2[3u5v8Z:a=S=TQ-h*nQ-w+OQ.j+yQ.v,WQ/_-PQ/g-cQ/m-jQ/x-yQ0q/bQ0t/hQ0w/lQ1P/wU1W0U1[9VQ1c0dQ1l0uQ1p1QQ2Y2]Q2pjQ2q3xQ2w3yQ2x3{Q2y3}Q2z4PQ2{4RQ2|4TQ2}2_Q3P2aQ3Q2bQ3R2cQ3S2dQ3T2eQ3U2fQ3V2gQ3W2hQ3X2iQ3Y2jQ3Z2kQ3[2lQ3]2mQ3^2nQ3_2oQ3`2rQ3a2sQ3b2tQ3d2uQ3e2vQ3h3OQ3i3cQ3k3fQ3l3gQ3m3jQ3p3nQ3q3oQ3s3rQ4X4VQ4x3zQ4y3|Q4z4OQ4{4QQ4|4SQ4}4UQ5O4bQ5Q4dQ5R4eQ5S4fQ5T4gQ5U4hQ5V4iQ5W4jQ5X4kQ5Y4lQ5Z4mQ5[4nQ5]4oQ5^4pQ5_4qQ5`4rQ5a4sQ5b4tQ5c4uQ5e4vQ5f4wQ5i5PQ5j5dQ5l5gQ5m5hQ5n5kQ5q5oQ5r5pQ5t5sQ6P4`Q6Q3wQ6U6SQ6|6]Q6}6^Q7O6_Q7P6`Q7Q6aQ7R6bQ7S6cQ7T6eU7U,S.s0cQ7V%cQ7X6gQ7Y6hQ7Z6iQ7[6jQ7]6kQ7^6lQ7_6mQ7`6nQ7a6oQ7b6pQ7c6qQ7d6rQ7e6sQ7f6tQ7g6uQ7i6wQ7j6xQ7m6yQ7o6zQ7p6{Q7w7WQ7x7hQ7z7nQ7|7qQ7}7rQ8O7tQ8P7vQ8Q7yQ8R7{Q8U8SQ8V8TQ8X8WQ8[8eU9T#k&r7kQ9]8iQ9^8jQ9_8kQ9`8lQ9a8mQ9b8nQ9d8oQ9e8qQ9f8rQ9h8tQ9i8uQ9j8vQ9k8wQ9l8xQ9m8yQ9n8zQ9o8{Q9p8|Q9q8}Q9r9OQ9s9PQ9t9QQ9u9RQ9v9SQ9w9YQ9y9ZQ9z9[Q:O9gQ:P9xQ:S9|Q:U9}Q:V:QQ:Z:XQ:]:YQ:_:^Q:b8hQ;i:cQ;j:dQ;k:eQ;l:fQ;m:gQ;n:hQ;o:iQ;p:jQ;q:kQ;r:nQ;u:qQ;v:rQ;w:sQ;x:tQ;y:uQ;z:vQ;{:wQ;|:xQ;}:yQ<O:zQ<P:{Q<Q:|Q<R:}Q<S;OQ<T;PQ<U;QQ<V;RQ<W;SQ<X;TQ<Y;UQ<Z;VQ<[;WQ<];XQ<^;YQ<_;ZQ<`;[Q<a;]Q<b;^Q<c;_Q<d;`Q<e;bQ<f;cQ<g;dQ<h;eQ<k;fQ<l;gQ<m;hQ<q;sQ<r;tQ<s<iQ<t<jQ<v<nQ<w<oQ<x<pQ<y<uQ<|<zQ<}<{Q=P=OQ=U8gQ=V8fQ=^=YQ=_9UQ=`9WQ=p=lR=q=mR)z&oQ%u!iQ(}%cT)x&o-Q$SiPVX[bklmnopxyz!S!W!X!Y!l!r!s!w!{#R#S#T#U#V#W#X#Y#Z#[#]#^#_#`#a#b$R$S$U$y$}%P%S%U%}&S'X'}(_)o+O+q+t-y.a.c.k/w0_0`0d0h1Q1`2Z2[6w6x!t3v'T2]2^2_2`2a2b2c2d2e2f2g2h2i2j2k2l2m2n2o2r2s2t2u2v3O3c3f3g3j3n3o3r3y3{3}4P4R4T5u5v!x6R3t3u3w3x3z3|4O4Q4S4U4Y4`4a4b4c4d4e4f4g4h4i4j4k4l4m4n4o4p4q4r4s4t4u4v4w5P5d5g5h5k5o5p5s$O8d_j!]!i#k#n#o#s#t%R%T&o&r&s&v&}'y(k(z)O)k*O*T,U,W-Q6]6^6_6`6a6b6c6d6e6f6g6h6i6j6k6l6m6n6o6p6q6r6s6t6u6y6z6{7W7k7n7q7v7{8S8T8W8Y8Z8e8f8g8h#|=W!y!z!}%c&W)s)u*S*n,S-c-j.s/b/h/l0c0u4V6S7h7r7t7y8i8j8k8l8m8n8o8p8q8r8s8t8u8v8w8x8y8z8{8|8}9O9P9Q9R9S9Y9Z9[9g9x9|9}:Q:X:Y:^:`:a;b;c=Y=l=m!v=j+y-P9U9W:c:d:e:f:g:i:j:l:n:o:q:s:u:w:y:{:};P;R;T;V;X;Z;];_;d;f;h;s<i<n<o<u<z<{=O=Q=S!]=k0U1[9V:h:k:m:p:r:t:v:x:z:|;O;Q;S;U;W;Y;[;^;`;e;g;t<j<p=R=TQ#r_Q&q#kQ&y#sR)|&rS#q_#s^$Tj3w3x8e8f8g8hS*P&v7kT9X#k&rQ&|#tR*W&}R&T!|R&Z!}Q&Y!}R,})uQ#|gQ'U#}S'g$h*rQ*X'RQ*m'hQ*p'lQ*u'qQ+V(OS+Z(S+fQ,l)]Q-b*iQ-r*yQ-u*|Q-x+PS.O+[+_Q.y,YQ/r-qQ/u-tQ/y-zQ0O.PQ0}/tQ1o1OQ4]5|Q4^5}Q4_6OQ5x6[Q6X8aQ6Y8bQ6Z8cQ:T=cQ:W=dQ:[=hQ=f=}Q=g>OQ=i>PQ=t>WQ=u>XQ=v>YR=w>Z0t!OPVX[_bjklmnopxyz!S!W!X!Y!]!i!l!r!s!w!y!z!{!}#R#S#T#U#V#W#X#Y#Z#[#]#^#_#`#a#b#k#n#o#s#t$R$S$U$y$}%P%R%S%T%U%c%}&S&W&o&r&s&v&}'T'X'y'}(_(k(z)O)k)o)s)u*O*S*T*n+O+q+t+y,S,U,W-P-Q-c-j-y.a.c.k.s/b/h/l/w0U0_0`0c0d0h0u1Q1[1`2Z2[2]2^2_2`2a2b2c2d2e2f2g2h2i2j2k2l2m2n2o2r2s2t2u2v3O3c3f3g3j3n3o3r3t3u3w3x3y3z3{3|3}4O4P4Q4R4S4T4U4V4Y4`4a4b4c4d4e4f4g4h4i4j4k4l4m4n4o4p4q4r4s4t4u4v4w5P5d5g5h5k5o5p5s5u5v6S6]6^6_6`6a6b6c6d6e6f6g6h6i6j6k6l6m6n6o6p6q6r6s6t6u6w6x6y6z6{7W7h7k7n7q7r7t7v7y7{8S8T8W8Y8Z8e8f8g8h8i8j8k8l8m8n8o8p8q8r8s8t8u8v8w8x8y8z8{8|8}9O9P9Q9R9S9U9V9W9Y9Z9[9g9x9|9}:Q:X:Y:^:`:a:c:d:e:f:g:h:i:j:k:l:m:n:o:p:q:r:s:t:u:v:w:x:y:z:{:|:};O;P;Q;R;S;T;U;V;W;X;Y;Z;[;];^;_;`;b;c;d;e;f;g;h;s;t<i<j<n<o<p<u<z<{=O=Q=R=S=T=Y=l=m!v$Pg#}$h'R'h'l'q(O(S)]*i*r*y*|+P+[+_+f,Y-q-t-z.P/t1O5|5}6O6[8a8b8c=c=d=h=}>O>P>W>X>Y>ZS$]r']Q%l!gS%p!h%sQ)d%qU+W(Q(R+cQ,o)aQ,s)eQ/Y,tQ/z-{R0o/Z|vPVX!S!l!r!s!w$}%P%S%U(_+q+t.a.c.k0_0`0h1`#U#i[bklmnopxyz!W!X!Y!{#R#S#T#U#V#W#X#Y#Z#[#]#^#_#`#a#b$R$S$U$y%}&S'X'})o+O-y/w0d1Q2Z2[6w6xd+](S)Y+[+_+f,f,g,j.P.}!t6v'T2]2^2_2`2a2b2c2d2e2f2g2h2i2j2k2l2m2n2o2r2s2t2u2v3O3c3f3g3j3n3o3r3y3{3}4P4R4T5u5v!x;a3t3u3w3x3z3|4O4Q4S4U4Y4`4a4b4c4d4e4f4g4h4i4j4k4l4m4n4o4p4q4r4s4t4u4v4w5P5d5g5h5k5o5p5s$O=y_j!]!i#k#n#o#s#t%R%T&o&r&s&v&}'y(k(z)O)k*O*T,U,W-Q6]6^6_6`6a6b6c6d6e6f6g6h6i6j6k6l6m6n6o6p6q6r6s6t6u6y6z6{7W7k7n7q7v7{8S8T8W8Y8Z8e8f8g8h#|>[!y!z!}%c&W)s)u*S*n,S-c-j.s/b/h/l0c0u4V6S7h7r7t7y8i8j8k8l8m8n8o8p8q8r8s8t8u8v8w8x8y8z8{8|8}9O9P9Q9R9S9Y9Z9[9g9x9|9}:Q:X:Y:^:`:a;b;c=Y=l=m!v>]+y-P9U9W:c:d:e:f:g:i:j:l:n:o:q:s:u:w:y:{:};P;R;T;V;X;Z;];_;d;f;h;s<i<n<o<u<z<{=O=Q=S!]>^0U1[9V:h:k:m:p:r:t:v:x:z:|;O;Q;S;U;W;Y;[;^;`;e;g;t<j<p=R=TR'o$hQ'n$hR-k*rR$_rR-p*vQ+X(QQ+Y(RR.W+cT+e(S+fe+^(S)Y+[+_+f,f,g,j.P.}Q%f!_Q'a$bQ*b'bQ.T+`Q0R.QR1T0PQ#eZQ%X!WQ%Z!XQ%]!YQ'|$pQ(r%VQ(s%WQ(t%YQ(u%[Q(|%bQ)R%fQ)^%lQ)h%rQ)m%uQ*a'aQ,m)_Q-]*bQ.U+aQ.V+bQ.d+wQ.n,PQ.o,QQ.p,RQ.u,VQ.x,XQ.|,aQ/T,nQ/|-}Q0S.RQ0T.TQ0V.WQ0Z.gQ0j/PQ0p/^Q1R/}Q1U0QQ1V0RQ1_0^Q1g0iQ1q1SQ1r1TQ1u1ZQ1x1^Q1|1iQ2S1zR2T1{Q$pvS+a(S+fU-}+Z+[+_S/}.O.PR1S0O|!aPVX!S!l!r!s!w$}%P%S%U(_+q+t.a.c.k0_0`0h1`Q$dtW+b(S)Y+f,jW.R+[+_,f,gT0Q.P.}0t!OPVX[_bjklmnopxyz!S!W!X!Y!]!i!l!r!s!w!y!z!{!}#R#S#T#U#V#W#X#Y#Z#[#]#^#_#`#a#b#k#n#o#s#t$R$S$U$y$}%P%R%S%T%U%c%}&S&W&o&r&s&v&}'T'X'y'}(_(k(z)O)k)o)s)u*O*S*T*n+O+q+t+y,S,U,W-P-Q-c-j-y.a.c.k.s/b/h/l/w0U0_0`0c0d0h0u1Q1[1`2Z2[2]2^2_2`2a2b2c2d2e2f2g2h2i2j2k2l2m2n2o2r2s2t2u2v3O3c3f3g3j3n3o3r3t3u3w3x3y3z3{3|3}4O4P4Q4R4S4T4U4V4Y4`4a4b4c4d4e4f4g4h4i4j4k4l4m4n4o4p4q4r4s4t4u4v4w5P5d5g5h5k5o5p5s5u5v6S6]6^6_6`6a6b6c6d6e6f6g6h6i6j6k6l6m6n6o6p6q6r6s6t6u6w6x6y6z6{7W7h7k7n7q7r7t7v7y7{8S8T8W8Y8Z8e8f8g8h8i8j8k8l8m8n8o8p8q8r8s8t8u8v8w8x8y8z8{8|8}9O9P9Q9R9S9U9V9W9Y9Z9[9g9x9|9}:Q:X:Y:^:`:a:c:d:e:f:g:h:i:j:k:l:m:n:o:p:q:r:s:t:u:v:w:x:y:z:{:|:};O;P;Q;R;S;T;U;V;W;X;Y;Z;[;];^;_;`;b;c;d;e;f;g;h;s;t<i<j<n<o<p<u<z<{=O=Q=R=S=T=Y=l=mR.{,^0w}PVX[_bjklmnopxyz!S!W!X!Y!]!i!l!r!s!w!y!z!{!}#R#S#T#U#V#W#X#Y#Z#[#]#^#_#`#a#b#k#n#o#s#t$R$S$U$y$}%P%R%S%T%U%c%}&S&W&o&r&s&v&}'T'X'y'}(_(k(z)O)k)o)s)u*O*S*T*n+O+q+t+y,S,U,W,^-P-Q-c-j-y.a.c.k.s/b/h/l/w0U0_0`0c0d0h0u1Q1[1`2Z2[2]2^2_2`2a2b2c2d2e2f2g2h2i2j2k2l2m2n2o2r2s2t2u2v3O3c3f3g3j3n3o3r3t3u3w3x3y3z3{3|3}4O4P4Q4R4S4T4U4V4Y4`4a4b4c4d4e4f4g4h4i4j4k4l4m4n4o4p4q4r4s4t4u4v4w5P5d5g5h5k5o5p5s5u5v6S6]6^6_6`6a6b6c6d6e6f6g6h6i6j6k6l6m6n6o6p6q6r6s6t6u6w6x6y6z6{7W7h7k7n7q7r7t7v7y7{8S8T8W8Y8Z8e8f8g8h8i8j8k8l8m8n8o8p8q8r8s8t8u8v8w8x8y8z8{8|8}9O9P9Q9R9S9U9V9W9Y9Z9[9g9x9|9}:Q:X:Y:^:`:a:c:d:e:f:g:h:i:j:k:l:m:n:o:p:q:r:s:t:u:v:w:x:y:z:{:|:};O;P;Q;R;S;T;U;V;W;X;Y;Z;[;];^;_;`;b;c;d;e;f;g;h;s;t<i<j<n<o<p<u<z<{=O=Q=R=S=T=Y=l=mT$w{${Q(h%PQ(m%SQ(p%UR1e0hQ%b!]Q(l%RQ,T(kQ.r,SQ.t,UQ0b.sR1b0cQ%r!hR)_%sR)g%q",
    nodeNames: "\u26A0 ( HeredocString EscapeSequence abstract LogicOp array as Boolean break case catch class clone const continue default declare do echo else elseif enddeclare endfor endforeach endif endswitch endwhile enum extends final finally fn for foreach from function global goto if implements include include_once LogicOp insteadof interface list match namespace new null LogicOp print require require_once return switch throw trait try unset use var Visibility while LogicOp yield LineComment BlockComment TextInterpolation PhpClose Text PhpOpen Template TextInterpolation EmptyStatement ; } { Block : LabelStatement Name ExpressionStatement ConditionalExpression LogicOp MatchExpression ) ( ParenthesizedExpression MatchBlock MatchArm , => AssignmentExpression ArrayExpression ValueList & VariadicUnpacking ... Pair [ ] ListExpression ValueList Pair Pair SubscriptExpression MemberExpression -> ?-> VariableName DynamicVariable $ ${ CallExpression ArgList NamedArgument SpreadArgument CastExpression UnionType LogicOp OptionalType NamedType QualifiedName \\ NamespaceName ScopedExpression :: AssignOp UpdateExpression UpdateOp YieldExpression BinaryExpression LogicOp LogicOp LogicOp BitOp BitOp BitOp CompareOp CompareOp BitOp ArithOp ConcatOp ArithOp ArithOp IncludeExpression RequireExpression CloneExpression UnaryExpression ControlOp LogicOp PrintIntrinsic FunctionExpression static ParamList Parameter #[ Attributes Attribute VariadicParameter PropertyParameter UseList ArrowFunction NewExpression BaseClause ClassInterfaceClause DeclarationList ConstDeclaration VariableDeclarator PropertyDeclaration VariableDeclarator MethodDeclaration UseDeclaration UseList UseInsteadOfClause UseAsClause UpdateExpression ArithOp ShellExpression ThrowExpression Integer Float String MemberExpression SubscriptExpression UnaryExpression ArithOp Interpolation String IfStatement ColonBlock SwitchStatement Block CaseStatement DefaultStatement ColonBlock WhileStatement EmptyStatement DoStatement ForStatement ForSpec SequenceExpression ForeachStatement ForSpec Pair GotoStatement ContinueStatement BreakStatement ReturnStatement TryStatement CatchDeclarator DeclareStatement EchoStatement UnsetStatement ConstDeclaration FunctionDefinition ClassDeclaration InterfaceDeclaration TraitDeclaration EnumDeclaration EnumBody EnumCase NamespaceDefinition NamespaceUseDeclaration UseGroup UseClause UseClause GlobalDeclaration FunctionStaticDeclaration Program",
    maxTerm: 303,
    nodeProps: [
      [NodeProp.group, -36, 2, 8, 50, 82, 84, 86, 89, 94, 95, 103, 107, 108, 111, 112, 115, 119, 124, 127, 130, 132, 133, 147, 148, 149, 150, 153, 154, 164, 165, 178, 180, 181, 182, 183, 184, 190, "Expression", -28, 75, 79, 81, 83, 191, 193, 198, 200, 201, 204, 207, 208, 209, 210, 211, 213, 214, 215, 216, 217, 218, 219, 220, 221, 224, 225, 229, 230, "Statement", -3, 120, 122, 123, "Type"],
      [NodeProp.openedBy, 70, "phpOpen", 77, "{", 87, "(", 102, "#["],
      [NodeProp.closedBy, 72, "phpClose", 78, "}", 88, ")", 158, "]"]
    ],
    skippedNodes: [0],
    repeatNodeCount: 29,
    tokenData: "!5h_R!ZOX$tXY%nYZ&}Z]$t]^%n^p$tpq%nqr(]rs)wst*atu/nuv2_vw3`wx4gxy8Oyz8fz{8|{|:W|};_}!O;u!O!P=R!P!QBl!Q!RFr!R![Hn![!]Nz!]!^!!O!^!_!!f!_!`!%R!`!a!&V!a!b!'Z!b!c!*T!c!d!*k!d!e!+q!e!}!*k!}#O!-k#O#P!.R#P#Q!.i#Q#R!/P#R#S!*k#S#T!/j#T#U!*k#U#V!+q#V#o!*k#o#p!2y#p#q!3a#q#r!4j#r#s!5Q#s$f$t$f$g%n$g&j!*k&j$I_$t$I_$I`%n$I`$KW$t$KW$KX%n$KX?HT$t?HT?HU%n?HU~$tP$yT&vPOY$tYZ%YZ!^$t!^!_%_!_~$tP%_O&vPP%bSOY$tYZ%YZ!a$t!b~$tV%ub&vP&uUOX$tXY%nYZ&}Z]$t]^%n^p$tpq%nq!^$t!^!_%_!_$f$t$f$g%n$g$I_$t$I_$I`%n$I`$KW$t$KW$KX%n$KX?HT$t?HT?HU%n?HU~$tV'UW&vP&uUXY'nYZ'n]^'npq'n$f$g'n$I_$I`'n$KW$KX'n?HT?HU'nU'sW&uUXY'nYZ'n]^'npq'n$f$g'n$I_$I`'n$KW$KX'n?HT?HU'nR(dU$^Q&vPOY$tYZ%YZ!^$t!^!_%_!_!`(v!`~$tR(}U$QQ&vPOY$tYZ%YZ!^$t!^!_%_!_!`)a!`~$tR)hT$QQ&vPOY$tYZ%YZ!^$t!^!_%_!_~$tV*QT'eS&vP'fQOY$tYZ%YZ!^$t!^!_%_!_~$tV*hZ&vP!eUOY+ZYZ%YZ]+Z]^$t^!^+Z!^!_+}!_!a+Z!a!b-i!b!}+Z!}#O.x#O~+ZV+bX&vP!eUOY+ZYZ%YZ]+Z]^$t^!^+Z!^!_+}!_!a+Z!a!b-i!b~+ZV,SV!eUOY+ZYZ%YZ]+Z]^$t^!a+Z!a!b,i!b~+ZU,lUOY-OYZ-dZ]-O]^-d^!`-O!a~-OU-TT!eUOY-OZ]-O^!a-O!a!b,i!b~-OU-iO!eUV-nX&vPOY+ZYZ.ZZ]+Z]^.b^!^+Z!^!_+}!_!`+Z!`!a$t!a~+ZV.bO&vP!eUV.iT&vP!eUOY$tYZ%YZ!^$t!^!_%_!_~$tV/RX&vP$dQ!eUOY+ZYZ%YZ]+Z]^$t^!^+Z!^!_+}!_!a+Z!a!b-i!b~+Z_/u^&vP#eQOY$tYZ%YZ!^$t!^!_%_!_!c$t!c!}0q!}#R$t#R#S0q#S#T$t#T#o0q#o#p1w#p$g$t$g&j0q&j~$t_0x_&vP#c^OY$tYZ%YZ!Q$t!Q![0q![!^$t!^!_%_!_!c$t!c!}0q!}#R$t#R#S0q#S#T$t#T#o0q#o$g$t$g&j0q&j~$tV2OT&vP#fUOY$tYZ%YZ!^$t!^!_%_!_~$tR2fU&vP$VQOY$tYZ%YZ!^$t!^!_%_!_!`2x!`~$tR3PT#wQ&vPOY$tYZ%YZ!^$t!^!_%_!_~$tV3gW#TU&vPOY$tYZ%YZv$tvw4Pw!^$t!^!_%_!_!`2x!`~$tR4WT#|Q&vPOY$tYZ%YZ!^$t!^!_%_!_~$tR4nX&vP%UQOY4gYZ5ZZw4gwx6bx!^4g!^!_6x!_#O4g#O#P7j#P~4gR5bT&vP%UQOw5qwx6Vx#O5q#O#P6[#P~5qQ5vT%UQOw5qwx6Vx#O5q#O#P6[#P~5qQ6[O%UQQ6_PO~5qR6iT&vP%UQOY$tYZ%YZ!^$t!^!_%_!_~$tR6}X%UQOY4gYZ5ZZw4gwx6bx!a4g!a!b5q!b#O4g#O#P7j#P~4gR7oT&vPOY4gYZ5ZZ!^4g!^!_6x!_~4gR8VT!zQ&vPOY$tYZ%YZ!^$t!^!_%_!_~$tV8mT!yU&vPOY$tYZ%YZ!^$t!^!_%_!_~$tR9TW&vP$VQOY$tYZ%YZz$tz{9m{!^$t!^!_%_!_!`2x!`~$tR9tU$WQ&vPOY$tYZ%YZ!^$t!^!_%_!_!`2x!`~$tR:_W$TQ&vPOY$tYZ%YZ{$t{|:w|!^$t!^!_%_!_!`2x!`~$tR;OT$yQ&vPOY$tYZ%YZ!^$t!^!_%_!_~$tR;fT#OQ&vPOY$tYZ%YZ!^$t!^!_%_!_~$t_<OX$TQ%SW&vPOY$tYZ%YZ}$t}!O:w!O!^$t!^!_%_!_!`2x!`!a<k!a~$tV<rT#aU&vPOY$tYZ%YZ!^$t!^!_%_!_~$tV=YY&vP$UQOY$tYZ%YZ!O$t!O!P=x!P!Q$t!Q![>z![!^$t!^!_%_!_!`2x!`~$tV=}V&vPOY$tYZ%YZ!O$t!O!P>d!P!^$t!^!_%_!_~$tV>kT#VU&vPOY$tYZ%YZ!^$t!^!_%_!_~$tR?R]&vP$}QOY$tYZ%YZ!Q$t!Q![>z![!^$t!^!_%_!_!g$t!g!h?z!h#R$t#R#SBQ#S#X$t#X#Y?z#Y~$tR@PZ&vPOY$tYZ%YZ{$t{|@r|}$t}!O@r!O!Q$t!Q![A^![!^$t!^!_%_!_~$tR@wV&vPOY$tYZ%YZ!Q$t!Q![A^![!^$t!^!_%_!_~$tRAeX&vP$}QOY$tYZ%YZ!Q$t!Q![A^![!^$t!^!_%_!_#R$t#R#S@r#S~$tRBVV&vPOY$tYZ%YZ!Q$t!Q![>z![!^$t!^!_%_!_~$tVBsY&vP$VQOY$tYZ%YZz$tz{Cc{!P$t!P!Q+Z!Q!^$t!^!_%_!_!`2x!`~$tVChV&vPOYCcYZC}ZzCcz{EQ{!^Cc!^!_FY!_~CcVDSR&vPOzD]z{Di{~D]UD`ROzD]z{Di{~D]UDlTOzD]z{Di{!PD]!P!QD{!Q~D]UEQO!fUVEVX&vPOYCcYZC}ZzCcz{EQ{!PCc!P!QEr!Q!^Cc!^!_FY!_~CcVEyT!fU&vPOY$tYZ%YZ!^$t!^!_%_!_~$tVF]VOYCcYZC}ZzCcz{EQ{!aCc!a!bD]!b~CcZFyk&vP$|YOY$tYZ%YZ!O$t!O!P>z!P!Q$t!Q![Hn![!^$t!^!_%_!_!d$t!d!eJ`!e!g$t!g!h?z!h!q$t!q!rKt!r!z$t!z!{MS!{#R$t#R#SIt#S#U$t#U#VJ`#V#X$t#X#Y?z#Y#c$t#c#dKt#d#l$t#l#mMS#m~$tZHu_&vP$|YOY$tYZ%YZ!O$t!O!P>z!P!Q$t!Q![Hn![!^$t!^!_%_!_!g$t!g!h?z!h#R$t#R#SIt#S#X$t#X#Y?z#Y~$tZIyV&vPOY$tYZ%YZ!Q$t!Q![Hn![!^$t!^!_%_!_~$tZJeW&vPOY$tYZ%YZ!Q$t!Q!RJ}!R!SJ}!S!^$t!^!_%_!_~$tZKUY&vP$|YOY$tYZ%YZ!Q$t!Q!RJ}!R!SJ}!S!^$t!^!_%_!_#R$t#R#SJ`#S~$tZKyV&vPOY$tYZ%YZ!Q$t!Q!YL`!Y!^$t!^!_%_!_~$tZLgX&vP$|YOY$tYZ%YZ!Q$t!Q!YL`!Y!^$t!^!_%_!_#R$t#R#SKt#S~$tZMXZ&vPOY$tYZ%YZ!Q$t!Q![Mz![!^$t!^!_%_!_!c$t!c!iMz!i#T$t#T#ZMz#Z~$tZNR]&vP$|YOY$tYZ%YZ!Q$t!Q![Mz![!^$t!^!_%_!_!c$t!c!iMz!i#R$t#R#SMS#S#T$t#T#ZMz#Z~$tR! RV!rQ&vPOY$tYZ%YZ![$t![!]! h!]!^$t!^!_%_!_~$tR! oT#tQ&vPOY$tYZ%YZ!^$t!^!_%_!_~$tV!!VT!nU&vPOY$tYZ%YZ!^$t!^!_%_!_~$tR!!kW$RQOY$tYZ%YZ!^$t!^!_!#T!_!`!#n!`!a)a!a!b!$[!b~$tR!#[U$SQ&vPOY$tYZ%YZ!^$t!^!_%_!_!`2x!`~$tR!#uV$RQ&vPOY$tYZ%YZ!^$t!^!_%_!_!`$t!`!a)a!a~$tP!$aR!jP!_!`!$j!r!s!$o#d#e!$oP!$oO!jPP!$rQ!j!k!$x#[#]!$xP!${Q!r!s!$j#d#e!$jV!%YV#uQ&vPOY$tYZ%YZ!^$t!^!_%_!_!`(v!`!a!%o!a~$tV!%vT#PU&vPOY$tYZ%YZ!^$t!^!_%_!_~$tR!&^V$RQ&vPOY$tYZ%YZ!^$t!^!_%_!_!`!&s!`!a!#T!a~$tR!&zT$RQ&vPOY$tYZ%YZ!^$t!^!_%_!_~$tV!'bY!wQ&vPOY$tYZ%YZ}$t}!O!(Q!O!^$t!^!_%_!_!`$t!`!a!)S!a!b!)j!b~$tV!(VV&vPOY$tYZ%YZ!^$t!^!_%_!_!`$t!`!a!(l!a~$tV!(sT#bU&vPOY$tYZ%YZ!^$t!^!_%_!_~$tV!)ZT!hU&vPOY$tYZ%YZ!^$t!^!_%_!_~$tR!)qU#zQ&vPOY$tYZ%YZ!^$t!^!_%_!_!`2x!`~$tR!*[T$]Q&vPOY$tYZ%YZ!^$t!^!_%_!_~$t_!*r_&vP!t^OY$tYZ%YZ!Q$t!Q![!*k![!^$t!^!_%_!_!c$t!c!}!*k!}#R$t#R#S!*k#S#T$t#T#o!*k#o$g$t$g&j!*k&j~$t_!+xc&vP!t^OY$tYZ%YZr$trs!-Tsw$twx4gx!Q$t!Q![!*k![!^$t!^!_%_!_!c$t!c!}!*k!}#R$t#R#S!*k#S#T$t#T#o!*k#o$g$t$g&j!*k&j~$tR!-[T&vP'fQOY$tYZ%YZ!^$t!^!_%_!_~$tV!-rT#XU&vPOY$tYZ%YZ!^$t!^!_%_!_~$tV!.YT#qU&vPOY$tYZ%YZ!^$t!^!_%_!_~$tR!.pT#YQ&vPOY$tYZ%YZ!^$t!^!_%_!_~$tR!/WU$OQ&vPOY$tYZ%YZ!^$t!^!_%_!_!`2x!`~$tR!/oX&vPOY!/jYZ!0[Z!^!/j!^!_!1_!_#O!/j#O#P!1}#P#S!/j#S#T!2c#T~!/jR!0aT&vPO#O!0p#O#P!1S#P#S!0p#S#T!1Y#T~!0pQ!0sTO#O!0p#O#P!1S#P#S!0p#S#T!1Y#T~!0pQ!1VPO~!0pQ!1_O$zQR!1bXOY!/jYZ!0[Z!a!/j!a!b!0p!b#O!/j#O#P!1}#P#S!/j#S#T!2c#T~!/jR!2ST&vPOY!/jYZ!0[Z!^!/j!^!_!1_!_~!/jR!2jT$zQ&vPOY$tYZ%YZ!^$t!^!_%_!_~$tV!3QT!pU&vPOY$tYZ%YZ!^$t!^!_%_!_~$tV!3jW#}Q#mS&vPOY$tYZ%YZ!^$t!^!_%_!_!`2x!`#p$t#p#q!4S#q~$tR!4ZT#{Q&vPOY$tYZ%YZ!^$t!^!_%_!_~$tR!4qT!oQ&vPOY$tYZ%YZ!^$t!^!_%_!_~$tR!5XT$^Q&vPOY$tYZ%YZ!^$t!^!_%_!_~$t",
    tokenizers: [expression, interpolated, semicolon2, 0, 1, 2, 3, eofToken],
    topRules: { "Template": [0, 73], "Program": [1, 231] },
    dynamicPrecedences: { "283": 1 },
    specialized: [{ term: 82, get: (value, stack) => keywords(value) << 1 }, { term: 82, get: (value) => spec_Name[value] || -1 }],
    tokenPrec: 29359
  });

  // node_modules/@lezer/html/dist/index.es.js
  var scriptText = 53;
  var StartCloseScriptTag = 1;
  var styleText = 54;
  var StartCloseStyleTag = 2;
  var textareaText = 55;
  var StartCloseTextareaTag = 3;
  var StartTag = 4;
  var StartScriptTag = 5;
  var StartStyleTag = 6;
  var StartTextareaTag = 7;
  var StartSelfClosingTag = 8;
  var StartCloseTag = 9;
  var NoMatchStartCloseTag = 10;
  var MismatchedStartCloseTag = 11;
  var missingCloseTag = 56;
  var IncompleteCloseTag = 12;
  var commentContent$1 = 57;
  var Element = 18;
  var ScriptText = 27;
  var StyleText = 30;
  var TextareaText = 33;
  var OpenTag = 35;
  var Dialect_noMatch = 0;
  var selfClosers = {
    area: true,
    base: true,
    br: true,
    col: true,
    command: true,
    embed: true,
    frame: true,
    hr: true,
    img: true,
    input: true,
    keygen: true,
    link: true,
    meta: true,
    param: true,
    source: true,
    track: true,
    wbr: true,
    menuitem: true
  };
  var implicitlyClosed = {
    dd: true,
    li: true,
    optgroup: true,
    option: true,
    p: true,
    rp: true,
    rt: true,
    tbody: true,
    td: true,
    tfoot: true,
    th: true,
    tr: true
  };
  var closeOnOpen = {
    dd: { dd: true, dt: true },
    dt: { dd: true, dt: true },
    li: { li: true },
    option: { option: true, optgroup: true },
    optgroup: { optgroup: true },
    p: {
      address: true,
      article: true,
      aside: true,
      blockquote: true,
      dir: true,
      div: true,
      dl: true,
      fieldset: true,
      footer: true,
      form: true,
      h1: true,
      h2: true,
      h3: true,
      h4: true,
      h5: true,
      h6: true,
      header: true,
      hgroup: true,
      hr: true,
      menu: true,
      nav: true,
      ol: true,
      p: true,
      pre: true,
      section: true,
      table: true,
      ul: true
    },
    rp: { rp: true, rt: true },
    rt: { rp: true, rt: true },
    tbody: { tbody: true, tfoot: true },
    td: { td: true, th: true },
    tfoot: { tbody: true },
    th: { td: true, th: true },
    thead: { tbody: true, tfoot: true },
    tr: { tr: true }
  };
  function nameChar(ch) {
    return ch == 45 || ch == 46 || ch == 58 || ch >= 65 && ch <= 90 || ch == 95 || ch >= 97 && ch <= 122 || ch >= 161;
  }
  function isSpace2(ch) {
    return ch == 9 || ch == 10 || ch == 13 || ch == 32;
  }
  var cachedName = null;
  var cachedInput = null;
  var cachedPos = 0;
  function tagNameAfter(input, offset) {
    let pos = input.pos + offset;
    if (cachedPos == pos && cachedInput == input)
      return cachedName;
    let next = input.peek(offset);
    while (isSpace2(next))
      next = input.peek(++offset);
    let name2 = "";
    for (; ; ) {
      if (!nameChar(next))
        break;
      name2 += String.fromCharCode(next);
      next = input.peek(++offset);
    }
    cachedInput = input;
    cachedPos = pos;
    return cachedName = name2 ? name2.toLowerCase() : next == question || next == bang ? void 0 : null;
  }
  var lessThan = 60;
  var greaterThan = 62;
  var slash2 = 47;
  var question = 63;
  var bang = 33;
  function ElementContext(name2, parent) {
    this.name = name2;
    this.parent = parent;
    this.hash = parent ? parent.hash : 0;
    for (let i = 0; i < name2.length; i++)
      this.hash += (this.hash << 4) + name2.charCodeAt(i) + (name2.charCodeAt(i) << 8);
  }
  var startTagTerms = [StartTag, StartSelfClosingTag, StartScriptTag, StartStyleTag, StartTextareaTag];
  var elementContext = new ContextTracker({
    start: null,
    shift(context, term, stack, input) {
      return startTagTerms.indexOf(term) > -1 ? new ElementContext(tagNameAfter(input, 1) || "", context) : context;
    },
    reduce(context, term) {
      return term == Element && context ? context.parent : context;
    },
    reuse(context, node, stack, input) {
      let type = node.type.id;
      return type == StartTag || type == OpenTag ? new ElementContext(tagNameAfter(input, 1) || "", context) : context;
    },
    hash(context) {
      return context ? context.hash : 0;
    },
    strict: false
  });
  var tagStart = new ExternalTokenizer((input, stack) => {
    if (input.next != lessThan) {
      if (input.next < 0 && stack.context)
        input.acceptToken(missingCloseTag);
      return;
    }
    input.advance();
    let close = input.next == slash2;
    if (close)
      input.advance();
    let name2 = tagNameAfter(input, 0);
    if (name2 === void 0)
      return;
    if (!name2)
      return input.acceptToken(close ? IncompleteCloseTag : StartTag);
    let parent = stack.context ? stack.context.name : null;
    if (close) {
      if (name2 == parent)
        return input.acceptToken(StartCloseTag);
      if (parent && implicitlyClosed[parent])
        return input.acceptToken(missingCloseTag, -2);
      if (stack.dialectEnabled(Dialect_noMatch))
        return input.acceptToken(NoMatchStartCloseTag);
      for (let cx = stack.context; cx; cx = cx.parent)
        if (cx.name == name2)
          return;
      input.acceptToken(MismatchedStartCloseTag);
    } else {
      if (name2 == "script")
        return input.acceptToken(StartScriptTag);
      if (name2 == "style")
        return input.acceptToken(StartStyleTag);
      if (name2 == "textarea")
        return input.acceptToken(StartTextareaTag);
      if (selfClosers.hasOwnProperty(name2))
        return input.acceptToken(StartSelfClosingTag);
      if (parent && closeOnOpen[parent] && closeOnOpen[parent][name2])
        input.acceptToken(missingCloseTag, -1);
      else
        input.acceptToken(StartTag);
    }
  }, { contextual: true });
  var commentContent = new ExternalTokenizer((input) => {
    for (let endPos = 0, i = 0; ; i++) {
      if (input.next < 0) {
        if (i)
          input.acceptToken(commentContent$1);
        break;
      }
      if (input.next == "-->".charCodeAt(endPos)) {
        endPos++;
        if (endPos == 3) {
          if (i > 3)
            input.acceptToken(commentContent$1, -2);
          break;
        }
      } else {
        endPos = 0;
      }
      input.advance();
    }
  });
  function contentTokenizer(tag, textToken, endToken) {
    let lastState = 2 + tag.length;
    return new ExternalTokenizer((input) => {
      for (let state = 0, matchedLen = 0, i = 0; ; i++) {
        if (input.next < 0) {
          if (i)
            input.acceptToken(textToken);
          break;
        }
        if (state == 0 && input.next == lessThan || state == 1 && input.next == slash2 || state >= 2 && state < lastState && input.next == tag.charCodeAt(state - 2)) {
          state++;
          matchedLen++;
        } else if ((state == 2 || state == lastState) && isSpace2(input.next)) {
          matchedLen++;
        } else if (state == lastState && input.next == greaterThan) {
          if (i > matchedLen)
            input.acceptToken(textToken, -matchedLen);
          else
            input.acceptToken(endToken, -(matchedLen - 2));
          break;
        } else if ((input.next == 10 || input.next == 13) && i) {
          input.acceptToken(textToken, 1);
          break;
        } else {
          state = matchedLen = 0;
        }
        input.advance();
      }
    });
  }
  var scriptTokens = contentTokenizer("script", scriptText, StartCloseScriptTag);
  var styleTokens = contentTokenizer("style", styleText, StartCloseStyleTag);
  var textareaTokens = contentTokenizer("textarea", textareaText, StartCloseTextareaTag);
  var parser4 = LRParser.deserialize({
    version: 13,
    states: ",xOVOxOOO!WQ!bO'#CoO!]Q!bO'#CyO!bQ!bO'#C|O!gQ!bO'#DPO!lQ!bO'#DRO!qOXO'#CnO!|OYO'#CnO#XO[O'#CnO$eOxO'#CnOOOW'#Cn'#CnO$lO!rO'#DSO$tQ!bO'#DUO$yQ!bO'#DVOOOW'#Dj'#DjOOOW'#DX'#DXQVOxOOO%OQ#tO,59ZO%WQ#tO,59eO%`Q#tO,59hO%hQ#tO,59kO%pQ#tO,59mOOOX'#D]'#D]O%xOXO'#CwO&TOXO,59YOOOY'#D^'#D^O&]OYO'#CzO&hOYO,59YOOO['#D_'#D_O&pO[O'#C}O&{O[O,59YOOOW'#D`'#D`O'TOxO,59YO'[Q!bO'#DQOOOW,59Y,59YOOO`'#Da'#DaO'aO!rO,59nOOOW,59n,59nO'iQ!bO,59pO'nQ!bO,59qOOOW-E7V-E7VO'sQ#tO'#CqOOQO'#DY'#DYO(OQ#tO1G.uOOOX1G.u1G.uO(WQ#tO1G/POOOY1G/P1G/PO(`Q#tO1G/SOOO[1G/S1G/SO(hQ#tO1G/VOOOW1G/V1G/VO(pQ#tO1G/XOOOW1G/X1G/XOOOX-E7Z-E7ZO(xQ!bO'#CxOOOW1G.t1G.tOOOY-E7[-E7[O(}Q!bO'#C{OOO[-E7]-E7]O)SQ!bO'#DOOOOW-E7^-E7^O)XQ!bO,59lOOO`-E7_-E7_OOOW1G/Y1G/YOOOW1G/[1G/[OOOW1G/]1G/]O)^Q&jO,59]OOQO-E7W-E7WOOOX7+$a7+$aOOOY7+$k7+$kOOO[7+$n7+$nOOOW7+$q7+$qOOOW7+$s7+$sO)iQ!bO,59dO)nQ!bO,59gO)sQ!bO,59jOOOW1G/W1G/WO)xO,UO'#CtO*WO7[O'#CtOOQO1G.w1G.wOOOW1G/O1G/OOOOW1G/R1G/ROOOW1G/U1G/UOOOO'#DZ'#DZO*fO,UO,59`OOQO,59`,59`OOOO'#D['#D[O*tO7[O,59`OOOO-E7X-E7XOOQO1G.z1G.zOOOO-E7Y-E7Y",
    stateData: "+[~O!]OS~OSSOTPOUQOVROWTOY]OZ[O[^O^^O_^O`^Oa^Ow^Oz_O!cZO~OdaO~OdbO~OdcO~OddO~OdeO~O!VfOPkP!YkP~O!WiOQnP!YnP~O!XlORqP!YqP~OSSOTPOUQOVROWTOXqOY]OZ[O[^O^^O_^O`^Oa^Ow^O!cZO~O!YrO~P#dO!ZsO!duO~OdvO~OdwO~OfyOj|O~OfyOj!OO~OfyOj!QO~OfyOj!SO~OfyOj!UO~O!VfOPkX!YkX~OP!WO!Y!XO~O!WiOQnX!YnX~OQ!ZO!Y!XO~O!XlORqX!YqX~OR!]O!Y!XO~O!Y!XO~P#dOd!_O~O!ZsO!d!aO~Oj!bO~Oj!cO~Og!dOfeXjeX~OfyOj!fO~OfyOj!gO~OfyOj!hO~OfyOj!iO~OfyOj!jO~Od!kO~Od!lO~Od!mO~Oj!nO~Oi!qO!_!oO!a!pO~Oj!rO~Oj!sO~Oj!tO~O_!uO`!uO!_!wO!`!uO~O_!xO`!xO!a!wO!b!xO~O_!uO`!uO!_!{O!`!uO~O_!xO`!xO!a!{O!b!xO~O`_a!cwz!c~",
    goto: "%o!_PPPPPPPPPPPPPPPPPP!`!fP!lPP!xPP!{#O#R#X#[#_#e#h#k#q#w!`P!`!`P#}$T$k$q$w$}%T%Z%aPPPPPPPP%gX^OX`pXUOX`pezabcde{}!P!R!TR!q!dRhUR!XhXVOX`pRkVR!XkXWOX`pRnWR!XnXXOX`pQrXR!XpXYOX`pQ`ORx`Q{aQ}bQ!PcQ!RdQ!TeZ!e{}!P!R!TQ!v!oR!z!vQ!y!pR!|!yQgUR!VgQjVR!YjQmWR![mQpXR!^pQtZR!`tS_O`ToXp",
    nodeNames: "\u26A0 StartCloseTag StartCloseTag StartCloseTag StartTag StartTag StartTag StartTag StartTag StartCloseTag StartCloseTag StartCloseTag IncompleteCloseTag Document Text EntityReference CharacterReference InvalidEntity Element OpenTag TagName Attribute AttributeName Is AttributeValue UnquotedAttributeValue EndTag ScriptText CloseTag OpenTag StyleText CloseTag OpenTag TextareaText CloseTag OpenTag CloseTag SelfClosingTag Comment ProcessingInst MismatchedCloseTag CloseTag DoctypeDecl",
    maxTerm: 66,
    context: elementContext,
    nodeProps: [
      [NodeProp.closedBy, -11, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, "EndTag", -4, 19, 29, 32, 35, "CloseTag"],
      [NodeProp.group, -9, 12, 15, 16, 17, 18, 38, 39, 40, 41, "Entity", 14, "Entity TextContent", -3, 27, 30, 33, "TextContent Entity"],
      [NodeProp.openedBy, 26, "StartTag StartCloseTag", -4, 28, 31, 34, 36, "OpenTag"]
    ],
    skippedNodes: [0],
    repeatNodeCount: 9,
    tokenData: "!#b!aR!WOX$kXY)sYZ)sZ]$k]^)s^p$kpq)sqr$krs*zsv$kvw+dwx2yx}$k}!O3f!O!P$k!P!Q7_!Q![$k![!]8u!]!^$k!^!_>b!_!`!!p!`!a8T!a!c$k!c!}8u!}#R$k#R#S8u#S#T$k#T#o8u#o$f$k$f$g&R$g%W$k%W%o8u%o%p$k%p&a8u&a&b$k&b1p8u1p4U$k4U4d8u4d4e$k4e$IS8u$IS$I`$k$I`$Ib8u$Ib$Kh$k$Kh%#t8u%#t&/x$k&/x&Et8u&Et&FV$k&FV;'S8u;'S;:j<t;:j?&r$k?&r?Ah8u?Ah?BY$k?BY?Mn8u?Mn~$k!Z$vc^PiW!``!bpOX$kXZ&RZ]$k]^&R^p$kpq&Rqr$krs&qsv$kvw)Rwx'rx!P$k!P!Q&R!Q!^$k!^!_(k!_!a&R!a$f$k$f$g&R$g~$k!R&[V^P!``!bpOr&Rrs&qsv&Rwx'rx!^&R!^!_(k!_~&Rq&xT^P!bpOv&qwx'Xx!^&q!^!_'g!_~&qP'^R^POv'Xw!^'X!_~'Xp'lQ!bpOv'gx~'ga'yU^P!``Or'rrs'Xsv'rw!^'r!^!_(]!_~'r`(bR!``Or(]sv(]w~(]!Q(rT!``!bpOr(krs'gsv(kwx(]x~(kW)WXiWOX)RZ])R^p)Rqr)Rsw)Rx!P)R!Q!^)R!a$f)R$g~)R!a*O^^P!``!bp!]^OX&RXY)sYZ)sZ]&R]^)s^p&Rpq)sqr&Rrs&qsv&Rwx'rx!^&R!^!_(k!_~&R!Z+TT!_h^P!bpOv&qwx'Xx!^&q!^!_'g!_~&q!Z+kbiWaPOX,sXZ.QZ],s]^.Q^p,sqr,srs.Qst/]tw,swx.Qx!P,s!P!Q.Q!Q!],s!]!^)R!^!a.Q!a$f,s$f$g.Q$g~,s!Z,xbiWOX,sXZ.QZ],s]^.Q^p,sqr,srs.Qst)Rtw,swx.Qx!P,s!P!Q.Q!Q!],s!]!^.i!^!a.Q!a$f,s$f$g.Q$g~,s!R.TTOp.Qqs.Qt!].Q!]!^.d!^~.Q!R.iO_!R!Z.pXiW_!ROX)RZ])R^p)Rqr)Rsw)Rx!P)R!Q!^)R!a$f)R$g~)R!Z/baiWOX0gXZ1qZ]0g]^1q^p0gqr0grs1qsw0gwx1qx!P0g!P!Q1q!Q!]0g!]!^)R!^!a1q!a$f0g$f$g1q$g~0g!Z0laiWOX0gXZ1qZ]0g]^1q^p0gqr0grs1qsw0gwx1qx!P0g!P!Q1q!Q!]0g!]!^2V!^!a1q!a$f0g$f$g1q$g~0g!R1tSOp1qq!]1q!]!^2Q!^~1q!R2VO`!R!Z2^XiW`!ROX)RZ])R^p)Rqr)Rsw)Rx!P)R!Q!^)R!a$f)R$g~)R!Z3SU!ax^P!``Or'rrs'Xsv'rw!^'r!^!_(]!_~'r!]3qe^PiW!``!bpOX$kXZ&RZ]$k]^&R^p$kpq&Rqr$krs&qsv$kvw)Rwx'rx}$k}!O5S!O!P$k!P!Q&R!Q!^$k!^!_(k!_!a&R!a$f$k$f$g&R$g~$k!]5_d^PiW!``!bpOX$kXZ&RZ]$k]^&R^p$kpq&Rqr$krs&qsv$kvw)Rwx'rx!P$k!P!Q&R!Q!^$k!^!_(k!_!`&R!`!a6m!a$f$k$f$g&R$g~$k!T6xV^P!``!bp!dQOr&Rrs&qsv&Rwx'rx!^&R!^!_(k!_~&R!X7hX^P!``!bpOr&Rrs&qsv&Rwx'rx!^&R!^!_(k!_!`&R!`!a8T!a~&R!X8`VjU^P!``!bpOr&Rrs&qsv&Rwx'rx!^&R!^!_(k!_~&R!a9U!YfSdQ^PiW!``!bpOX$kXZ&RZ]$k]^&R^p$kpq&Rqr$krs&qsv$kvw)Rwx'rx}$k}!O8u!O!P8u!P!Q&R!Q![8u![!]8u!]!^$k!^!_(k!_!a&R!a!c$k!c!}8u!}#R$k#R#S8u#S#T$k#T#o8u#o$f$k$f$g&R$g$}$k$}%O8u%O%W$k%W%o8u%o%p$k%p&a8u&a&b$k&b1p8u1p4U8u4U4d8u4d4e$k4e$IS8u$IS$I`$k$I`$Ib8u$Ib$Je$k$Je$Jg8u$Jg$Kh$k$Kh%#t8u%#t&/x$k&/x&Et8u&Et&FV$k&FV;'S8u;'S;:j<t;:j?&r$k?&r?Ah8u?Ah?BY$k?BY?Mn8u?Mn~$k!a=Pe^PiW!``!bpOX$kXZ&RZ]$k]^&R^p$kpq&Rqr$krs&qsv$kvw)Rwx'rx!P$k!P!Q&R!Q!^$k!^!_(k!_!a&R!a$f$k$f$g&R$g;=`$k;=`<%l8u<%l~$k!R>iW!``!bpOq(kqr?Rrs'gsv(kwx(]x!a(k!a!bKj!b~(k!R?YZ!``!bpOr(krs'gsv(kwx(]x}(k}!O?{!O!f(k!f!gAR!g#W(k#W#XGz#X~(k!R@SV!``!bpOr(krs'gsv(kwx(]x}(k}!O@i!O~(k!R@rT!``!bp!cPOr(krs'gsv(kwx(]x~(k!RAYV!``!bpOr(krs'gsv(kwx(]x!q(k!q!rAo!r~(k!RAvV!``!bpOr(krs'gsv(kwx(]x!e(k!e!fB]!f~(k!RBdV!``!bpOr(krs'gsv(kwx(]x!v(k!v!wBy!w~(k!RCQV!``!bpOr(krs'gsv(kwx(]x!{(k!{!|Cg!|~(k!RCnV!``!bpOr(krs'gsv(kwx(]x!r(k!r!sDT!s~(k!RD[V!``!bpOr(krs'gsv(kwx(]x!g(k!g!hDq!h~(k!RDxW!``!bpOrDqrsEbsvDqvwEvwxFfx!`Dq!`!aGb!a~DqqEgT!bpOvEbvxEvx!`Eb!`!aFX!a~EbPEyRO!`Ev!`!aFS!a~EvPFXOzPqF`Q!bpzPOv'gx~'gaFkV!``OrFfrsEvsvFfvwEvw!`Ff!`!aGQ!a~FfaGXR!``zPOr(]sv(]w~(]!RGkT!``!bpzPOr(krs'gsv(kwx(]x~(k!RHRV!``!bpOr(krs'gsv(kwx(]x#c(k#c#dHh#d~(k!RHoV!``!bpOr(krs'gsv(kwx(]x#V(k#V#WIU#W~(k!RI]V!``!bpOr(krs'gsv(kwx(]x#h(k#h#iIr#i~(k!RIyV!``!bpOr(krs'gsv(kwx(]x#m(k#m#nJ`#n~(k!RJgV!``!bpOr(krs'gsv(kwx(]x#d(k#d#eJ|#e~(k!RKTV!``!bpOr(krs'gsv(kwx(]x#X(k#X#YDq#Y~(k!RKqW!``!bpOrKjrsLZsvKjvwLowxNPx!aKj!a!b! g!b~KjqL`T!bpOvLZvxLox!aLZ!a!bM^!b~LZPLrRO!aLo!a!bL{!b~LoPMORO!`Lo!`!aMX!a~LoPM^OwPqMcT!bpOvLZvxLox!`LZ!`!aMr!a~LZqMyQ!bpwPOv'gx~'gaNUV!``OrNPrsLosvNPvwLow!aNP!a!bNk!b~NPaNpV!``OrNPrsLosvNPvwLow!`NP!`!a! V!a~NPa! ^R!``wPOr(]sv(]w~(]!R! nW!``!bpOrKjrsLZsvKjvwLowxNPx!`Kj!`!a!!W!a~Kj!R!!aT!``!bpwPOr(krs'gsv(kwx(]x~(k!V!!{VgS^P!``!bpOr&Rrs&qsv&Rwx'rx!^&R!^!_(k!_~&R",
    tokenizers: [scriptTokens, styleTokens, textareaTokens, tagStart, commentContent, 0, 1, 2, 3, 4, 5],
    topRules: { "Document": [0, 13] },
    dialects: { noMatch: 0 },
    tokenPrec: 464
  });
  function getAttrs(element, input) {
    let attrs = /* @__PURE__ */ Object.create(null);
    for (let att of element.firstChild.getChildren("Attribute")) {
      let name2 = att.getChild("AttributeName"), value = att.getChild("AttributeValue") || att.getChild("UnquotedAttributeValue");
      if (name2)
        attrs[input.read(name2.from, name2.to)] = !value ? "" : value.name == "AttributeValue" ? input.read(value.from + 1, value.to - 1) : input.read(value.from, value.to);
    }
    return attrs;
  }
  function maybeNest(node, input, tags3) {
    let attrs;
    for (let tag of tags3) {
      if (!tag.attrs || tag.attrs(attrs || (attrs = getAttrs(node.node.parent, input))))
        return { parser: tag.parser };
    }
    return null;
  }
  function configureNesting(tags3) {
    let script = [], style = [], textarea = [];
    for (let tag of tags3) {
      let array2 = tag.tag == "script" ? script : tag.tag == "style" ? style : tag.tag == "textarea" ? textarea : null;
      if (!array2)
        throw new RangeError("Only script, style, and textarea tags can host nested parsers");
      array2.push(tag);
    }
    return parseMixed((node, input) => {
      let id2 = node.type.id;
      if (id2 == ScriptText)
        return maybeNest(node, input, script);
      if (id2 == StyleText)
        return maybeNest(node, input, style);
      if (id2 == TextareaText)
        return maybeNest(node, input, textarea);
      return null;
    });
  }

  // node_modules/@lezer/css/dist/index.es.js
  var descendantOp = 93;
  var Unit = 1;
  var callee = 94;
  var identifier = 95;
  var VariableName = 2;
  var space2 = [
    9,
    10,
    11,
    12,
    13,
    32,
    133,
    160,
    5760,
    8192,
    8193,
    8194,
    8195,
    8196,
    8197,
    8198,
    8199,
    8200,
    8201,
    8202,
    8232,
    8233,
    8239,
    8287,
    12288
  ];
  var colon = 58;
  var parenL = 40;
  var underscore = 95;
  var bracketL = 91;
  var dash = 45;
  var period = 46;
  var hash = 35;
  var percent = 37;
  function isAlpha(ch) {
    return ch >= 65 && ch <= 90 || ch >= 97 && ch <= 122 || ch >= 161;
  }
  function isDigit(ch) {
    return ch >= 48 && ch <= 57;
  }
  var identifiers = new ExternalTokenizer((input, stack) => {
    for (let inside2 = false, dashes = 0, i = 0; ; i++) {
      let { next } = input;
      if (isAlpha(next) || next == dash || next == underscore || inside2 && isDigit(next)) {
        if (!inside2 && (next != dash || i > 0))
          inside2 = true;
        if (dashes === i && next == dash)
          dashes++;
        input.advance();
      } else {
        if (inside2)
          input.acceptToken(next == parenL ? callee : dashes == 2 && stack.canShift(VariableName) ? VariableName : identifier);
        break;
      }
    }
  });
  var descendant = new ExternalTokenizer((input) => {
    if (space2.includes(input.peek(-1))) {
      let { next } = input;
      if (isAlpha(next) || next == underscore || next == hash || next == period || next == bracketL || next == colon || next == dash)
        input.acceptToken(descendantOp);
    }
  });
  var unitToken = new ExternalTokenizer((input) => {
    if (!space2.includes(input.peek(-1))) {
      let { next } = input;
      if (next == percent) {
        input.advance();
        input.acceptToken(Unit);
      }
      if (isAlpha(next)) {
        do {
          input.advance();
        } while (isAlpha(input.next));
        input.acceptToken(Unit);
      }
    }
  });
  var spec_callee = { __proto__: null, lang: 32, "nth-child": 32, "nth-last-child": 32, "nth-of-type": 32, dir: 32, url: 60, "url-prefix": 60, domain: 60, regexp: 60, selector: 134 };
  var spec_AtKeyword = { __proto__: null, "@import": 114, "@media": 138, "@charset": 142, "@namespace": 146, "@keyframes": 152, "@supports": 164 };
  var spec_identifier2 = { __proto__: null, not: 128, only: 128, from: 158, to: 160 };
  var parser5 = LRParser.deserialize({
    version: 13,
    states: "7WOYQ[OOOOQP'#Cd'#CdOOQP'#Cc'#CcO!ZQ[O'#CfO!}QXO'#CaO#UQ[O'#ChO#aQ[O'#DPO#fQ[O'#DTOOQP'#Ec'#EcO#kQdO'#DeO$VQ[O'#DrO#kQdO'#DtO$hQ[O'#DvO$sQ[O'#DyO$xQ[O'#EPO%WQ[O'#EROOQS'#Eb'#EbOOQS'#ES'#ESQYQ[OOOOQP'#Cg'#CgOOQP,59Q,59QO!ZQ[O,59QO%_Q[O'#EVO%yQWO,58{O&RQ[O,59SO#aQ[O,59kO#fQ[O,59oO%_Q[O,59sO%_Q[O,59uO%_Q[O,59vO'bQ[O'#D`OOQS,58{,58{OOQP'#Ck'#CkOOQO'#C}'#C}OOQP,59S,59SO'iQWO,59SO'nQWO,59SOOQP'#DR'#DROOQP,59k,59kOOQO'#DV'#DVO'sQ`O,59oOOQS'#Cp'#CpO#kQdO'#CqO'{QvO'#CsO)VQtO,5:POOQO'#Cx'#CxO'iQWO'#CwO)kQWO'#CyOOQS'#Ef'#EfOOQO'#Dh'#DhO)pQ[O'#DoO*OQWO'#EiO$xQ[O'#DmO*^QWO'#DpOOQO'#Ej'#EjO%|QWO,5:^O*cQpO,5:`OOQS'#Dx'#DxO*kQWO,5:bO*pQ[O,5:bOOQO'#D{'#D{O*xQWO,5:eO*}QWO,5:kO+VQWO,5:mOOQS-E8Q-E8QOOQP1G.l1G.lO+yQXO,5:qOOQO-E8T-E8TOOQS1G.g1G.gOOQP1G.n1G.nO'iQWO1G.nO'nQWO1G.nOOQP1G/V1G/VO,WQ`O1G/ZO,qQXO1G/_O-XQXO1G/aO-oQXO1G/bO.VQXO'#CdO.zQWO'#DaOOQS,59z,59zO/PQWO,59zO/XQ[O,59zO/`QdO'#CoO/gQ[O'#DOOOQP1G/Z1G/ZO#kQdO1G/ZO/nQpO,59]OOQS,59_,59_O#kQdO,59aO/vQWO1G/kOOQS,59c,59cO/{Q!bO,59eO0TQWO'#DhO0`QWO,5:TO0eQWO,5:ZO$xQ[O,5:VO$xQ[O'#EYO0mQWO,5;TO0xQWO,5:XO%_Q[O,5:[OOQS1G/x1G/xOOQS1G/z1G/zOOQS1G/|1G/|O1ZQWO1G/|O1`QdO'#D|OOQS1G0P1G0POOQS1G0V1G0VOOQS1G0X1G0XOOQP7+$Y7+$YOOQP7+$u7+$uO#kQdO7+$uO#kQdO,59{O1nQ[O'#EXO1xQWO1G/fOOQS1G/f1G/fO1xQWO1G/fO2QQtO'#ETO2uQdO'#EeO3PQWO,59ZO3UQXO'#EhO3]QWO,59jO3bQpO7+$uOOQS1G.w1G.wOOQS1G.{1G.{OOQS7+%V7+%VO3jQWO1G/PO#kQdO1G/oOOQO1G/u1G/uOOQO1G/q1G/qO3oQWO,5:tOOQO-E8W-E8WO3}QXO1G/vOOQS7+%h7+%hO4UQYO'#CsO%|QWO'#EZO4^QdO,5:hOOQS,5:h,5:hO4lQpO<<HaO4tQtO1G/gOOQO,5:s,5:sO5XQ[O,5:sOOQO-E8V-E8VOOQS7+%Q7+%QO5cQWO7+%QOOQS-E8R-E8RO#kQdO'#EUO5kQWO,5;POOQT1G.u1G.uO5sQWO,5;SOOQP1G/U1G/UOOQP<<Ha<<HaOOQS7+$k7+$kO5{QdO7+%ZOOQO7+%b7+%bOOQS,5:u,5:uOOQS-E8X-E8XOOQS1G0S1G0SOOQPAN={AN={O6SQtO'#EWO#kQdO'#EWO6}QdO7+%ROOQO7+%R7+%ROOQO1G0_1G0_OOQS<<Hl<<HlO7_QdO,5:pOOQO-E8S-E8SOOQO<<Hu<<HuO7iQtO,5:rOOQS-E8U-E8UOOQO<<Hm<<Hm",
    stateData: "8j~O#TOSROS~OUWOXWO]TO^TOtUOxVO!Y_O!ZXO!gYO!iZO!k[O!n]O!t^O#RPO#WRO~O#RcO~O]hO^hOpfOtiOxjO|kO!PmO#PlO#WeO~O!RnO~P!`O`sO#QqO#RpO~O#RuO~O#RwO~OQ!QObzOf!QOh!QOn!PO#Q}O#RyO#Z{O~Ob!SO!b!UO!e!VO#R!RO!R#]P~Oh![On!PO#R!ZO~O#R!^O~Ob!SO!b!UO!e!VO#R!RO~O!W#]P~P$VOUWOXWO]TO^TOtUOxVO#RPO#WRO~OpfO!RnO~O`!hO#QqO#RpO~OQ!pOUWOXWO]TO^TOtUOxVO!Y_O!ZXO!gYO!iZO!k[O!n]O!t^O#R!oO#WRO~O!Q!qO~P&^Ob!tO~Ob!uO~Ov!vOz!wO~OP!yObgXjgX!WgX!bgX!egX#RgXagXQgXfgXhgXngXpgX#QgX#ZgXvgX!QgX!VgX~Ob!SOj!zO!b!UO!e!VO#R!RO!W#]P~Ob!}O~Ob!SO!b!UO!e!VO#R#OO~Op#SO!`#RO!R#]X!W#]X~Ob#VO~Oj!zO!W#XO~O!W#YO~Oh#ZOn!PO~O!R#[O~O!RnO!`#RO~O!RnO!W#_O~O]hO^hOtiOxjO|kO!PmO#PlO#WeO~Op!ya!R!yaa!ya~P+_Ov#aOz#bO~O]hO^hOtiOxjO#WeO~Op{i|{i!P{i!R{i#P{ia{i~P,`Op}i|}i!P}i!R}i#P}ia}i~P,`Op!Oi|!Oi!P!Oi!R!Oi#P!Oia!Oi~P,`O]WX]!UX^WXpWXtWXxWX|WX!PWX!RWX#PWX#WWX~O]#cO~O!Q#fO!W#dO~O!Q#fO~P&^Oa#XP~P#kOa#[P~P%_Oa#nOj!zO~O!W#pO~Oh#qOo#qO~O]!^Xa![X!`![X~O]#rO~Oa#sO!`#RO~Op#SO!R#]a!W#]a~O!`#ROp!aa!R!aa!W!aaa!aa~O!W#xO~O!Q#|O!q#zO!r#zO#Z#yO~O!Q!{X!W!{X~P&^O!Q$SO!W#dO~Oj!zOQ!wXa!wXb!wXf!wXh!wXn!wXp!wX#Q!wX#R!wX#Z!wX~Op$VOa#XX~P#kOa$XO~Oa#[X~P!`Oa$ZO~Oj!zOv$[O~Oa$]O~O!`#ROp!|a!R!|a!W!|a~Oa$_O~P+_OP!yO!RgX~O!Q$bO!q#zO!r#zO#Z#yO~Oj!zOv$cO~Oj!zOp$eO!V$gO!Q!Ti!W!Ti~P#kO!Q!{a!W!{a~P&^O!Q$iO!W#dO~Op$VOa#Xa~OpfOa#[a~Oa$lO~P#kOj!zOQ!zXb!zXf!zXh!zXn!zXp!zX!Q!zX!V!zX!W!zX#Q!zX#R!zX#Z!zX~Op$eO!V$oO!Q!Tq!W!Tq~P#kOa!xap!xa~P#kOj!zOQ!zab!zaf!zah!zan!zap!za!Q!za!V!za!W!za#Q!za#R!za#Z!za~Oo#Zj!Pj~",
    goto: ",O#_PPPPP#`P#h#vP#h$U#hPP$[PPP$b$k$kP$}P$kP$k%e%wPPP&a&g#hP&mP#hP&sP#hP#h#hPPP&y']'iPP#`PP'o'o'y'oP'oP'o'oP#`P#`P#`P'|#`P(P(SPP#`P#`(V(e(s(y)T)Z)e)kPPPPPP)q)yP*e*hP+^+a+j]`Obn!s#d$QiWObfklmn!s!u#V#d$QiQObfklmn!s!u#V#d$QQdRR!ceQrTR!ghQ!gsQ!|!OR#`!hq!QXZz!t!w!z#b#c#i#r$O$V$^$e$f$jp!QXZz!t!w!z#b#c#i#r$O$V$^$e$f$jT#z#[#{q!OXZz!t!w!z#b#c#i#r$O$V$^$e$f$jp!QXZz!t!w!z#b#c#i#r$O$V$^$e$f$jQ![[R#Z!]QtTR!ihQ!gtR#`!iQvUR!jiQxVR!kjQoSQ!fgQ#W!XQ#^!`Q#_!aR$`#zQ!rnQ#g!sQ$P#dR$h$QX!pn!s#d$Qa!WY^_|!S!U#R#SR#P!SR!][R!_]R#]!_QbOU!bb!s$QQ!snR$Q#dQ#i!tU$U#i$^$jQ$^#rR$j$VQ$W#iR$k$WQgSS!eg$YR$Y#kQ$f$OR$n$fQ#e!rS$R#e$TR$T#gQ#T!TR#v#TQ#{#[R$a#{]aObn!s#d$Q[SObn!s#d$QQ!dfQ!lkQ!mlQ!nmQ#k!uR#w#VR#j!tQ|XQ!YZQ!xz[#h!t#i#r$V$^$jQ#m!wQ#o!zQ#}#bQ$O#cS$d$O$fR$m$eR#l!uQ!XYQ!a_R!{|U!TY_|Q!`^Q#Q!SQ#U!UQ#t#RR#u#S",
    nodeNames: "\u26A0 Unit VariableName Comment StyleSheet RuleSet UniversalSelector TagSelector TagName NestingSelector ClassSelector ClassName PseudoClassSelector : :: PseudoClassName PseudoClassName ) ( ArgList ValueName ParenthesizedValue ColorLiteral NumberLiteral StringLiteral BinaryExpression BinOp CallExpression Callee CallLiteral CallTag ParenthesizedContent , PseudoClassName ArgList IdSelector # IdName ] AttributeSelector [ AttributeName MatchOp ChildSelector ChildOp DescendantSelector SiblingSelector SiblingOp } { Block Declaration PropertyName Important ; ImportStatement AtKeyword import KeywordQuery FeatureQuery FeatureName BinaryQuery LogicOp UnaryQuery UnaryQueryOp ParenthesizedQuery SelectorQuery selector MediaStatement media CharsetStatement charset NamespaceStatement namespace NamespaceName KeyframesStatement keyframes KeyframeName KeyframeList from to SupportsStatement supports AtRule",
    maxTerm: 106,
    nodeProps: [
      [NodeProp.openedBy, 17, "(", 48, "{"],
      [NodeProp.closedBy, 18, ")", 49, "}"]
    ],
    skippedNodes: [0, 3],
    repeatNodeCount: 8,
    tokenData: "Ay~R![OX$wX^%]^p$wpq%]qr(crs+}st,otu2Uuv$wvw2rwx2}xy3jyz3uz{3z{|4_|}8U}!O8a!O!P8x!P!Q9Z!Q![;e![!]<Y!]!^<x!^!_$w!_!`=T!`!a=`!a!b$w!b!c>O!c!}$w!}#O?[#O#P$w#P#Q?g#Q#R2U#R#T$w#T#U?r#U#c$w#c#d@q#d#o$w#o#pAQ#p#q2U#q#rA]#r#sAh#s#y$w#y#z%]#z$f$w$f$g%]$g#BY$w#BY#BZ%]#BZ$IS$w$IS$I_%]$I_$I|$w$I|$JO%]$JO$JT$w$JT$JU%]$JU$KV$w$KV$KW%]$KW&FU$w&FU&FV%]&FV~$wW$zQOy%Qz~%QW%VQoWOy%Qz~%Q~%bf#T~OX%QX^&v^p%Qpq&vqy%Qz#y%Q#y#z&v#z$f%Q$f$g&v$g#BY%Q#BY#BZ&v#BZ$IS%Q$IS$I_&v$I_$I|%Q$I|$JO&v$JO$JT%Q$JT$JU&v$JU$KV%Q$KV$KW&v$KW&FU%Q&FU&FV&v&FV~%Q~&}f#T~oWOX%QX^&v^p%Qpq&vqy%Qz#y%Q#y#z&v#z$f%Q$f$g&v$g#BY%Q#BY#BZ&v#BZ$IS%Q$IS$I_&v$I_$I|%Q$I|$JO&v$JO$JT%Q$JT$JU&v$JU$KV%Q$KV$KW&v$KW&FU%Q&FU&FV&v&FV~%Q^(fSOy%Qz#]%Q#]#^(r#^~%Q^(wSoWOy%Qz#a%Q#a#b)T#b~%Q^)YSoWOy%Qz#d%Q#d#e)f#e~%Q^)kSoWOy%Qz#c%Q#c#d)w#d~%Q^)|SoWOy%Qz#f%Q#f#g*Y#g~%Q^*_SoWOy%Qz#h%Q#h#i*k#i~%Q^*pSoWOy%Qz#T%Q#T#U*|#U~%Q^+RSoWOy%Qz#b%Q#b#c+_#c~%Q^+dSoWOy%Qz#h%Q#h#i+p#i~%Q^+wQ!VUoWOy%Qz~%Q~,QUOY+}Zr+}rs,ds#O+}#O#P,i#P~+}~,iOh~~,lPO~+}_,tWtPOy%Qz!Q%Q!Q![-^![!c%Q!c!i-^!i#T%Q#T#Z-^#Z~%Q^-cWoWOy%Qz!Q%Q!Q![-{![!c%Q!c!i-{!i#T%Q#T#Z-{#Z~%Q^.QWoWOy%Qz!Q%Q!Q![.j![!c%Q!c!i.j!i#T%Q#T#Z.j#Z~%Q^.qWfUoWOy%Qz!Q%Q!Q![/Z![!c%Q!c!i/Z!i#T%Q#T#Z/Z#Z~%Q^/bWfUoWOy%Qz!Q%Q!Q![/z![!c%Q!c!i/z!i#T%Q#T#Z/z#Z~%Q^0PWoWOy%Qz!Q%Q!Q![0i![!c%Q!c!i0i!i#T%Q#T#Z0i#Z~%Q^0pWfUoWOy%Qz!Q%Q!Q![1Y![!c%Q!c!i1Y!i#T%Q#T#Z1Y#Z~%Q^1_WoWOy%Qz!Q%Q!Q![1w![!c%Q!c!i1w!i#T%Q#T#Z1w#Z~%Q^2OQfUoWOy%Qz~%QY2XSOy%Qz!_%Q!_!`2e!`~%QY2lQzQoWOy%Qz~%QX2wQXPOy%Qz~%Q~3QUOY2}Zw2}wx,dx#O2}#O#P3d#P~2}~3gPO~2}_3oQbVOy%Qz~%Q~3zOa~_4RSUPjSOy%Qz!_%Q!_!`2e!`~%Q_4fUjS!PPOy%Qz!O%Q!O!P4x!P!Q%Q!Q![7_![~%Q^4}SoWOy%Qz!Q%Q!Q![5Z![~%Q^5bWoW#ZUOy%Qz!Q%Q!Q![5Z![!g%Q!g!h5z!h#X%Q#X#Y5z#Y~%Q^6PWoWOy%Qz{%Q{|6i|}%Q}!O6i!O!Q%Q!Q![6z![~%Q^6nSoWOy%Qz!Q%Q!Q![6z![~%Q^7RSoW#ZUOy%Qz!Q%Q!Q![6z![~%Q^7fYoW#ZUOy%Qz!O%Q!O!P5Z!P!Q%Q!Q![7_![!g%Q!g!h5z!h#X%Q#X#Y5z#Y~%Q_8ZQpVOy%Qz~%Q^8fUjSOy%Qz!O%Q!O!P4x!P!Q%Q!Q![7_![~%Q_8}S#WPOy%Qz!Q%Q!Q![5Z![~%Q~9`RjSOy%Qz{9i{~%Q~9nSoWOy9iyz9zz{:o{~9i~9}ROz9zz{:W{~9z~:ZTOz9zz{:W{!P9z!P!Q:j!Q~9z~:oOR~~:tUoWOy9iyz9zz{:o{!P9i!P!Q;W!Q~9i~;_QR~oWOy%Qz~%Q^;jY#ZUOy%Qz!O%Q!O!P5Z!P!Q%Q!Q![7_![!g%Q!g!h5z!h#X%Q#X#Y5z#Y~%QX<_S]POy%Qz![%Q![!]<k!]~%QX<rQ^PoWOy%Qz~%Q_<}Q!WVOy%Qz~%QY=YQzQOy%Qz~%QX=eS|POy%Qz!`%Q!`!a=q!a~%QX=xQ|PoWOy%Qz~%QX>RUOy%Qz!c%Q!c!}>e!}#T%Q#T#o>e#o~%QX>lY!YPoWOy%Qz}%Q}!O>e!O!Q%Q!Q![>e![!c%Q!c!}>e!}#T%Q#T#o>e#o~%QX?aQxPOy%Qz~%Q^?lQvUOy%Qz~%QX?uSOy%Qz#b%Q#b#c@R#c~%QX@WSoWOy%Qz#W%Q#W#X@d#X~%QX@kQ!`PoWOy%Qz~%QX@tSOy%Qz#f%Q#f#g@d#g~%QXAVQ!RPOy%Qz~%Q_AbQ!QVOy%Qz~%QZAmS!PPOy%Qz!_%Q!_!`2e!`~%Q",
    tokenizers: [descendant, unitToken, identifiers, 0, 1, 2, 3],
    topRules: { "StyleSheet": [0, 4] },
    specialized: [{ term: 94, get: (value) => spec_callee[value] || -1 }, { term: 56, get: (value) => spec_AtKeyword[value] || -1 }, { term: 95, get: (value) => spec_identifier2[value] || -1 }],
    tokenPrec: 1078
  });

  // node_modules/@codemirror/lang-css/dist/index.js
  var _properties = null;
  function properties() {
    if (!_properties && typeof document == "object" && document.body) {
      let names = [];
      for (let prop in document.body.style) {
        if (!/[A-Z]|^-|^(item|length)$/.test(prop))
          names.push(prop);
      }
      _properties = names.sort().map((name2) => ({ type: "property", label: name2 }));
    }
    return _properties || [];
  }
  var pseudoClasses = /* @__PURE__ */ [
    "active",
    "after",
    "before",
    "checked",
    "default",
    "disabled",
    "empty",
    "enabled",
    "first-child",
    "first-letter",
    "first-line",
    "first-of-type",
    "focus",
    "hover",
    "in-range",
    "indeterminate",
    "invalid",
    "lang",
    "last-child",
    "last-of-type",
    "link",
    "not",
    "nth-child",
    "nth-last-child",
    "nth-last-of-type",
    "nth-of-type",
    "only-of-type",
    "only-child",
    "optional",
    "out-of-range",
    "placeholder",
    "read-only",
    "read-write",
    "required",
    "root",
    "selection",
    "target",
    "valid",
    "visited"
  ].map((name2) => ({ type: "class", label: name2 }));
  var values = /* @__PURE__ */ [
    "above",
    "absolute",
    "activeborder",
    "additive",
    "activecaption",
    "after-white-space",
    "ahead",
    "alias",
    "all",
    "all-scroll",
    "alphabetic",
    "alternate",
    "always",
    "antialiased",
    "appworkspace",
    "asterisks",
    "attr",
    "auto",
    "auto-flow",
    "avoid",
    "avoid-column",
    "avoid-page",
    "avoid-region",
    "axis-pan",
    "background",
    "backwards",
    "baseline",
    "below",
    "bidi-override",
    "blink",
    "block",
    "block-axis",
    "bold",
    "bolder",
    "border",
    "border-box",
    "both",
    "bottom",
    "break",
    "break-all",
    "break-word",
    "bullets",
    "button",
    "button-bevel",
    "buttonface",
    "buttonhighlight",
    "buttonshadow",
    "buttontext",
    "calc",
    "capitalize",
    "caps-lock-indicator",
    "caption",
    "captiontext",
    "caret",
    "cell",
    "center",
    "checkbox",
    "circle",
    "cjk-decimal",
    "clear",
    "clip",
    "close-quote",
    "col-resize",
    "collapse",
    "color",
    "color-burn",
    "color-dodge",
    "column",
    "column-reverse",
    "compact",
    "condensed",
    "contain",
    "content",
    "contents",
    "content-box",
    "context-menu",
    "continuous",
    "copy",
    "counter",
    "counters",
    "cover",
    "crop",
    "cross",
    "crosshair",
    "currentcolor",
    "cursive",
    "cyclic",
    "darken",
    "dashed",
    "decimal",
    "decimal-leading-zero",
    "default",
    "default-button",
    "dense",
    "destination-atop",
    "destination-in",
    "destination-out",
    "destination-over",
    "difference",
    "disc",
    "discard",
    "disclosure-closed",
    "disclosure-open",
    "document",
    "dot-dash",
    "dot-dot-dash",
    "dotted",
    "double",
    "down",
    "e-resize",
    "ease",
    "ease-in",
    "ease-in-out",
    "ease-out",
    "element",
    "ellipse",
    "ellipsis",
    "embed",
    "end",
    "ethiopic-abegede-gez",
    "ethiopic-halehame-aa-er",
    "ethiopic-halehame-gez",
    "ew-resize",
    "exclusion",
    "expanded",
    "extends",
    "extra-condensed",
    "extra-expanded",
    "fantasy",
    "fast",
    "fill",
    "fill-box",
    "fixed",
    "flat",
    "flex",
    "flex-end",
    "flex-start",
    "footnotes",
    "forwards",
    "from",
    "geometricPrecision",
    "graytext",
    "grid",
    "groove",
    "hand",
    "hard-light",
    "help",
    "hidden",
    "hide",
    "higher",
    "highlight",
    "highlighttext",
    "horizontal",
    "hsl",
    "hsla",
    "hue",
    "icon",
    "ignore",
    "inactiveborder",
    "inactivecaption",
    "inactivecaptiontext",
    "infinite",
    "infobackground",
    "infotext",
    "inherit",
    "initial",
    "inline",
    "inline-axis",
    "inline-block",
    "inline-flex",
    "inline-grid",
    "inline-table",
    "inset",
    "inside",
    "intrinsic",
    "invert",
    "italic",
    "justify",
    "keep-all",
    "landscape",
    "large",
    "larger",
    "left",
    "level",
    "lighter",
    "lighten",
    "line-through",
    "linear",
    "linear-gradient",
    "lines",
    "list-item",
    "listbox",
    "listitem",
    "local",
    "logical",
    "loud",
    "lower",
    "lower-hexadecimal",
    "lower-latin",
    "lower-norwegian",
    "lowercase",
    "ltr",
    "luminosity",
    "manipulation",
    "match",
    "matrix",
    "matrix3d",
    "medium",
    "menu",
    "menutext",
    "message-box",
    "middle",
    "min-intrinsic",
    "mix",
    "monospace",
    "move",
    "multiple",
    "multiple_mask_images",
    "multiply",
    "n-resize",
    "narrower",
    "ne-resize",
    "nesw-resize",
    "no-close-quote",
    "no-drop",
    "no-open-quote",
    "no-repeat",
    "none",
    "normal",
    "not-allowed",
    "nowrap",
    "ns-resize",
    "numbers",
    "numeric",
    "nw-resize",
    "nwse-resize",
    "oblique",
    "opacity",
    "open-quote",
    "optimizeLegibility",
    "optimizeSpeed",
    "outset",
    "outside",
    "outside-shape",
    "overlay",
    "overline",
    "padding",
    "padding-box",
    "painted",
    "page",
    "paused",
    "perspective",
    "pinch-zoom",
    "plus-darker",
    "plus-lighter",
    "pointer",
    "polygon",
    "portrait",
    "pre",
    "pre-line",
    "pre-wrap",
    "preserve-3d",
    "progress",
    "push-button",
    "radial-gradient",
    "radio",
    "read-only",
    "read-write",
    "read-write-plaintext-only",
    "rectangle",
    "region",
    "relative",
    "repeat",
    "repeating-linear-gradient",
    "repeating-radial-gradient",
    "repeat-x",
    "repeat-y",
    "reset",
    "reverse",
    "rgb",
    "rgba",
    "ridge",
    "right",
    "rotate",
    "rotate3d",
    "rotateX",
    "rotateY",
    "rotateZ",
    "round",
    "row",
    "row-resize",
    "row-reverse",
    "rtl",
    "run-in",
    "running",
    "s-resize",
    "sans-serif",
    "saturation",
    "scale",
    "scale3d",
    "scaleX",
    "scaleY",
    "scaleZ",
    "screen",
    "scroll",
    "scrollbar",
    "scroll-position",
    "se-resize",
    "self-start",
    "self-end",
    "semi-condensed",
    "semi-expanded",
    "separate",
    "serif",
    "show",
    "single",
    "skew",
    "skewX",
    "skewY",
    "skip-white-space",
    "slide",
    "slider-horizontal",
    "slider-vertical",
    "sliderthumb-horizontal",
    "sliderthumb-vertical",
    "slow",
    "small",
    "small-caps",
    "small-caption",
    "smaller",
    "soft-light",
    "solid",
    "source-atop",
    "source-in",
    "source-out",
    "source-over",
    "space",
    "space-around",
    "space-between",
    "space-evenly",
    "spell-out",
    "square",
    "start",
    "static",
    "status-bar",
    "stretch",
    "stroke",
    "stroke-box",
    "sub",
    "subpixel-antialiased",
    "svg_masks",
    "super",
    "sw-resize",
    "symbolic",
    "symbols",
    "system-ui",
    "table",
    "table-caption",
    "table-cell",
    "table-column",
    "table-column-group",
    "table-footer-group",
    "table-header-group",
    "table-row",
    "table-row-group",
    "text",
    "text-bottom",
    "text-top",
    "textarea",
    "textfield",
    "thick",
    "thin",
    "threeddarkshadow",
    "threedface",
    "threedhighlight",
    "threedlightshadow",
    "threedshadow",
    "to",
    "top",
    "transform",
    "translate",
    "translate3d",
    "translateX",
    "translateY",
    "translateZ",
    "transparent",
    "ultra-condensed",
    "ultra-expanded",
    "underline",
    "unidirectional-pan",
    "unset",
    "up",
    "upper-latin",
    "uppercase",
    "url",
    "var",
    "vertical",
    "vertical-text",
    "view-box",
    "visible",
    "visibleFill",
    "visiblePainted",
    "visibleStroke",
    "visual",
    "w-resize",
    "wait",
    "wave",
    "wider",
    "window",
    "windowframe",
    "windowtext",
    "words",
    "wrap",
    "wrap-reverse",
    "x-large",
    "x-small",
    "xor",
    "xx-large",
    "xx-small"
  ].map((name2) => ({ type: "keyword", label: name2 })).concat(/* @__PURE__ */ [
    "aliceblue",
    "antiquewhite",
    "aqua",
    "aquamarine",
    "azure",
    "beige",
    "bisque",
    "black",
    "blanchedalmond",
    "blue",
    "blueviolet",
    "brown",
    "burlywood",
    "cadetblue",
    "chartreuse",
    "chocolate",
    "coral",
    "cornflowerblue",
    "cornsilk",
    "crimson",
    "cyan",
    "darkblue",
    "darkcyan",
    "darkgoldenrod",
    "darkgray",
    "darkgreen",
    "darkkhaki",
    "darkmagenta",
    "darkolivegreen",
    "darkorange",
    "darkorchid",
    "darkred",
    "darksalmon",
    "darkseagreen",
    "darkslateblue",
    "darkslategray",
    "darkturquoise",
    "darkviolet",
    "deeppink",
    "deepskyblue",
    "dimgray",
    "dodgerblue",
    "firebrick",
    "floralwhite",
    "forestgreen",
    "fuchsia",
    "gainsboro",
    "ghostwhite",
    "gold",
    "goldenrod",
    "gray",
    "grey",
    "green",
    "greenyellow",
    "honeydew",
    "hotpink",
    "indianred",
    "indigo",
    "ivory",
    "khaki",
    "lavender",
    "lavenderblush",
    "lawngreen",
    "lemonchiffon",
    "lightblue",
    "lightcoral",
    "lightcyan",
    "lightgoldenrodyellow",
    "lightgray",
    "lightgreen",
    "lightpink",
    "lightsalmon",
    "lightseagreen",
    "lightskyblue",
    "lightslategray",
    "lightsteelblue",
    "lightyellow",
    "lime",
    "limegreen",
    "linen",
    "magenta",
    "maroon",
    "mediumaquamarine",
    "mediumblue",
    "mediumorchid",
    "mediumpurple",
    "mediumseagreen",
    "mediumslateblue",
    "mediumspringgreen",
    "mediumturquoise",
    "mediumvioletred",
    "midnightblue",
    "mintcream",
    "mistyrose",
    "moccasin",
    "navajowhite",
    "navy",
    "oldlace",
    "olive",
    "olivedrab",
    "orange",
    "orangered",
    "orchid",
    "palegoldenrod",
    "palegreen",
    "paleturquoise",
    "palevioletred",
    "papayawhip",
    "peachpuff",
    "peru",
    "pink",
    "plum",
    "powderblue",
    "purple",
    "rebeccapurple",
    "red",
    "rosybrown",
    "royalblue",
    "saddlebrown",
    "salmon",
    "sandybrown",
    "seagreen",
    "seashell",
    "sienna",
    "silver",
    "skyblue",
    "slateblue",
    "slategray",
    "snow",
    "springgreen",
    "steelblue",
    "tan",
    "teal",
    "thistle",
    "tomato",
    "turquoise",
    "violet",
    "wheat",
    "white",
    "whitesmoke",
    "yellow",
    "yellowgreen"
  ].map((name2) => ({ type: "constant", label: name2 })));
  var tags2 = /* @__PURE__ */ [
    "a",
    "abbr",
    "address",
    "article",
    "aside",
    "b",
    "bdi",
    "bdo",
    "blockquote",
    "body",
    "br",
    "button",
    "canvas",
    "caption",
    "cite",
    "code",
    "col",
    "colgroup",
    "dd",
    "del",
    "details",
    "dfn",
    "dialog",
    "div",
    "dl",
    "dt",
    "em",
    "figcaption",
    "figure",
    "footer",
    "form",
    "header",
    "hgroup",
    "h1",
    "h2",
    "h3",
    "h4",
    "h5",
    "h6",
    "hr",
    "html",
    "i",
    "iframe",
    "img",
    "input",
    "ins",
    "kbd",
    "label",
    "legend",
    "li",
    "main",
    "meter",
    "nav",
    "ol",
    "output",
    "p",
    "pre",
    "ruby",
    "section",
    "select",
    "small",
    "source",
    "span",
    "strong",
    "sub",
    "summary",
    "sup",
    "table",
    "tbody",
    "td",
    "template",
    "textarea",
    "tfoot",
    "th",
    "thead",
    "tr",
    "u",
    "ul"
  ].map((name2) => ({ type: "type", label: name2 }));
  var span = /^[\w-]*/;
  var cssCompletionSource = (context) => {
    let { state, pos } = context, node = syntaxTree(state).resolveInner(pos, -1);
    if (node.name == "PropertyName")
      return { from: node.from, options: properties(), span };
    if (node.name == "ValueName")
      return { from: node.from, options: values, span };
    if (node.name == "PseudoClassName")
      return { from: node.from, options: pseudoClasses, span };
    if (node.name == "TagName") {
      for (let { parent } = node; parent; parent = parent.parent)
        if (parent.name == "Block")
          return { from: node.from, options: properties(), span };
      return { from: node.from, options: tags2, span };
    }
    if (!context.explicit)
      return null;
    let above = node.resolve(pos), before = above.childBefore(pos);
    if (before && before.name == ":" && above.name == "PseudoClassSelector")
      return { from: pos, options: pseudoClasses, span };
    if (before && before.name == ":" && above.name == "Declaration" || above.name == "ArgList")
      return { from: pos, options: values, span };
    if (above.name == "Block")
      return { from: pos, options: properties(), span };
    return null;
  };
  var cssLanguage = /* @__PURE__ */ LRLanguage.define({
    parser: /* @__PURE__ */ parser5.configure({
      props: [
        /* @__PURE__ */ indentNodeProp.add({
          Declaration: /* @__PURE__ */ continuedIndent()
        }),
        /* @__PURE__ */ foldNodeProp.add({
          Block: foldInside
        }),
        /* @__PURE__ */ styleTags({
          "import charset namespace keyframes": tags.definitionKeyword,
          "media supports": tags.controlKeyword,
          "from to selector": tags.keyword,
          NamespaceName: tags.namespace,
          KeyframeName: tags.labelName,
          TagName: tags.tagName,
          ClassName: tags.className,
          PseudoClassName: /* @__PURE__ */ tags.constant(tags.className),
          IdName: tags.labelName,
          "FeatureName PropertyName": tags.propertyName,
          AttributeName: tags.attributeName,
          NumberLiteral: tags.number,
          KeywordQuery: tags.keyword,
          UnaryQueryOp: tags.operatorKeyword,
          "CallTag ValueName": tags.atom,
          VariableName: tags.variableName,
          Callee: tags.operatorKeyword,
          Unit: tags.unit,
          "UniversalSelector NestingSelector": tags.definitionOperator,
          AtKeyword: tags.keyword,
          MatchOp: tags.compareOperator,
          "ChildOp SiblingOp, LogicOp": tags.logicOperator,
          BinOp: tags.arithmeticOperator,
          Important: tags.modifier,
          Comment: tags.blockComment,
          ParenthesizedContent: /* @__PURE__ */ tags.special(tags.name),
          ColorLiteral: tags.color,
          StringLiteral: tags.string,
          ":": tags.punctuation,
          "PseudoOp #": tags.derefOperator,
          "; ,": tags.separator,
          "( )": tags.paren,
          "[ ]": tags.squareBracket,
          "{ }": tags.brace
        })
      ]
    }),
    languageData: {
      commentTokens: { block: { open: "/*", close: "*/" } },
      indentOnInput: /^\s*\}$/,
      wordChars: "-"
    }
  });
  var cssCompletion = /* @__PURE__ */ cssLanguage.data.of({ autocomplete: cssCompletionSource });
  function css() {
    return new LanguageSupport(cssLanguage, cssCompletion);
  }

  // node_modules/@codemirror/lang-html/dist/index.js
  var Targets = ["_blank", "_self", "_top", "_parent"];
  var Charsets = ["ascii", "utf-8", "utf-16", "latin1", "latin1"];
  var Methods = ["get", "post", "put", "delete"];
  var Encs = ["application/x-www-form-urlencoded", "multipart/form-data", "text/plain"];
  var Bool = ["true", "false"];
  var S = {};
  var Tags = {
    a: {
      attrs: {
        href: null,
        ping: null,
        type: null,
        media: null,
        target: Targets,
        hreflang: null
      }
    },
    abbr: S,
    acronym: S,
    address: S,
    applet: S,
    area: {
      attrs: {
        alt: null,
        coords: null,
        href: null,
        target: null,
        ping: null,
        media: null,
        hreflang: null,
        type: null,
        shape: ["default", "rect", "circle", "poly"]
      }
    },
    article: S,
    aside: S,
    audio: {
      attrs: {
        src: null,
        mediagroup: null,
        crossorigin: ["anonymous", "use-credentials"],
        preload: ["none", "metadata", "auto"],
        autoplay: ["autoplay"],
        loop: ["loop"],
        controls: ["controls"]
      }
    },
    b: S,
    base: { attrs: { href: null, target: Targets } },
    basefont: S,
    bdi: S,
    bdo: S,
    big: S,
    blockquote: { attrs: { cite: null } },
    body: S,
    br: S,
    button: {
      attrs: {
        form: null,
        formaction: null,
        name: null,
        value: null,
        autofocus: ["autofocus"],
        disabled: ["autofocus"],
        formenctype: Encs,
        formmethod: Methods,
        formnovalidate: ["novalidate"],
        formtarget: Targets,
        type: ["submit", "reset", "button"]
      }
    },
    canvas: { attrs: { width: null, height: null } },
    caption: S,
    center: S,
    cite: S,
    code: S,
    col: { attrs: { span: null } },
    colgroup: { attrs: { span: null } },
    command: {
      attrs: {
        type: ["command", "checkbox", "radio"],
        label: null,
        icon: null,
        radiogroup: null,
        command: null,
        title: null,
        disabled: ["disabled"],
        checked: ["checked"]
      }
    },
    data: { attrs: { value: null } },
    datagrid: { attrs: { disabled: ["disabled"], multiple: ["multiple"] } },
    datalist: { attrs: { data: null } },
    dd: S,
    del: { attrs: { cite: null, datetime: null } },
    details: { attrs: { open: ["open"] } },
    dfn: S,
    dir: S,
    div: S,
    dl: S,
    dt: S,
    em: S,
    embed: { attrs: { src: null, type: null, width: null, height: null } },
    eventsource: { attrs: { src: null } },
    fieldset: { attrs: { disabled: ["disabled"], form: null, name: null } },
    figcaption: S,
    figure: S,
    font: S,
    footer: S,
    form: {
      attrs: {
        action: null,
        name: null,
        "accept-charset": Charsets,
        autocomplete: ["on", "off"],
        enctype: Encs,
        method: Methods,
        novalidate: ["novalidate"],
        target: Targets
      }
    },
    frame: S,
    frameset: S,
    h1: S,
    h2: S,
    h3: S,
    h4: S,
    h5: S,
    h6: S,
    head: {
      children: ["title", "base", "link", "style", "meta", "script", "noscript", "command"]
    },
    header: S,
    hgroup: S,
    hr: S,
    html: {
      attrs: { manifest: null }
    },
    i: S,
    iframe: {
      attrs: {
        src: null,
        srcdoc: null,
        name: null,
        width: null,
        height: null,
        sandbox: ["allow-top-navigation", "allow-same-origin", "allow-forms", "allow-scripts"],
        seamless: ["seamless"]
      }
    },
    img: {
      attrs: {
        alt: null,
        src: null,
        ismap: null,
        usemap: null,
        width: null,
        height: null,
        crossorigin: ["anonymous", "use-credentials"]
      }
    },
    input: {
      attrs: {
        alt: null,
        dirname: null,
        form: null,
        formaction: null,
        height: null,
        list: null,
        max: null,
        maxlength: null,
        min: null,
        name: null,
        pattern: null,
        placeholder: null,
        size: null,
        src: null,
        step: null,
        value: null,
        width: null,
        accept: ["audio/*", "video/*", "image/*"],
        autocomplete: ["on", "off"],
        autofocus: ["autofocus"],
        checked: ["checked"],
        disabled: ["disabled"],
        formenctype: Encs,
        formmethod: Methods,
        formnovalidate: ["novalidate"],
        formtarget: Targets,
        multiple: ["multiple"],
        readonly: ["readonly"],
        required: ["required"],
        type: [
          "hidden",
          "text",
          "search",
          "tel",
          "url",
          "email",
          "password",
          "datetime",
          "date",
          "month",
          "week",
          "time",
          "datetime-local",
          "number",
          "range",
          "color",
          "checkbox",
          "radio",
          "file",
          "submit",
          "image",
          "reset",
          "button"
        ]
      }
    },
    ins: { attrs: { cite: null, datetime: null } },
    kbd: S,
    keygen: {
      attrs: {
        challenge: null,
        form: null,
        name: null,
        autofocus: ["autofocus"],
        disabled: ["disabled"],
        keytype: ["RSA"]
      }
    },
    label: { attrs: { for: null, form: null } },
    legend: S,
    li: { attrs: { value: null } },
    link: {
      attrs: {
        href: null,
        type: null,
        hreflang: null,
        media: null,
        sizes: ["all", "16x16", "16x16 32x32", "16x16 32x32 64x64"]
      }
    },
    map: { attrs: { name: null } },
    mark: S,
    menu: { attrs: { label: null, type: ["list", "context", "toolbar"] } },
    meta: {
      attrs: {
        content: null,
        charset: Charsets,
        name: ["viewport", "application-name", "author", "description", "generator", "keywords"],
        "http-equiv": ["content-language", "content-type", "default-style", "refresh"]
      }
    },
    meter: { attrs: { value: null, min: null, low: null, high: null, max: null, optimum: null } },
    nav: S,
    noframes: S,
    noscript: S,
    object: {
      attrs: {
        data: null,
        type: null,
        name: null,
        usemap: null,
        form: null,
        width: null,
        height: null,
        typemustmatch: ["typemustmatch"]
      }
    },
    ol: {
      attrs: { reversed: ["reversed"], start: null, type: ["1", "a", "A", "i", "I"] },
      children: ["li", "script", "template", "ul", "ol"]
    },
    optgroup: { attrs: { disabled: ["disabled"], label: null } },
    option: { attrs: { disabled: ["disabled"], label: null, selected: ["selected"], value: null } },
    output: { attrs: { for: null, form: null, name: null } },
    p: S,
    param: { attrs: { name: null, value: null } },
    pre: S,
    progress: { attrs: { value: null, max: null } },
    q: { attrs: { cite: null } },
    rp: S,
    rt: S,
    ruby: S,
    s: S,
    samp: S,
    script: {
      attrs: {
        type: ["text/javascript"],
        src: null,
        async: ["async"],
        defer: ["defer"],
        charset: Charsets
      }
    },
    section: S,
    select: {
      attrs: {
        form: null,
        name: null,
        size: null,
        autofocus: ["autofocus"],
        disabled: ["disabled"],
        multiple: ["multiple"]
      }
    },
    small: S,
    source: { attrs: { src: null, type: null, media: null } },
    span: S,
    strike: S,
    strong: S,
    style: {
      attrs: {
        type: ["text/css"],
        media: null,
        scoped: null
      }
    },
    sub: S,
    summary: S,
    sup: S,
    table: S,
    tbody: S,
    td: { attrs: { colspan: null, rowspan: null, headers: null } },
    textarea: {
      attrs: {
        dirname: null,
        form: null,
        maxlength: null,
        name: null,
        placeholder: null,
        rows: null,
        cols: null,
        autofocus: ["autofocus"],
        disabled: ["disabled"],
        readonly: ["readonly"],
        required: ["required"],
        wrap: ["soft", "hard"]
      }
    },
    tfoot: S,
    th: { attrs: { colspan: null, rowspan: null, headers: null, scope: ["row", "col", "rowgroup", "colgroup"] } },
    thead: S,
    time: { attrs: { datetime: null } },
    title: S,
    tr: S,
    track: {
      attrs: {
        src: null,
        label: null,
        default: null,
        kind: ["subtitles", "captions", "descriptions", "chapters", "metadata"],
        srclang: null
      }
    },
    tt: S,
    u: S,
    ul: { children: ["li", "script", "template", "ul", "ol"] },
    var: S,
    video: {
      attrs: {
        src: null,
        poster: null,
        width: null,
        height: null,
        crossorigin: ["anonymous", "use-credentials"],
        preload: ["auto", "metadata", "none"],
        autoplay: ["autoplay"],
        mediagroup: ["movie"],
        muted: ["muted"],
        controls: ["controls"]
      }
    },
    wbr: S
  };
  var GlobalAttrs = {
    accesskey: null,
    class: null,
    contenteditable: Bool,
    contextmenu: null,
    dir: ["ltr", "rtl", "auto"],
    draggable: ["true", "false", "auto"],
    dropzone: ["copy", "move", "link", "string:", "file:"],
    hidden: ["hidden"],
    id: null,
    inert: ["inert"],
    itemid: null,
    itemprop: null,
    itemref: null,
    itemscope: ["itemscope"],
    itemtype: null,
    lang: ["ar", "bn", "de", "en-GB", "en-US", "es", "fr", "hi", "id", "ja", "pa", "pt", "ru", "tr", "zh"],
    spellcheck: Bool,
    autocorrect: Bool,
    autocapitalize: Bool,
    style: null,
    tabindex: null,
    title: null,
    translate: ["yes", "no"],
    onclick: null,
    rel: ["stylesheet", "alternate", "author", "bookmark", "help", "license", "next", "nofollow", "noreferrer", "prefetch", "prev", "search", "tag"],
    role: /* @__PURE__ */ "alert application article banner button cell checkbox complementary contentinfo dialog document feed figure form grid gridcell heading img list listbox listitem main navigation region row rowgroup search switch tab table tabpanel textbox timer".split(" "),
    "aria-activedescendant": null,
    "aria-atomic": Bool,
    "aria-autocomplete": ["inline", "list", "both", "none"],
    "aria-busy": Bool,
    "aria-checked": ["true", "false", "mixed", "undefined"],
    "aria-controls": null,
    "aria-describedby": null,
    "aria-disabled": Bool,
    "aria-dropeffect": null,
    "aria-expanded": ["true", "false", "undefined"],
    "aria-flowto": null,
    "aria-grabbed": ["true", "false", "undefined"],
    "aria-haspopup": Bool,
    "aria-hidden": Bool,
    "aria-invalid": ["true", "false", "grammar", "spelling"],
    "aria-label": null,
    "aria-labelledby": null,
    "aria-level": null,
    "aria-live": ["off", "polite", "assertive"],
    "aria-multiline": Bool,
    "aria-multiselectable": Bool,
    "aria-owns": null,
    "aria-posinset": null,
    "aria-pressed": ["true", "false", "mixed", "undefined"],
    "aria-readonly": Bool,
    "aria-relevant": null,
    "aria-required": Bool,
    "aria-selected": ["true", "false", "undefined"],
    "aria-setsize": null,
    "aria-sort": ["ascending", "descending", "none", "other"],
    "aria-valuemax": null,
    "aria-valuemin": null,
    "aria-valuenow": null,
    "aria-valuetext": null
  };
  var AllTags = /* @__PURE__ */ Object.keys(Tags);
  var GlobalAttrNames = /* @__PURE__ */ Object.keys(GlobalAttrs);
  function elementName(doc2, tree, max = doc2.length) {
    if (!tree)
      return "";
    let tag = tree.firstChild;
    let name2 = tag && tag.getChild("TagName");
    return name2 ? doc2.sliceString(name2.from, Math.min(name2.to, max)) : "";
  }
  function findParentElement(tree, skip = false) {
    for (let cur2 = tree.parent; cur2; cur2 = cur2.parent)
      if (cur2.name == "Element") {
        if (skip)
          skip = false;
        else
          return cur2;
      }
    return null;
  }
  function allowedChildren(doc2, tree) {
    let parentInfo = Tags[elementName(doc2, findParentElement(tree, true))];
    return (parentInfo === null || parentInfo === void 0 ? void 0 : parentInfo.children) || AllTags;
  }
  function openTags(doc2, tree) {
    let open = [];
    for (let parent = tree; parent = findParentElement(parent); ) {
      let tagName = elementName(doc2, parent);
      if (tagName && parent.lastChild.name == "CloseTag")
        break;
      if (tagName && open.indexOf(tagName) < 0 && (tree.name == "EndTag" || tree.from >= parent.firstChild.to))
        open.push(tagName);
    }
    return open;
  }
  var identifier2 = /^[:\-\.\w\u00b7-\uffff]+$/;
  function completeTag(state, tree, from2, to) {
    let end = /\s*>/.test(state.sliceDoc(to, to + 5)) ? "" : ">";
    return {
      from: from2,
      to,
      options: allowedChildren(state.doc, tree).map((tagName) => ({ label: tagName, type: "type" })).concat(openTags(state.doc, tree).map((tag, i) => ({ label: "/" + tag, apply: "/" + tag + end, type: "type", boost: 99 - i }))),
      span: /^\/?[:\-\.\w\u00b7-\uffff]*$/
    };
  }
  function completeCloseTag(state, tree, from2, to) {
    let end = /\s*>/.test(state.sliceDoc(to, to + 5)) ? "" : ">";
    return {
      from: from2,
      to,
      options: openTags(state.doc, tree).map((tag, i) => ({ label: tag, apply: tag + end, type: "type", boost: 99 - i })),
      span: identifier2
    };
  }
  function completeStartTag(state, tree, pos) {
    let options = [], level = 0;
    for (let tagName of allowedChildren(state.doc, tree))
      options.push({ label: "<" + tagName, type: "type" });
    for (let open of openTags(state.doc, tree))
      options.push({ label: "</" + open + ">", type: "type", boost: 99 - level++ });
    return { from: pos, to: pos, options, span: /^<\/?[:\-\.\w\u00b7-\uffff]*$/ };
  }
  function completeAttrName(state, tree, from2, to) {
    let elt = findParentElement(tree), info = elt ? Tags[elementName(state.doc, elt)] : null;
    let names = info && info.attrs ? Object.keys(info.attrs).concat(GlobalAttrNames) : GlobalAttrNames;
    return {
      from: from2,
      to,
      options: names.map((attrName) => ({ label: attrName, type: "property" })),
      span: identifier2
    };
  }
  function completeAttrValue(state, tree, from2, to) {
    var _a2;
    let nameNode = (_a2 = tree.parent) === null || _a2 === void 0 ? void 0 : _a2.getChild("AttributeName");
    let options = [], span2 = void 0;
    if (nameNode) {
      let attrName = state.sliceDoc(nameNode.from, nameNode.to);
      let attrs = GlobalAttrs[attrName];
      if (!attrs) {
        let elt = findParentElement(tree), info = elt ? Tags[elementName(state.doc, elt)] : null;
        attrs = (info === null || info === void 0 ? void 0 : info.attrs) && info.attrs[attrName];
      }
      if (attrs) {
        let base2 = state.sliceDoc(from2, to).toLowerCase(), quoteStart = '"', quoteEnd = '"';
        if (/^['"]/.test(base2)) {
          span2 = base2[0] == '"' ? /^[^"]*$/ : /^[^']*$/;
          quoteStart = "";
          quoteEnd = state.sliceDoc(to, to + 1) == base2[0] ? "" : base2[0];
          base2 = base2.slice(1);
          from2++;
        } else {
          span2 = /^[^\s<>='"]*$/;
        }
        for (let value of attrs)
          options.push({ label: value, apply: quoteStart + value + quoteEnd, type: "constant" });
      }
    }
    return { from: from2, to, options, span: span2 };
  }
  function htmlCompletionSource(context) {
    let { state, pos } = context, around = syntaxTree(state).resolveInner(pos), tree = around.resolve(pos, -1);
    for (let scan = pos, before; around == tree && (before = tree.childBefore(scan)); ) {
      let last = before.lastChild;
      if (!last || !last.type.isError || last.from < last.to)
        break;
      around = tree = before;
      scan = last.from;
    }
    if (tree.name == "TagName") {
      return tree.parent && /CloseTag$/.test(tree.parent.name) ? completeCloseTag(state, tree, tree.from, pos) : completeTag(state, tree, tree.from, pos);
    } else if (tree.name == "StartTag") {
      return completeTag(state, tree, pos, pos);
    } else if (tree.name == "StartCloseTag" || tree.name == "IncompleteCloseTag") {
      return completeCloseTag(state, tree, pos, pos);
    } else if (context.explicit && (tree.name == "OpenTag" || tree.name == "SelfClosingTag") || tree.name == "AttributeName") {
      return completeAttrName(state, tree, tree.name == "AttributeName" ? tree.from : pos, pos);
    } else if (tree.name == "Is" || tree.name == "AttributeValue" || tree.name == "UnquotedAttributeValue") {
      return completeAttrValue(state, tree, tree.name == "Is" ? pos : tree.from, pos);
    } else if (context.explicit && (around.name == "Element" || around.name == "Text" || around.name == "Document")) {
      return completeStartTag(state, tree, pos);
    } else {
      return null;
    }
  }
  var htmlLanguage = /* @__PURE__ */ LRLanguage.define({
    parser: /* @__PURE__ */ parser4.configure({
      props: [
        /* @__PURE__ */ indentNodeProp.add({
          Element(context) {
            let after = /^(\s*)(<\/)?/.exec(context.textAfter);
            if (context.node.to <= context.pos + after[0].length)
              return context.continue();
            return context.lineIndent(context.node.from) + (after[2] ? 0 : context.unit);
          },
          "OpenTag CloseTag SelfClosingTag"(context) {
            return context.column(context.node.from) + context.unit;
          },
          Document(context) {
            if (context.pos + /\s*/.exec(context.textAfter)[0].length < context.node.to)
              return context.continue();
            let endElt = null, close;
            for (let cur2 = context.node; ; ) {
              let last = cur2.lastChild;
              if (!last || last.name != "Element" || last.to != cur2.to)
                break;
              endElt = cur2 = last;
            }
            if (endElt && !((close = endElt.lastChild) && (close.name == "CloseTag" || close.name == "SelfClosingTag")))
              return context.lineIndent(endElt.from) + context.unit;
            return null;
          }
        }),
        /* @__PURE__ */ foldNodeProp.add({
          Element(node) {
            let first = node.firstChild, last = node.lastChild;
            if (!first || first.name != "OpenTag")
              return null;
            return { from: first.to, to: last.name == "CloseTag" ? last.from : node.to };
          }
        }),
        /* @__PURE__ */ styleTags({
          "Text RawText": tags.content,
          "StartTag StartCloseTag SelfCloserEndTag EndTag SelfCloseEndTag": tags.angleBracket,
          TagName: tags.tagName,
          "MismatchedCloseTag/TagName": [tags.tagName, tags.invalid],
          AttributeName: tags.attributeName,
          "AttributeValue UnquotedAttributeValue": tags.attributeValue,
          Is: tags.definitionOperator,
          "EntityReference CharacterReference": tags.character,
          Comment: tags.blockComment,
          ProcessingInst: tags.processingInstruction,
          DoctypeDecl: tags.documentMeta
        })
      ],
      wrap: /* @__PURE__ */ configureNesting([
        {
          tag: "script",
          attrs(attrs) {
            return !attrs.type || /^(?:text|application)\/(?:x-)?(?:java|ecma)script$|^module$|^$/i.test(attrs.type);
          },
          parser: javascriptLanguage.parser
        },
        {
          tag: "style",
          attrs(attrs) {
            return (!attrs.lang || attrs.lang == "css") && (!attrs.type || /^(text\/)?(x-)?(stylesheet|css)$/i.test(attrs.type));
          },
          parser: cssLanguage.parser
        }
      ])
    }),
    languageData: {
      commentTokens: { block: { open: "<!--", close: "-->" } },
      indentOnInput: /^\s*<\/\w+\W$/,
      wordChars: "-._"
    }
  });
  var htmlCompletion = /* @__PURE__ */ htmlLanguage.data.of({ autocomplete: htmlCompletionSource });
  function html(config2 = {}) {
    let lang = htmlLanguage;
    if (config2.matchClosingTags === false)
      lang = lang.configure({ dialect: "noMatch" });
    return new LanguageSupport(lang, [
      htmlCompletion,
      config2.autoCloseTags !== false ? autoCloseTags : [],
      javascript().support,
      css().support
    ]);
  }
  var autoCloseTags = /* @__PURE__ */ EditorView.inputHandler.of((view, from2, to, text) => {
    if (view.composing || view.state.readOnly || from2 != to || text != ">" && text != "/" || !htmlLanguage.isActiveAt(view.state, from2, -1))
      return false;
    let { state } = view;
    let changes = state.changeByRange((range) => {
      var _a2, _b, _c;
      let { head } = range, around = syntaxTree(state).resolveInner(head, -1), name2;
      if (around.name == "TagName" || around.name == "StartTag")
        around = around.parent;
      if (text == ">" && around.name == "OpenTag") {
        if (((_b = (_a2 = around.parent) === null || _a2 === void 0 ? void 0 : _a2.lastChild) === null || _b === void 0 ? void 0 : _b.name) != "CloseTag" && (name2 = elementName(state.doc, around.parent, head)))
          return { range: EditorSelection.cursor(head + 1), changes: { from: head, insert: `></${name2}>` } };
      } else if (text == "/" && around.name == "OpenTag") {
        let empty2 = around.parent, base2 = empty2 === null || empty2 === void 0 ? void 0 : empty2.parent;
        if (empty2.from == head - 1 && ((_c = base2.lastChild) === null || _c === void 0 ? void 0 : _c.name) != "CloseTag" && (name2 = elementName(state.doc, base2, head))) {
          let insert2 = `/${name2}>`;
          return { range: EditorSelection.cursor(head + insert2.length), changes: { from: head, insert: insert2 } };
        }
      }
      return { range };
    });
    if (changes.changes.empty)
      return false;
    view.dispatch(changes, { userEvent: "input.type", scrollIntoView: true });
    return true;
  });

  // node_modules/@codemirror/lang-php/dist/index.js
  var phpLanguage = /* @__PURE__ */ LRLanguage.define({
    parser: /* @__PURE__ */ parser3.configure({
      props: [
        /* @__PURE__ */ indentNodeProp.add({
          IfStatement: /* @__PURE__ */ continuedIndent({ except: /^\s*({|else\b|elseif\b|endif\b)/ }),
          TryStatement: /* @__PURE__ */ continuedIndent({ except: /^\s*({|catch\b|finally\b)/ }),
          SwitchBody: (context) => {
            let after = context.textAfter, closed = /^\s*\}/.test(after), isCase = /^\s*(case|default)\b/.test(after);
            return context.baseIndent + (closed ? 0 : isCase ? 1 : 2) * context.unit;
          },
          ColonBlock: (cx) => cx.baseIndent + cx.unit,
          "Block EnumBody DeclarationList": /* @__PURE__ */ delimitedIndent({ closing: "}" }),
          ArrowFunction: (cx) => cx.baseIndent + cx.unit,
          "String BlockComment": () => -1,
          Statement: /* @__PURE__ */ continuedIndent({ except: /^({|end(for|foreach|switch|while)\b)/ })
        }),
        /* @__PURE__ */ foldNodeProp.add({
          "Block EnumBody DeclarationList SwitchBody ArrayExpression ValueList": foldInside,
          ColonBlock(tree) {
            return { from: tree.from + 1, to: tree.to };
          },
          BlockComment(tree) {
            return { from: tree.from + 2, to: tree.to - 2 };
          }
        }),
        /* @__PURE__ */ styleTags({
          "Visibility abstract final static": tags.modifier,
          "for foreach while do if else elseif switch try catch finally return throw break continue default case": tags.controlKeyword,
          "endif endfor endforeach endswitch endwhile goto match": tags.controlKeyword,
          "and or xor yield unset clone instanceof insteadof": tags.operatorKeyword,
          "function fn class trait implements extends const enum global namespace trait use var": tags.definitionKeyword,
          "include include_once require require_once declare enddeclare": tags.definitionKeyword,
          "new from echo print array list as": tags.keyword,
          null: tags.null,
          Boolean: tags.bool,
          VariableName: tags.variableName,
          "NamespaceName/...": tags.namespace,
          "NamedType/...": tags.typeName,
          Name: tags.name,
          "CallExpression/Name": /* @__PURE__ */ tags.function(tags.variableName),
          "LabelStatement/Name": tags.labelName,
          "MemberExpression/Name MemberExpression/VariableName": tags.propertyName,
          "CallExpression/MemberExpression/Name": /* @__PURE__ */ tags.function(tags.propertyName),
          "FunctionDefinition/Name": /* @__PURE__ */ tags.function(/* @__PURE__ */ tags.definition(tags.variableName)),
          "ClassDeclaration/Name": /* @__PURE__ */ tags.definition(tags.className),
          UpdateOp: tags.updateOperator,
          ArithOp: tags.arithmeticOperator,
          LogicOp: tags.logicOperator,
          BitOp: tags.bitwiseOperator,
          CompareOp: tags.compareOperator,
          ControlOp: tags.controlOperator,
          AssignOp: tags.definitionOperator,
          "$ ConcatOp": tags.operator,
          LineComment: tags.lineComment,
          BlockComment: tags.blockComment,
          Integer: tags.integer,
          Float: tags.float,
          String: tags.string,
          ShellExpression: /* @__PURE__ */ tags.special(tags.string),
          "=> ->": tags.punctuation,
          "( )": tags.paren,
          "#[ [ ]": tags.squareBracket,
          "${ { }": tags.brace,
          "-> ?->": tags.derefOperator,
          ", ; :: : \\": tags.separator,
          "PhpOpen PhpClose": tags.processingInstruction
        })
      ]
    }),
    languageData: {
      commentTokens: { block: { open: "/*", close: "*/" }, line: "//" },
      indentOnInput: /^\s*(?:case |default:|end(?:if|for(?:each)?|switch|while)|else(?:if)?|\{|\})$/,
      wordChars: "$"
    }
  });
  function php(config2 = {}) {
    let support = [], base2;
    if (config2.baseLanguage === null)
      ;
    else if (config2.baseLanguage) {
      base2 = config2.baseLanguage;
    } else {
      let htmlSupport = html({ matchClosingTags: false });
      support.push(htmlSupport.support);
      base2 = htmlSupport.language;
    }
    return new LanguageSupport(phpLanguage.configure({
      wrap: base2 && parseMixed((node) => {
        if (!node.type.isTop)
          return null;
        return {
          parser: base2.parser,
          overlay: (node2) => node2.name == "Text"
        };
      }),
      top: config2.plain ? "Program" : "Template"
    }), support);
  }

  // resources/js/components/code-editor.js
  var code_editor_default = (Alpine) => {
    Alpine.data("codeEditorFormComponent", ({
      state
    }) => {
      return {
        state,
        init: function() {
          this.render();
        },
        render() {
          this.editor = null;
          this.editor = new EditorView({
            state: EditorState.create({
              extensions: [
                basicSetup,
                keymap.of([indentWithTab]),
                javascript(),
                php(),
                json(),
                css(),
                html(),
                EditorView.updateListener.of((v) => {
                  if (v.docChanged) {
                    this.state = v.state.doc.toString();
                  }
                })
              ],
              doc: this.state
            }),
            parent: this.$refs.codeeditor
          });
        }
      };
    });
  };

  // resources/js/index.js
  document.addEventListener("alpine:init", () => {
    window.Alpine.plugin(code_editor_default);
  });
})();
