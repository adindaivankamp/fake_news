import re

def clean_text_light(text: str) -> str:
    if not text:
        return ""
    
    text = text.lower()
    text = text.encode("ascii", "ignore").decode()
    text = re.sub(r"[^\w\s]", " ", text)
    text = re.sub(r"\s+", " ", text).strip()

    return text