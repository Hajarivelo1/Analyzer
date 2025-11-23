Analyse SEO complète pour : {{ $project->base_url }}

📊 DONNÉES RÉELLES EXTRAITES DU SITE :

• **TITRE** : "{{ $seo['title'] ?? 'Non détecté' }}" ({{ strlen($seo['title'] ?? '') }} caractères)
• **META DESCRIPTION** : "{{ substr($seo['meta_description'] ?? 'Non détectée', 0, 150) }}{{ strlen($seo['meta_description'] ?? '') > 150 ? '...' : '' }}" ({{ strlen($seo['meta_description'] ?? '') }} caractères)
• **MOTS-CLÉS PRINCIPAUX** : 
@if(!empty($seo['keywords']) && is_array($seo['keywords']))
  @foreach(array_slice($seo['keywords'], 0, 8) as $keyword => $count)
  - {{ $keyword }} ({{ $count }} occurrences)
  @endforeach
  • **Densité moyenne** : {{ $seo['density'] ?? 0 }}%
@else
  Aucun mot-clé significatif détecté
@endif

• **STRUCTURE DES TITRES** :
@if(!empty($seo['headings_structure']) && is_array($seo['headings_structure']))
  - H1 : {{ $seo['headings_structure']['summary']['by_level']['h1'] ?? 0 }} trouvé(s) 
  @if(!empty($seo['headings_structure']['h1']) && is_array($seo['headings_structure']['h1']))
    @foreach($seo['headings_structure']['h1'] as $h1)
    - "{{ substr($h1['text'] ?? '', 0, 80) }}{{ strlen($h1['text'] ?? '') > 80 ? '...' : '' }}" ({{ $h1['length'] ?? 0 }} caractères)
    @endforeach
  @endif
  - H2 : {{ $seo['headings_structure']['summary']['by_level']['h2'] ?? 0 }} trouvé(s)
  - H3 : {{ $seo['headings_structure']['summary']['by_level']['h3'] ?? 0 }} trouvé(s)
  - H4 : {{ $seo['headings_structure']['summary']['by_level']['h4'] ?? 0 }} trouvé(s)
  - H5 : {{ $seo['headings_structure']['summary']['by_level']['h5'] ?? 0 }} trouvé(s)
  - H6 : {{ $seo['headings_structure']['summary']['by_level']['h6'] ?? 0 }} trouvé(s)
  - **Total headings** : {{ $seo['headings_structure']['summary']['total'] ?? 0 }}
  
  @if(!empty($seo['headings_structure']['has_issues']) && !empty($seo['headings_structure']['issues']))
  • **PROBLÈMES HEADINGS** :
    @foreach($seo['headings_structure']['issues'] as $issue)
    - ⚠️ {{ $issue }}
    @endforeach
  @endif
@else
  - Aucune structure de headings détectée
@endif

• **ANALYSE DU CONTENU** : 
  - **Mots total** : {{ $seo['word_count'] ?? 0 }} mots
  - **Paragraphes** : {{ $seo['content_analysis']['paragraph_count'] ?? 0 }} paragraphes
  - **Paragraphes courts** (< 40 mots) : {{ $seo['content_analysis']['short_paragraphs'] ?? 0 }}
  - **Paragraphes dupliqués** : {{ count($seo['content_analysis']['duplicate_paragraphs'] ?? []) }}
  - **Mots moyens/paragraphe** : {{ $seo['content_analysis']['avg_words_per_paragraph'] ?? 0 }}
  - **Score de lisibilité** : {{ $seo['readability_score'] ?? 'N/A' }}%
  - **Longueur du contenu** : {{ $seo['body_length'] ?? 0 }} caractères

• **MÉDIAS** :
  - **Images détectées** : {{ $seo['images_count'] ?? 0 }} images
  - **Images sans alt** : {{ $seo['technical_audit']['images_with_missing_alt'] ?? 0 }}

• **LIENS** : 
  - **Liens totaux** : {{ $seo['total_links'] ?? 0 }} liens
  - **Liens internes** : {{ $seo['internal_links'] ?? 0 }}
  - **Liens externes** : {{ $seo['external_links'] ?? 0 }}

