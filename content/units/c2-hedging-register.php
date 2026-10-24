<?php
return [
    'icon' => '🎓',
    'vocab' => [
        ['suggest', 'sugerir, indicar'], ['indicate', 'indicar'], ['appear to', 'parecer'], ['likelihood', 'probabilidad'],
        ['tentative', 'tentativo/a, provisorio/a'], ['sample', 'muestra'], ['correlation', 'correlación'], ['caveat', 'salvedad, advertencia'],
    ],
    'vocab_icons' => [
        'arguably' => '🤔', 'to some extent' => '📏', 'findings' => '🔬', 'ascertain' => '✔️', 'presumably' => '🧐', 'call off' => '❌', 'postpone' => '⏭️', 'tend to' => '📈',
        'suggest' => '💭', 'indicate' => '👉', 'appear to' => '👀', 'likelihood' => '🎲', 'tentative' => '✏️', 'sample' => '🧪', 'correlation' => '📊', 'caveat' => '⚠️',
    ],
    'readings' => [
        'abstract' => [
            'kind' => 'article',
            'title' => 'Bilingualism and attention: a cautious look at the evidence',
            'text' => "Over the past two decades, a considerable number of studies have suggested that bilingual adults may perform better than monolinguals on certain attention tasks. These findings have been widely reported in the media, often in rather exaggerated terms.\n\nThe present study examined a sample of 320 adults in Córdoba, half of whom used English and Spanish daily. Bilingual participants appeared to switch between tasks slightly faster; however, the difference was small and, arguably, of limited practical importance. When factors such as education and income were controlled for, the effect became considerably weaker.\n\nThese results should therefore be interpreted with caution. While there seems to be a correlation between daily bilingual use and certain attentional skills, it would be premature to conclude that speaking two languages causes these benefits. It is possible that people with stronger attention skills are simply more likely to become bilingual.\n\nA further caveat concerns the sample, which was drawn largely from university staff. Future research could ascertain whether similar patterns emerge in more diverse populations.",
        ],
    ],
    'exercises' => [
        ['match', 'Uní cada expresión informal con su versión académica.', [['look into', 'investigate'], ['find out', 'ascertain'], ['call off', 'cancel'], ['put off', 'postpone'], ['a lot of', 'a considerable number of']], 'En el registro académico se prefieren verbos de origen latino y frases más precisas.'],
        ['mcq', 'Choose the word: The study used a ___ of 500 students from three universities.', ['sample', 'caveat', 'likelihood'], 0, 'Sample: muestra.', 'skill' => 'vocabulary'],
        ['mcq', "Choose the word: There is a strong ___ between sleep and academic performance.", ['correlation', 'sample', 'caveat'], 0, 'Correlation: correlación (no implica causalidad).', 'skill' => 'vocabulary'],
        ['mcq', 'Choose the word: These conclusions are only ___ ; more data is needed.', ['tentative', 'arguably', 'presumably'], 0, 'Tentative: provisorio, no definitivo.', 'skill' => 'vocabulary'],

        ['listen', '¿Qué tan segura es la afirmación?', 'The data would seem to suggest that the policy has been, to some extent, effective.', ['Muy segura y categórica', 'Prudente y matizada', 'Totalmente negativa'], 1, 'Would seem to suggest y to some extent son marcadores de cautela (hedging).'],
        ['listen', '¿Qué se dice del evento?', 'Owing to unforeseen circumstances, the conference has been postponed until further notice.', ['Se canceló definitivamente', 'Se postergó sin fecha', 'Se adelantó'], 1, 'Postponed until further notice: postergado hasta nuevo aviso.'],
        ['dictation', 'Escuchá y escribí la oración.', 'These findings should be interpreted with caution.', 'Fórmula académica para limitar el alcance de una conclusión.'],

        ['reading', 'abstract', 'How does the writer describe media coverage of previous studies?', ['Accurate', 'Often exaggerated', 'Non-existent'], 1, '"Widely reported in the media, often in rather exaggerated terms."'],
        ['reading', 'abstract', 'What happened when education and income were controlled for?', ['The effect became stronger.', 'The effect became considerably weaker.', 'Nothing changed.'], 1, '"The effect became considerably weaker."'],
        ['reading', 'abstract', 'What alternative explanation does the writer offer?', ['People with better attention may be more likely to become bilingual.', 'Monolinguals are less intelligent.', 'Spanish is easier than English.'], 0, '"People with stronger attention skills are simply more likely to become bilingual."'],
        ['reading', 'abstract', 'True or false? The sample represents the general population well.', ['True', 'False'], 1, 'False: "drawn largely from university staff".'],

        ['speak', 'Decí la oración académica en voz alta.', 'It could be argued that the results are, to some extent, inconclusive.', 'Pausas breves en las comas; acento en ARGUED e INCONCLUSIVE.'],
        ['speak', 'Decí la oración en voz alta.', 'There appears to be a correlation between the two variables.', 'Correlation: /ˌkɒrəˈleɪʃən/.'],

        ['write', 'Escribí un párrafo académico (resumen de resultados) sobre un tema que te interese: presentá un hallazgo, matizalo y señalá al menos una limitación.',
            ['min' => 100, 'must' => [['\b(may|might|could|would seem|appear(s)? to|seem(s)? to|tend(s)? to)\b', 'Usás verbos de cautela (may, appear to, tend to…)'], ['\b(arguably|to some extent|presumably|relatively|somewhat|largely|possibly|likely)\b', 'Usás un adverbio o frase de matización'], ['\b(findings|sample|correlation|caveat|limitation|data|evidence)\b', 'Usás vocabulario académico'], ['\b(however|nevertheless|while|although|whereas)\b', 'Contrastás ideas (however, while…)']],
             'model' => "The findings of this small survey suggest that remote work may have a positive effect on employees' wellbeing. Participants who worked from home at least three days a week tended to report lower levels of stress and appeared to sleep somewhat longer. However, these results should be interpreted with caution. The sample was relatively small and consisted largely of young professionals in the technology sector, who are arguably more comfortable with digital tools. Furthermore, the data were self-reported, which may have introduced some bias. While there seems to be a correlation between remote work and wellbeing, further research would be needed to establish a causal relationship."],
            'El hedging no es inseguridad: es precisión sobre cuánto demuestran los datos.'],
    ],
];
