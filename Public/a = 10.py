import sympy

def evaluate_limit_approximation():
    print("โปรดป้อนสมการสำหรับลิมิตข้างซ้าย (ใช้ 'x' เป็นตัวแปร):")
    # Example: "x**2 + 1" or "1/x"
    eq_left_str = input("สมการข้างซ้าย: ")

    print("\nโปรดป้อนสมการสำหรับลิมิตข้างขวา (ใช้ 'x' เป็นตัวแปร):")
    # Example: "x**2 + 1" or "1/x"
    eq_right_str = input("สมการข้างขวา: ")

    try:
        limit_point_str = input("\nโปรดป้อนจุดลิมิต (เช่น 0, 1, -2): ")
        limit_point = float(limit_point_str)
    except ValueError:
        print("ข้อผิดพลาด: จุดลิมิตไม่ถูกต้อง โปรดป้อนตัวเลข")
        return

    # Define the symbolic variable 'x' for sympy
    x = sympy.Symbol('x')

    try:
        # Parse the input equations into sympy expressions
        eq_left = sympy.sympify(eq_left_str)
        eq_right = sympy.sympify(eq_right_str)
    except (sympy.SympifyError, SyntaxError) as e:
        print(f"ข้อผิดพลาดในการแยกวิเคราะห์สมการ: {e}")
        print("โปรดตรวจสอบรูปแบบสมการของคุณ (เช่น ใช้ x**2 สำหรับ x^2)")
        return

    # --- Approximation for Left-Hand Limit ---
    # Choose a small epsilon for approximation from the left
    epsilon_left = 1e-6
    point_left = limit_point - epsilon_left
    try:
        value_left = float(eq_left.subs(x, point_left).evalf())
    except Exception as e:
        print(f"\nไม่สามารถคำนวณค่าสำหรับลิมิตข้างซ้ายได้: {e}")
        value_left = "หาค่าไม่ได้"

    # --- Approximation for Right-Hand Limit ---
    # Choose a small epsilon for approximation from the right
    epsilon_right = 1e-6
    point_right = limit_point + epsilon_right
    try:
        value_right = float(eq_right.subs(x, point_right).evalf())
    except Exception as e:
        print(f"\nไม่สามารถคำนวณค่าสำหรับลิมิตข้างขวาได้: {e}")
        value_right = "หาค่าไม่ได้"

    print(f"\n----- ผลลัพธ์การคำนวณลิมิต -----")
    print(f"ลิมิตข้างซ้ายเมื่อ x เข้าใกล้ {limit_point} จากซ้าย (จากสมการ '{eq_left_str}'): {value_left}")
    print(f"ลิมิตข้างขวาเมื่อ x เข้าใกล้ {limit_point} จากขวา (จากสมการ '{eq_right_str}'): {value_right}")

    # Check if the limit exists based on approximation
    if isinstance(value_left, (float, int)) and isinstance(value_right, (float, int)):
        tolerance = 1e-4  # A small tolerance for floating-point comparisons
        if abs(value_left - value_right) < tolerance:
            print(f"ดังนั้น ลิมิตของฟังก์ชันที่จุด {limit_point} น่าจะมีอยู่ และมีค่าประมาณ: {value_left:.6f}")
        else:
            print(f"เนื่องจากลิมิตข้างซ้าย ({value_left:.6f}) ไม่เท่ากับลิมิตข้างขวา ({value_right:.6f})")
            print(f"ดังนั้น ลิมิตของฟังก์ชันที่จุด {limit_point} ไม่มีอยู่")
    else:
        print("ไม่สามารถสรุปการมีอยู่ของลิมิตได้ เนื่องจากมีค่าที่ไม่สามารถหาได้")

# Run the function
if __name__ == "__main__":
    evaluate_limit_approximation()