🔧 **AUDIT TECHNIQUE COMPLET** :
@if(!empty($seo['technical_audit']) && is_array($seo['technical_audit']))
- **Titre présent** : {{ ($seo['technical_audit']['has_title'] ?? false) ? '✅ PRÉSENT' : '❌ ABSENT' }}
- **Meta description** : {{ ($seo['technical_audit']['has_meta_description'] ?? false) ? '✅ PRÉSENT' : '❌ ABSENT' }}
- **Balise H1** : {{ ($seo['technical_audit']['has_h1'] ?? false) ? '✅ PRÉSENT' : '❌ ABSENT' }} ({{ $seo['technical_audit']['h1_count'] ?? 0 }} trouvée(s))
- **Viewport mobile** : {{ ($seo['technical_audit']['has_viewport'] ?? false) ? '✅ PRÉSENT' : '❌ ABSENT' }}
- **Balise Canonical** : {{ ($seo['technical_audit']['has_canonical'] ?? false) ? '✅ PRÉSENT' : '❌ ABSENT' }}
- **Balise Robots** : {{ ($seo['technical_audit']['has_robots'] ?? false) ? '✅ PRÉSENT' : '❌ ABSENT' }}
- **Sitemap détecté** : {{ ($seo['technical_audit']['has_sitemap'] ?? false) ? '✅ PRÉSENT' : '❌ ABSENT' }}
- **Favicon** : {{ ($seo['technical_audit']['has_favicon'] ?? false) ? '✅ PRÉSENT' : '❌ ABSENT' }}
- **Open Graph** : {{ ($seo['technical_audit']['has_og_tags'] ?? false) ? '✅ PRÉSENT' : '❌ ABSENT' }}
- **Twitter Cards** : {{ ($seo['technical_audit']['has_twitter_cards'] ?? false) ? '✅ PRÉSENT' : '❌ ABSENT' }}
- **Schema.org** : {{ ($seo['technical_audit']['has_schema_org'] ?? false) ? '✅ PRÉSENT' : '❌ ABSENT' }}
- **Images sans alt** : {{ $seo['technical_audit']['images_with_missing_alt'] ?? 0 }}
@else
- ❌ Aucun audit technique disponible dans les données
@endif

🌐 **INFORMATIONS TECHNIQUES AVANCÉES** :
- **HTTPS** : {{ ($seo['https_enabled'] ?? false) ? '✅ ACTIVÉ' : '❌ NON ACTIVÉ' }}
- **Noindex détecté** : {{ ($seo['noindex_detected'] ?? false) ? '❌ OUI - Le site est en noindex!' : '✅ NON' }}
- **Données structurées** : {{ ($seo['has_structured_data'] ?? false) ? '✅ PRÉSENTES' : '❌ ABSENTES' }}
- **Mobile Friendly** : {{ ($seo['mobile'] ?? false) ? '✅ OUI' : '❌ NON' }}
- **Langue HTML** : {{ $seo['html_lang'] ?? 'Non spécifiée' }}
- **Temps de chargement** : {{ $seo['load_time'] ?? 'N/A' }} secondes
- **Taille HTML** : {{ $seo['html_size'] ?? 0 }} octets
- **Open Graph Tags** : {{ ($seo['has_og_tags'] ?? false) ? '✅ PRÉSENTS' : '❌ ABSENTS' }}
- **Favicon** : {{ ($seo['has_favicon'] ?? false) ? '✅ PRÉSENT' : '❌ ABSENT' }}

@if(!empty($perf) && is_array($perf))
🚀 **PERFORMANCE PAGE SPEED** :
@foreach($perf as $opportunity)
- **{{ $opportunity['title'] ?? 'Opportunité' }}** : {{ $opportunity['description'] ?? 'Non spécifié' }}
@endforeach
@else
🚀 **PERFORMANCE PAGE SPEED** : Aucune opportunité d'optimisation détectée
@endif

---

🎯 **TÂCHES DEMANDÉES** :

1. **SCORE SEO GLOBAL** : Donne un score réaliste sur 100 basé UNIQUEMENT sur les données réelles ci-dessus
2. **PROBLÈMES IDENTIFIÉS** : Liste les vrais problèmes SEO détectés (sois précis avec les données réelles)
3. **CHECKLIST ACTIONNABLE** : Propose des recommandations personnalisées pour améliorer ce site spécifique

⚠️ **IMPORTANT** : Base-toi EXCLUSIVEMENT sur les données fournies. 
- Si HTTPS est activé ({{ $seo['https_enabled'] ? 'OUI' : 'NON' }}), prends-le en compte
- Si un titre existe, ne dis pas qu'il est absent
- Si une meta description existe, ne dis pas qu'elle est manquante  
- Si des H1/H2/H3 sont présents, ne dis pas qu'ils sont absents
- Si l'audit technique montre des éléments présents, prends-les en compte
- Sois précis et factuel avec les données réelles du site

📝 **NOTE SPÉCIALE POUR L'IA** : 
Les données ci-dessus sont EXACTES et PROVENENT DIRECTEMENT du scraper. 
- HTTPS: {{ $seo['https_enabled'] ? 'ACTIVÉ' : 'NON ACTIVÉ' }}
- Titre: {{ !empty($seo['title']) ? 'PRÉSENT' : 'ABSENT' }} 
- Meta: {{ !empty($seo['meta_description']) ? 'PRÉSENTE' : 'ABSENTE' }}
- H1: {{ $seo['headings_structure']['summary']['by_level']['h1'] ?? 0 }} trouvé(s)
- Contenu: {{ $seo['word_count'] ?? 0 }} mots