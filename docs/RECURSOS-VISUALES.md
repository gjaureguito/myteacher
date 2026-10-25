# Recursos visuales y animaciones · MyTeacher

Generados con la herramienta integrada image_gen (sin CLI), 25/10/2026. Imágenes ilustrativas de personas ficticias y un espacio de estudio; las etiquetas visibles identifican su origen. Los originales se conservaron en la carpeta de generación y se publican variantes WebP de 640 y 1280 píxeles.

## Archivos finales

- public/assets/images/conversation-v2-640.webp
- public/assets/images/conversation-v2-1280.webp
- public/assets/images/study-desk-v2-640.webp
- public/assets/images/study-desk-v2-1280.webp

La portada usa picture/srcset, tamaños explícitos, prioridad alta y decodificación asíncrona. Las imágenes de secciones inferiores y del panel se cargan de forma diferida. Los cuatro recursos suman aproximadamente 346 KB; el navegador selecciona la variante apropiada.

## Movimiento

Entradas con IntersectionObserver y Web Animations, trazo de portada y ondas decorativas finitas (<3 segundos), elevación de tarjetas y acercamiento de imágenes al interactuar. Sin video automático ni animación decorativa infinita nueva. Se respeta prefers-reduced-motion y el botón del pie permite desactivar y recordar el movimiento. El contenido es visible si falla JavaScript. Los efectos se pausan cuando la pestaña queda oculta.

## Prompt final · conversación

Use case: photorealistic-natural. Asset type: editorial hero photograph for MyTeacher, an adult English self-study platform in Argentina. Create a premium, candid and believable lifestyle photograph, landscape 3:2. Two adult friends, a Latin American woman in her early thirties and a man in his mid thirties, casually practising a conversation in English at a small beautiful sunlit cafe table. One open notebook with indistinct unlettered pages, one closed deep forest green book, simple ceramic coffee cups. Genuine relaxed engaged expressions, natural hands and anatomy, no exaggerated laughter, eye-level close medium framing, upper bodies and table clearly visible, subject group centred so it crops well to portrait. Warm natural window light, cream plaster, soft olive foliage and dark teal accents, restrained contemporary editorial art direction, realistic skin texture, shallow but not extreme depth of field. No text, no lettering, no logos, no watermark, no flag motifs, no interface mockup. Only two people, no crowd. It should feel like English opening an everyday conversation, not a posed corporate classroom. Final asset will be used on the live MyTeacher site; return the generated image and local saved path.

## Prompt final · escritorio

Use case: photorealistic-natural. Asset type: editorial study photograph for the MyTeacher English learning platform, complementing a warm cream and deep forest green website with lime details. Landscape 3:2 image. A beautifully natural close overhead three-quarter view of an adult's quiet home study desk beside a sunlit window: an open thick cream paper workbook with a few subtle unlettered ruled lines, an elegant forest green closed book underneath, wooden pencil, simple sage ceramic coffee cup, off-white headphones resting to the side, and the edge of a laptop with an indistinct dark screen, soft plant shadows. Tactile real paper and warm oak textures, sophisticated magazine photography, daylight, uncluttered composition with the open book as the focal point. No people, no hands, no readable text, no lettering, no logo, no flags, no watermark, no UI screenshot. The entire notebook must be visible and naturally proportioned. Calm aspirational self-study, not a generic office desk. Final image will be integrated in a live website; return the generated image and local saved path.

