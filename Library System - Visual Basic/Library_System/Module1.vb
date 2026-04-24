Imports MySql.Data.MySqlClient
Imports System.Data

Module Module1
    Public connString As String = "server=localhost;user=root;password=;database=library_db"

    Public Function GetData(query As String) As DataTable
        Dim dt As New DataTable()
        Try
            Using conn As New MySqlConnection(connString)
                Using adapter As New MySqlDataAdapter(query, conn)
                    adapter.Fill(dt)
                End Using
            End Using
        Catch ex As Exception
            MsgBox("Database Error: " & ex.Message)
        End Try
        Return dt
    End Function


End Module