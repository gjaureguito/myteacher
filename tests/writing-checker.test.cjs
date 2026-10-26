// Ejecutar desde la raíz del proyecto: node tests/writing-checker.test.cjs
const assert=require('node:assert/strict');
const fs=require('node:fs');
const vm=require('node:vm');
const src=fs.readFileSync('public/assets/writing-checker.js','utf8');
const start=src.indexOf('const rules=[');
const end=src.indexOf('\n ];',start);
assert(start>=0 && end>start,'No se encontró la definición de reglas');
const rules=vm.runInNewContext(src.slice(start,end+4)+'\nrules', {});
function detects(id, text) {
 const rule=rules.find(x=>x.id===id);
 assert(rule,'Regla inexistente: '+id);
 rule.re.lastIndex=0;
 return Boolean(rule.re.exec(text));
}
const cases=[
 ['did-not','She did not went to work.',true],
 ['did-not','She did not go to work.',false],
 ['advices','She gave two advices.',true],
 ['advices','She gave two pieces of advice.',false],
 ['informations','We need many informations.',true],
 ['informations','We need more information.',false],
 ['a-vowel','I ate a apple.',true],
 ['a-vowel','I ate an apple.',false],
 ['an-consonant','He has an book.',true],
 ['an-consonant','He has a book.',false],
 ['more-better','This option is more better.',true],
 ['more-better','This option is better.',false]
];
for(const [id,text,expected] of cases) assert.equal(detects(id,text),expected, id+' / '+text);
console.log('OK: '+cases.length+' verificaciones de reglas gramaticales.');
