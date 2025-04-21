import sys
import json
import re
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import cosine_similarity

def tokenize(text):
    # split on non‑letters, lowercase
    return re.findall(r"[A-Za-z]+", text.lower())

def process_search(input_file, output_file):
    try:
        # 1. load input JSON
        with open(input_file, 'r', encoding='utf-8') as f:
            data = json.load(f)

        query     = data.get('query', '')
        specialty = data.get('specialty', '').lower()
        doctors   = data.get('doctors', [])

        # 2. filter by specialty if given
        if specialty:
            doctors = [
                d for d in doctors
                if d.get('specialite','').lower() == specialty
            ]

        # 3. build list of full names
        names = [f"{d.get('prenom','')} {d.get('nom','')}" for d in doctors]
        proc_names = [" ".join(tokenize(n)) for n in names]
        proc_query = " ".join(tokenize(query))

        results = []
        if proc_names:
            # 4. vectorize + compare
            vectorizer = TfidfVectorizer()
            doc_matrix = vectorizer.fit_transform(proc_names)
            query_vec  = vectorizer.transform([proc_query])
            scores     = cosine_similarity(query_vec, doc_matrix).flatten()

            # 5. collect matches
            for i, sc in enumerate(scores):
                if sc > 0.1:
                    d = doctors[i]
                    results.append({
                        "id":         d["id_utilisateur"],
                        "name":       f"{d['prenom']} {d['nom']}",
                        "specialty":  d["specialite"],
                        "address":    d["adresse"],
                        "experience": d["experience"],
                        "photo":      d["photo_profil"],
                        "score":      float(sc)
                    })

        # 6. write output JSON
        with open(output_file, 'w', encoding='utf-8') as f:
            json.dump(results, f, indent=2, ensure_ascii=False)

        return True

    except Exception as e:
        print(f"Error: {e}", file=sys.stderr)
        return False

if __name__ == "__main__":
    if len(sys.argv) != 3:
        print("Usage: python found_med.py <input.json> <output.json>")
        sys.exit(1)
    ok = process_search(sys.argv[1], sys.argv[2])
    sys.exit(0 if ok else 1)
