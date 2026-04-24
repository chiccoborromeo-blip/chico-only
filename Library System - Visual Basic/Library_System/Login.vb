Imports MySql.Data.MySqlClient
Imports System.Security.Cryptography
Imports System.Text

Public Class frmLogin

    Private Sub frmLogin_Load(sender As Object, e As EventArgs) Handles MyBase.Load
        txtEmail.Text = "Email"
        txtEmail.ForeColor = Color.DarkGray

        txtPassword.Text = "Password"
        txtPassword.ForeColor = Color.DarkGray
        txtPassword.PasswordChar = ControlChars.NullChar

        pbShowpassword.Visible = False
        pbHidepassword.Visible = False
    End Sub

    ' ══════════════════════════════════════════
    ' EMAIL PLACEHOLDER
    ' ══════════════════════════════════════════
    Private Sub txtEmail_GotFocus(sender As Object, e As EventArgs) Handles txtEmail.GotFocus
        If txtEmail.Text = "Email" Then
            txtEmail.Text = ""
            txtEmail.ForeColor = Color.Black
        End If
    End Sub

    Private Sub txtEmail_LostFocus(sender As Object, e As EventArgs) Handles txtEmail.LostFocus
        If txtEmail.Text = "" Then
            txtEmail.Text = "Email"
            txtEmail.ForeColor = Color.DarkGray
        End If
    End Sub

    ' ══════════════════════════════════════════
    ' PASSWORD PLACEHOLDER
    ' ══════════════════════════════════════════
    Private Sub txtPassword_GotFocus(sender As Object, e As EventArgs) Handles txtPassword.GotFocus
        If txtPassword.Text = "Password" Then
            txtPassword.Text = ""
            txtPassword.PasswordChar = "●"c
            txtPassword.ForeColor = Color.Black
        End If
        UpdateEyeVisibility()
    End Sub

    Private Sub txtPassword_LostFocus(sender As Object, e As EventArgs) Handles txtPassword.LostFocus
        If txtPassword.Text = "" Then
            txtPassword.Text = "Password"
            txtPassword.PasswordChar = ControlChars.NullChar
            txtPassword.ForeColor = Color.DarkGray
        End If
        pbShowpassword.Visible = False
        pbHidepassword.Visible = False
    End Sub

    Private Sub txtPassword_TextChanged(sender As Object, e As EventArgs) Handles txtPassword.TextChanged
        UpdateEyeVisibility()
    End Sub

    ' ══════════════════════════════════════════
    ' SHOW / HIDE PASSWORD
    ' ══════════════════════════════════════════
    Private Sub pbShowpassword_Click(sender As Object, e As EventArgs) Handles pbShowpassword.Click
        txtPassword.PasswordChar = ControlChars.NullChar
        pbShowpassword.Visible = False
        pbHidepassword.Visible = True
    End Sub

    Private Sub pbHidepassword_Click(sender As Object, e As EventArgs) Handles pbHidepassword.Click
        txtPassword.PasswordChar = "●"c
        pbHidepassword.Visible = False
        pbShowpassword.Visible = True
    End Sub

    Private Sub UpdateEyeVisibility()
        If txtPassword.Focused AndAlso txtPassword.Text.Length > 0 Then
            If txtPassword.PasswordChar = ControlChars.NullChar Then
                pbHidepassword.Visible = True
                pbShowpassword.Visible = False
            Else
                pbShowpassword.Visible = True
                pbHidepassword.Visible = False
            End If
        Else
            pbShowpassword.Visible = False
            pbHidepassword.Visible = False
        End If
    End Sub

    ' ══════════════════════════════════════════
    ' HASH PASSWORD
    ' ══════════════════════════════════════════
    Private Function HashPassword(password As String) As String
        Using sha256 As SHA256 = SHA256.Create()
            Dim bytes As Byte() = Encoding.UTF8.GetBytes(password)
            Dim hash As Byte() = sha256.ComputeHash(bytes)
            Dim builder As New StringBuilder()
            For Each b As Byte In hash
                builder.Append(b.ToString("x2"))
            Next
            Return builder.ToString()
        End Using
    End Function

    ' ══════════════════════════════════════════
    ' LOGIN
    ' ══════════════════════════════════════════
    Private Sub btnLogin_Click(sender As Object, e As EventArgs) Handles btnLogin.Click
        Dim email As String = txtEmail.Text.Trim()
        Dim password As String = txtPassword.Text.Trim()

        If email = "" OrElse email = "Email" OrElse
           password = "" OrElse password = "Password" Then
            MessageBox.Show("Please enter your email and password.",
                            "Warning", MessageBoxButtons.OK, MessageBoxIcon.Warning)
            Exit Sub
        End If

        Try
            Using conn As New MySqlConnection("server=localhost;userid=root;password=;database=library_db")
                conn.Open()

                Dim cmd As New MySqlCommand("
                    SELECT id, name, role, password
                    FROM users
                    WHERE email = @email", conn)
                cmd.Parameters.AddWithValue("@email", email)

                Using dr As MySqlDataReader = cmd.ExecuteReader()

                    If Not dr.Read() Then
                        MessageBox.Show("Email not found.",
                                        "Login Failed", MessageBoxButtons.OK, MessageBoxIcon.Warning)
                        Exit Sub
                    End If

                    Dim dbPassword As String = dr("password").ToString()
                    Dim inputPassword As String = HashPassword(password)

                    If dbPassword <> inputPassword Then
                        MessageBox.Show("Incorrect password.",
                                        "Login Failed", MessageBoxButtons.OK, MessageBoxIcon.Warning)
                        Exit Sub
                    End If

                    Dim userId As Integer = Convert.ToInt32(dr("id"))
                    Dim userName As String = dr("name").ToString()
                    Dim userRole As String = dr("role").ToString()

                    dr.Close()

                    MessageBox.Show("Welcome, " & userName & "!",
                                    "Login Successful", MessageBoxButtons.OK, MessageBoxIcon.Information)

                    If userRole.ToLower() = "admin" Then
                        Dim f As New frmAdmindashboard()
                        f.Show()
                    Else
                        Dim f As New userdashboard()
                        f.loggedInUserId = userId
                        f.Show()
                    End If

                    Me.Hide()

                End Using
            End Using

        Catch ex As MySqlException
            MessageBox.Show("Database error: " & ex.Message, "Error",
                            MessageBoxButtons.OK, MessageBoxIcon.Error)
        Catch ex As Exception
            MessageBox.Show("Error: " & ex.Message, "Error",
                            MessageBoxButtons.OK, MessageBoxIcon.Error)
        End Try
    End Sub

    ' ══════════════════════════════════════════
    ' ENTER KEY = LOGIN
    ' ══════════════════════════════════════════
    Private Sub txtPassword_KeyDown(sender As Object, e As KeyEventArgs) Handles txtPassword.KeyDown
        If e.KeyCode = Keys.Enter Then
            btnLogin.PerformClick()
        End If
    End Sub

End Class