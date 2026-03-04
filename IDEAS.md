# Future Ideas

Potential additions for future consideration.

## Text Metrics

- `wordCount()` - count words in text
- `sentenceCount()` - count sentences in text

## Grapheme-Aware Operations

- `truncate(int $length, string $ellipsis = '...')` - truncate preserving grapheme clusters
- `substring(int $start, ?int $length = null)` - grapheme-aware substring

## Script Analysis

- `dominantScript()` - returns the primary script in mixed-script text
- `scriptBreakdown()` - returns percentage breakdown per script
- `scriptCount()` - count of distinct scripts in text
