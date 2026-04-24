Imports MySql.Data.MySqlClient
Imports System.Security.Cryptography
Imports System.Text
Public Class frmRegister
    Private Sub register_load(sender As Object, e As EventArgs) Handles MyBase.Load
        txtFullname.Text = "Enter your full name"
        txtEmail.ForeColor = Color.DarkGray

        txtEmail.Text = "Enter your email"
        txtEmail.ForeColor = Color.DarkGray

        txtPassword.Text = "Password"
        txtPassword.ForeColor = Color.DarkGray
    End Sub

    Private Sub txtFullname_GotFocus(sender As Object, e As EventArgs) Handles txtFullname.GotFocus
        If txtFullname.Text = "Enter your full name" Then
            txtFullname.Text = ""
            txtFullname.ForeColor = Color.Black
        End If
    End Sub

    Private Sub txtEmail_GotFocus(sender As Object, e As EventArgs) Handles txtEmail.GotFocus
        If txtEmail.Text = "Enter your email" Then
            txtEmail.Text = ""
            txtEmail.ForeColor = Color.Black
        End If
    End Sub

    Private Sub txtFullname_LostFocus(sender As Object, e As EventArgs) Handles txtFullname.LostFocus
        If txtFullname.Text = "" Then
            txtFullname.Text = "Enter your full name"
            txtFullname.ForeColor = Color.DarkGray
        End If
    End Sub

    Private Sub txtUsername_LostFocus(sender As Object, e As EventArgs) Handles txtEmail.LostFocus
        If txtEmail.Text = "" Then
            txtEmail.Text = "Enter your email"
            txtEmail.ForeColor = Color.DarkGray
        End If
    End Sub

    Private Sub txtPassword_GotFocus(sender As Object, e As EventArgs) Handles txtPassword.GotFocus
        If txtPassword.Text = "Password" Then
            txtPassword.Text = ""
            txtPassword.PasswordChar = "●"
            txtPassword.ForeColor = Color.Black
        End If
    End Sub

    Private Sub txtPassword_LostFocus(sender As Object, e As EventArgs) Handles txtPassword.LostFocus
        If txtPassword.Text = "" Then
            txtPassword.Text = "Password"
            txtPassword.PasswordChar = ""
            txtPassword.ForeColor = Color.DarkGray
        End If
    End Sub


    Private Sub Form1_Load(sender As Object, e As EventArgs) Handles MyBase.Load
        txtPassword.PasswordChar = "●"c
        pbShowpassword.Visible = False
        pbHidepassword.Visible = False
    End Sub

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

    Private Sub txtPassword_TextChanged(sender As Object, e As EventArgs) Handles txtPassword.TextChanged
        UpdateEyeVisibility()
    End Sub

    Private Sub txtPassword_KeyDown(sender As Object, e As KeyEventArgs) Handles txtPassword.KeyDown
        If e.KeyCode = Keys.Enter Then
            btnRegister.PerformClick()
            e.SuppressKeyPress = True
        End If
    End Sub

    Private Sub txtPassword_Enter(sender As Object, e As EventArgs) Handles txtPassword.Enter
        UpdateEyeVisibility()
    End Sub

    Private Sub txtPassword_Leave(sender As Object, e As EventArgs) Handles txtPassword.Leave
        pbShowpassword.Visible = False
        pbHidepassword.Visible = False
    End Sub

    Private Sub UpdateEyeVisibility()
        If txtPassword.Focused AndAlso txtPassword.Text.Length > 0 Then

            ' Show correct icon based on current state
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



    ' ✅ Load roles when the form opens



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



    Private Sub btnRegister_Click(sender As Object, e As EventArgs) Handles btnRegister.Click
        Dim name As String = txtFullname.Text.Trim()
        Dim email As String = txtEmail.Text.Trim()
        Dim rawPassword As String = txtPassword.Text.Trim()

        ' Validation
        If name = "" Or email = "" Or rawPassword = "" Then
            MsgBox("Please fill in all fields.", MsgBoxStyle.Exclamation, "Warning")
            Exit Sub
        End If

        Dim password As String = HashPassword(rawPassword)

        Dim con As New MySqlConnection("server=localhost;userid=root;password=;database=library_db")
        Try
            con.Open()

            ' Check duplicate email
            Dim checkCmd As New MySqlCommand("SELECT COUNT(*) FROM users WHERE email=@email", con)
            checkCmd.Parameters.AddWithValue("@email", email)
            Dim exists As Integer = Convert.ToInt32(checkCmd.ExecuteScalar())
            If exists > 0 Then
                MsgBox("Email already exists.", MsgBoxStyle.Exclamation)
                Exit Sub
            End If

            ' INSERT USER with is_approved = 0 (pending)
            Dim cmd As New MySqlCommand("
            INSERT INTO users (name, email, password, role, is_approved)
            VALUES (@name, @email, @password, 'user', 0)", con)
            cmd.Parameters.AddWithValue("@name", name)
            cmd.Parameters.AddWithValue("@email", email)
            cmd.Parameters.AddWithValue("@password", password)
            cmd.ExecuteNonQuery()
            con.Close()

            txtFullname.Clear()
            txtEmail.Clear()
            txtPassword.Clear()

            ' Approval pending message
            MessageBox.Show(
    "Success! You're all set." & Environment.NewLine & Environment.NewLine &
    "",
    "Registration Complete",
    MessageBoxButtons.OK,
    MessageBoxIcon.Information)

        Catch ex As Exception
            MsgBox("Error: " & ex.Message)
        End Try

        Me.Close()
    End Sub
    Private Sub llLogin_LinkClicked(sender As Object, e As LinkLabelLinkClickedEventArgs)
        frmLogin.Show()
        Me.Hide()
    End Sub


End Class