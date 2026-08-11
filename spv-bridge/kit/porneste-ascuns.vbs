' Porneste o comanda fara fereastra.
'
' Sarcinile programate ruleaza php.exe de-a dreptul, iar Windows ii da consola:
' la fiecare intrare in cont se deschideau cate o fereastra de fiecare instanta,
' cu nimic in ele in afara randurilor serverului de web. Se incarcau bara de
' sarcini si se inchideau din greseala - iar odata inchise, se opreau si
' descarcarile, si dosarul urmarit.
'
' Nu exista alt fel de a ascunde consola unei sarcini programate: "hidden" din
' Task Scheduler ascunde sarcina din lista, nu fereastra programului. Wscript e
' singurul care poate porni ceva cu fereastra inchisa (parametrul 0 de mai jos).
'
' Fisierul acesta se scrie numai cu semne obisnuite, si fara BOM. Motorul
' VBScript se opreste din primul caracter daca gaseste unul - "Invalid
' character (1, 1)" -, sarcina iese cu codul 1 si nu porneste nimic, iar in
' jurnal scrie doar ca nu asculta nimeni pe port. Vezi KitBridge, unde .vbs sta
' langa .bat.
Option Explicit

Dim comanda, i

If WScript.Arguments.Count = 0 Then
    WScript.Quit 1
End If

' Fiecare argument se pune iar intre ghilimele: caile au spatii in ele
' ("Program Files", numele omului), iar fara ele s-ar rupe in bucati.
comanda = """" & WScript.Arguments(0) & """"

For i = 1 To WScript.Arguments.Count - 1
    comanda = comanda & " """ & WScript.Arguments(i) & """"
Next

' 0 = fara fereastra; False = nu se asteapta dupa el.
CreateObject("WScript.Shell").Run comanda, 0, False
