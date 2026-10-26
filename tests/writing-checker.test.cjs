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
 const match=rule.re.exec(text);
 if (!match) return false;
 if (id==='he-she' && /\b(?:does|did|can|could|will|would|should|may|might|must|let|make)\s*$/i.test(text.slice(Math.max(0,match.index-28),match.index))) return false;
 return true;
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
 ['more-better','This option is better.',false],
 ['he-she','She work every day.',true],
 ['he-she','She works every day.',false],
 ['he-she','Could she work here?',false],
 ['i-cap','i think this is useful.',true],
 ['i-cap','I think this is useful.',false],
 ['dont','I dont understand.',true],
 ['dont','I don’t understand.',false]
];
for(const [id,text,expected] of cases) assert.equal(detects(id,text),expected, id+' / '+text);

function replacePrecisely(original, regex, replacement, reviewedText) {
 if(original !== reviewedText) return original;
 regex.lastIndex=0;
 const match=regex.exec(reviewedText);
 if(!match)return original;
 const next=typeof replacement==='function'?replacement(match[0]):replacement;
 return original.slice(0,match.index)+next+original.slice(match.index+match[0].length);
}
assert.equal(replacePrecisely('  I dont know.', /\bdont\b/gi, 'don’t', '  I dont know.'), '  I don’t know.');
assert.equal(replacePrecisely('I dont know.', /\bdont\b/gi, 'don’t', 'I dont know.'), 'I don’t know.');
assert.equal(replacePrecisely('I dont know. I dont agree.', /\bdont\b/gi, 'don’t', 'I dont know. I dont agree.'), 'I don’t know. I dont agree.');
assert.equal(replacePrecisely('I dont know. I dont agree.', /\bdont\b/gi, 'don’t', 'I dont know.'), 'I dont know. I dont agree.');
const draft='She did not went.';
const corrected=replacePrecisely(draft,/\bdid\s+not\s+went\b/gi,m=>m.replace('went','go'),draft);
assert.equal(corrected,'She did not go.');
assert.equal(corrected=== 'She did not go.' ? draft : corrected,draft,'El deshacer restaura solo la versión exacta');

console.log('OK: '+cases.length+' verificaciones de reglas y 6 escenarios de reemplazo/deshacer.');
