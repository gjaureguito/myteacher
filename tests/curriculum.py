"""Validate authored curriculum coverage and editorial contracts, without a database."""
import json, re, subprocess
from pathlib import Path
ROOT=Path(__file__).resolve().parents[1]
source="""<?php
$items=[];
foreach(glob('content/[a-c][1-2].php') as $file) {
 $level=require $file;
 foreach($level['units'] as $unit) $items[]=$unit['slug'];
}
echo json_encode($items);
"""
slugs=json.loads(subprocess.run(['php'],input=source,text=True,cwd=ROOT,capture_output=True,check=True).stdout)
paths=sorted((ROOT/'content/labs').glob('*.json'))
assert len(paths)==len(slugs)==24
passages=set(); all_words=0
for path in paths:
 d=json.loads(path.read_text(encoding='utf-8'))
 assert d['slug']==path.stem and d['slug'] in slugs
 assert d['goal'] and d['strategy'] and len(d['glossary'])>=4 and len(d['checklist'])>=3
 assert len(d['passage'].split())>=60 and d['passage'] not in passages
 passages.add(d['passage'])
 assert len(d['questions'])==2
 for prompt,options,correct,explanation in d['questions']:
  assert prompt and len(options)==3 and len(set(options))==3
  assert type(correct) is int and 0<=correct<len(options) and len(explanation.split())>=10
 assert len(d['contrast'])==3 and d['contrast'][0]!=d['contrast'][1]
 assert len(d['model'].split())>=50
 ranges=re.findall(r'(\d+)–(\d+) palabras',d['task'])
 if ranges:
  models=[d['model']]
  if len(ranges)==2:
   models=d['model'].split('\n\n')
  assert len(models)==len(ranges)
  for model,(minimum,maximum) in zip(models,ranges):
   model=re.sub(r'^(Informal|Formal):\s*','',model)
   assert int(minimum)<=len(model.split())<=int(maximum),(d['slug'],len(model.split()),minimum,maximum)
 all_words+=sum(len(d[key].split()) for key in ['passage','model','strategy','goal','task'])
print(f'PASS: 24 unique workshops cover all units; 48 comprehension questions; models meet requested length; {all_words} words across readings, models, strategies, goals and tasks')
