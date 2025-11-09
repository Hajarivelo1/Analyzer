from flask import Flask, request, jsonify
import trafilatura
from textstat import flesch_reading_ease
from playwright.sync_api import sync_playwright
import re
import logging
import os

app = Flask(__name__)
logging.basicConfig(level=logging.INFO)

# 🔍 Extraction via navigateur headless avec simulation utilisateur
def fetch_with_playwright(url):
    with sync_playwright() as p:
        browser = p.chromium.launch(headless=True, args=["--window-size=1920,1080"])
        try:
            context = browser.new_context(user_agent="Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120 Safari/537.36")
            page = context.new_page()
            page.goto(url, timeout=30000)
            page.wait_for_load_state('domcontentloaded')
            page.wait_for_timeout(3000)  # Laisse le temps à Cloudflare
            page.mouse.move(100, 100)
            page.keyboard.press("PageDown")
            html = page.content()

            # 🗂️ Archive le HTML pour inspection
            os.makedirs("html_logs", exist_ok=True)
            with open("html_logs/last_scraped.html", "w", encoding="utf-8") as f:
                f.write(html)

        finally:
            browser.close()
        return html

# 🧠 Analyse sémantique des paragraphes
def analyze_paragraphs(text):
    raw_paragraphs = [p.strip() for p in re.split(r'\n{2,}|\r\n{2,}|(?<=[.!?])\s{2,}', text) if p.strip()]
    
    duplicates = []
    seen = set()
    for p in raw_paragraphs:
        key = re.sub(r'\W+', '', p.lower())
        if key in seen:
            duplicates.append(p)
        else:
            seen.add(key)

    short_count = sum(1 for p in raw_paragraphs if len(p.split()) < 40)

    return {
        "paragraphs": raw_paragraphs,
        "duplicate_count": len(duplicates),
        "short_count": short_count
    }

# 🚀 Endpoint principal
@app.route('/extract', methods=['POST'])
def extract():
    url = request.json.get('url')
    if not url:
        return jsonify({"error": "Missing URL"}), 400

    logging.info(f"[EXTRACT] Received URL: {url}")

    try:
        html = fetch_with_playwright(url)
    except Exception as e:
        logging.error(f"[EXTRACT] Playwright failed: {str(e)}")
        return jsonify({"error": f"Playwright failed: {str(e)}"}), 500

    if not html or "enable javascript and cookies" in html.lower():
        logging.warning(f"[EXTRACT] Cloudflare protection detected or empty HTML for {url}")
        return jsonify({"error": "Cloudflare protection detected or empty HTML"}), 403

    content = trafilatura.extract(html)
    if not content or len(content.strip()) < 100:
        logging.warning(f"[EXTRACT] No meaningful content extracted from {url}")
        return jsonify({"error": "No content extracted"}), 400

    readability = flesch_reading_ease(content)
    analysis = analyze_paragraphs(content)

    logging.info(f"[EXTRACT] Content length: {len(content)} | Readability: {readability:.2f} | Short: {analysis['short_count']} | Duplicates: {analysis['duplicate_count']}")

    return jsonify({
        "content": content,
        "readability": readability,
        "analysis": analysis
    })

# 🧱 Lancement du microservice
if __name__ == '__main__':
    app.run(port=5000)
