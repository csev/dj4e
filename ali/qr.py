#!/usr/bin/env python3
"""Generate a QR code for https://django.dr-chuck.com/"""

# python3 -m pip install "qrcode[pil]"

import qrcode


def main() -> None:
    url = "https://django.dr-chuck.com/"
    output_file = "django-dr-chuck-qr.png"

    qr = qrcode.QRCode(
        version=None,
        error_correction=qrcode.constants.ERROR_CORRECT_M,
        box_size=10,
        border=4,
    )

    qr.add_data(url)
    qr.make(fit=True)

    image = qr.make_image(fill_color="black", back_color="white")
    image.save(output_file)

    print(f"QR code saved as {output_file}")


if __name__ == "__main__":
    main()

